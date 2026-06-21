<?php

namespace App\Http\Controllers;

use App\Models\Control;
use App\Models\ControlMapping;
use App\Models\User;
use App\Support\Activity;
use Illuminate\Http\Request;

class ControlController extends Controller
{
    public function index(Request $r)
    {
        $q = Control::with('owner')->orderBy('framework')->orderBy('clause');
        foreach (['framework', 'theme', 'status'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        if ($r->filled('search')) {
            $s = $r->input('search');
            $q->where(fn ($w) => $w->where('clause', 'like', "%$s%")->orWhere('title', 'like', "%$s%"));
        }
        $controls = $q->paginate(40)->withQueryString();
        $frameworks = Control::query()->select('framework')->distinct()->orderBy('framework')->pluck('framework');

        return view('controls.index', [
            'controls' => $controls,
            'frameworks' => $frameworks,
            'filters' => $r->only('framework', 'theme', 'status', 'search'),
        ]);
    }

    public function show(Control $control)
    {
        $control->load(['owner', 'soaEntry.responsible', 'risks', 'mappingsFrom.targetControl', 'mappingsTo.sourceControl']);
        $cross = collect();
        foreach ($control->mappingsFrom as $m) {
            $cross->push(['ctrl' => $m->targetControl, 'rel' => $m->relationship_type, 'dir' => '→', 'id' => $m->id]);
        }
        foreach ($control->mappingsTo as $m) {
            $cross->push(['ctrl' => $m->sourceControl, 'rel' => $m->relationship_type, 'dir' => '←', 'id' => $m->id]);
        }
        $users = User::orderBy('full_name')->get(['id', 'full_name']);
        $allControls = Control::orderBy('framework')->orderBy('clause')->get(['id', 'clause', 'title', 'framework']);

        return view('controls.show', compact('control', 'cross', 'users', 'allControls'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'clause' => 'required|string|max:16|unique:controls,clause',
            'title' => 'required|string|max:256',
            'description' => 'required|string',
            'framework' => 'required|string|max:48',
            'theme' => 'required|string|max:32',
            'implementation_guidance' => 'nullable|string',
        ]);
        $control = Control::create($data);
        Activity::log('CREATE', 'control', $control->id);

        return redirect()->route('controls.show', $control)->with('status', 'Control created.');
    }

    public function update(Request $r, Control $control)
    {
        $data = $r->validate([
            'title' => 'required|string|max:256',
            'description' => 'required|string',
            'theme' => 'required|string|max:32',
            'implementation_guidance' => 'nullable|string',
            'status' => 'required|in:Not Started,In Progress,Implemented,Not Applicable',
            'owner_id' => 'nullable|uuid|exists:users,id',
            'review_date' => 'nullable|date',
        ]);
        $control->update($data);
        Activity::log('UPDATE', 'control', $control->id);

        return back()->with('status', 'Control updated.');
    }

    public function destroy(Control $control)
    {
        Activity::log('DELETE', 'control', $control->id);
        $control->delete();

        return redirect()->route('controls.index')->with('status', 'Control deleted.');
    }

    public function addMapping(Request $r, Control $control)
    {
        $data = $r->validate([
            'target_control_id' => 'required|uuid|exists:controls,id',
            'relationship_type' => 'required|in:equivalent,related,broader,narrower',
            'note' => 'nullable|string',
        ]);
        if ($data['target_control_id'] !== $control->id) {
            ControlMapping::firstOrCreate(
                ['source_control_id' => $control->id, 'target_control_id' => $data['target_control_id']],
                ['relationship_type' => $data['relationship_type'], 'note' => $data['note'] ?? null]
            );
            Activity::log('MAP', 'control', $control->id);
        }

        return back()->with('status', 'Mapping added.');
    }

    public function deleteMapping(Control $control, ControlMapping $mapping)
    {
        $mapping->delete();

        return back()->with('status', 'Mapping removed.');
    }
}
