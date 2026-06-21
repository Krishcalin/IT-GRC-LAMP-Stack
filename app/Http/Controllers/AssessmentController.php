<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentItem;
use App\Models\Control;
use App\Models\Supplier;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    private array $rules = [
        'title' => 'required|string|max:256',
        'description' => 'nullable|string',
        'assessment_type' => 'required|in:Control Self-Assessment,Maturity Assessment,Vendor Questionnaire',
        'framework' => 'nullable|string|max:48',
        'supplier_id' => 'nullable|uuid|exists:suppliers,id',
        'status' => 'required|in:Draft,In Progress,Submitted,Reviewed,Closed',
        'due_date' => 'nullable|date',
    ];

    public function index(Request $r)
    {
        $q = Assessment::with('owner', 'supplier', 'items')->orderBy('ref_id');
        foreach (['assessment_type', 'status'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        if ($r->filled('search')) {
            $q->where('title', 'like', '%'.$r->input('search').'%');
        }
        $items = $q->paginate(40)->withQueryString();

        return view('assessments.index', ['items' => $items, 'filters' => $r->only('assessment_type', 'status', 'search')]);
    }

    public function create()
    {
        return view('assessments.form', ['item' => new Assessment(['assessment_type' => 'Control Self-Assessment', 'status' => 'Draft']), 'users' => $this->users(), 'suppliers' => $this->suppliers()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $data['ref_id'] = Refs::next(Assessment::class, 'ASMT');
        $data['owner_id'] = Auth::id();
        $a = Assessment::create($data);
        Activity::log('CREATE', 'assessment', $a->id);

        return redirect()->route('assessments.show', $a)->with('status', 'Assessment created.');
    }

    public function show(Assessment $assessment)
    {
        $assessment->load('owner', 'supplier', 'items.control');
        $frameworks = Control::query()->select('framework')->distinct()->orderBy('framework')->pluck('framework');

        return view('assessments.show', compact('assessment', 'frameworks'));
    }

    public function edit(Assessment $assessment)
    {
        return view('assessments.form', ['item' => $assessment, 'users' => $this->users(), 'suppliers' => $this->suppliers()]);
    }

    public function update(Request $r, Assessment $assessment)
    {
        $assessment->update($r->validate($this->rules));
        Activity::log('UPDATE', 'assessment', $assessment->id);

        return redirect()->route('assessments.show', $assessment)->with('status', 'Assessment updated.');
    }

    public function destroy(Assessment $assessment)
    {
        Activity::log('DELETE', 'assessment', $assessment->id);
        $assessment->delete();

        return redirect()->route('assessments.index')->with('status', 'Assessment deleted.');
    }

    public function populate(Request $r, Assessment $assessment)
    {
        $data = $r->validate(['framework' => 'required|string|max:48']);
        $existing = $assessment->items()->whereNotNull('control_id')->pluck('control_id')->all();
        $controls = Control::where('framework', $data['framework'])->whereNotIn('id', $existing)->orderBy('clause')->get();
        $n = 0;
        foreach ($controls as $c) {
            $assessment->items()->create([
                'ref_id' => Refs::next(AssessmentItem::class, 'ASI'),
                'control_id' => $c->id,
                'question' => "{$c->clause} — {$c->title}",
            ]);
            $n++;
        }

        return back()->with('status', "Added $n item(s) from {$data['framework']}.");
    }

    public function storeItem(Request $r, Assessment $assessment)
    {
        $data = $r->validate([
            'question' => 'nullable|string',
            'control_id' => 'nullable|uuid|exists:controls,id',
            'maturity' => 'nullable|integer|min:0|max:5',
            'result' => 'nullable|in:Compliant,Partial,Non-Compliant,N/A,Yes,No',
            'response' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);
        $data['ref_id'] = Refs::next(AssessmentItem::class, 'ASI');
        $assessment->items()->create($data);

        return back()->with('status', 'Item added.');
    }

    public function updateItem(Request $r, Assessment $assessment, AssessmentItem $item)
    {
        $data = $r->validate([
            'maturity' => 'nullable|integer|min:0|max:5',
            'result' => 'nullable|in:Compliant,Partial,Non-Compliant,N/A,Yes,No',
            'response' => 'nullable|string',
            'comment' => 'nullable|string',
        ]);
        $item->update($data);

        return back()->with('status', 'Item updated.');
    }

    public function destroyItem(Assessment $assessment, AssessmentItem $item)
    {
        $item->delete();

        return back()->with('status', 'Item removed.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }

    private function suppliers()
    {
        return Supplier::orderBy('name')->get(['id', 'name']);
    }
}
