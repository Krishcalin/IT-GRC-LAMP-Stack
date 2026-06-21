<?php

namespace App\Http\Controllers;

use App\Models\Objective;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;

class ObjectiveController extends Controller
{
    private array $rules = [
        'title' => 'required|string|max:256',
        'description' => 'nullable|string',
        'measure' => 'nullable|string',
        'target_value' => 'nullable|string|max:128',
        'current_value' => 'nullable|string|max:128',
        'unit' => 'nullable|string|max:32',
        'status' => 'required|in:Not Started,On Track,At Risk,Achieved,Missed',
        'owner_id' => 'nullable|uuid|exists:users,id',
        'due_date' => 'nullable|date',
        'review_date' => 'nullable|date',
    ];

    public function index(Request $r)
    {
        $q = Objective::with('owner', 'metrics')->orderBy('ref_id');
        if ($r->filled('status')) {
            $q->where('status', $r->input('status'));
        }
        if ($r->filled('search')) {
            $q->where('title', 'like', '%'.$r->input('search').'%');
        }
        $items = $q->paginate(40)->withQueryString();

        return view('objectives.index', ['items' => $items, 'filters' => $r->only('status', 'search')]);
    }

    public function create()
    {
        return view('objectives.form', ['item' => new Objective(['status' => 'Not Started']), 'users' => $this->users()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $data['ref_id'] = Refs::next(Objective::class, 'OBJ');
        $m = Objective::create($data);
        Activity::log('CREATE', 'objective', $m->id);

        return redirect()->route('objectives.index')->with('status', 'Objective created.');
    }

    public function edit(Objective $objective)
    {
        return view('objectives.form', ['item' => $objective, 'users' => $this->users()]);
    }

    public function update(Request $r, Objective $objective)
    {
        $objective->update($r->validate($this->rules));
        Activity::log('UPDATE', 'objective', $objective->id);

        return redirect()->route('objectives.index')->with('status', 'Objective updated.');
    }

    public function destroy(Objective $objective)
    {
        Activity::log('DELETE', 'objective', $objective->id);
        $objective->delete();

        return redirect()->route('objectives.index')->with('status', 'Objective deleted.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }
}
