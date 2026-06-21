<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Risk;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;

class IncidentController extends Controller
{
    private array $rules = [
        'title' => 'required|string|max:256',
        'description' => 'nullable|string',
        'category' => 'required|string|max:48',
        'severity' => 'required|in:Low,Medium,High,Critical',
        'status' => 'required|in:New,Triaged,In Progress,Resolved,Closed',
        'reporter' => 'nullable|string|max:128',
        'owner_id' => 'nullable|uuid|exists:users,id',
        'risk_id' => 'nullable|uuid|exists:risks,id',
        'affected_assets' => 'nullable|string',
        'data_breach' => 'boolean',
        'containment_actions' => 'nullable|string',
        'root_cause' => 'nullable|string',
        'lessons_learned' => 'nullable|string',
        'evidence_notes' => 'nullable|string',
    ];

    public function index(Request $r)
    {
        $q = Incident::with('owner')->orderByDesc('ref_id');
        foreach (['category', 'severity', 'status'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        if ($r->filled('search')) {
            $q->where('title', 'like', '%'.$r->input('search').'%');
        }
        $items = $q->paginate(40)->withQueryString();

        return view('incidents.index', ['items' => $items, 'filters' => $r->only('category', 'severity', 'status', 'search')]);
    }

    public function create()
    {
        return view('incidents.form', ['item' => new Incident(['severity' => 'Medium', 'status' => 'New', 'category' => 'Other']), 'users' => $this->users(), 'risks' => $this->risks()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $data['ref_id'] = Refs::next(Incident::class, 'INC');
        $data['reported_at'] = now();
        $m = Incident::create($data);
        Activity::log('CREATE', 'incident', $m->id);

        return redirect()->route('incidents.index')->with('status', 'Incident logged.');
    }

    public function edit(Incident $incident)
    {
        return view('incidents.form', ['item' => $incident, 'users' => $this->users(), 'risks' => $this->risks()]);
    }

    public function update(Request $r, Incident $incident)
    {
        $data = $r->validate($this->rules);
        if (in_array($data['status'], ['Resolved', 'Closed'], true) && ! $incident->resolved_at) {
            $data['resolved_at'] = now();
        }
        $incident->update($data);
        Activity::log('UPDATE', 'incident', $incident->id);

        return redirect()->route('incidents.index')->with('status', 'Incident updated.');
    }

    public function destroy(Incident $incident)
    {
        Activity::log('DELETE', 'incident', $incident->id);
        $incident->delete();

        return redirect()->route('incidents.index')->with('status', 'Incident deleted.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }

    private function risks()
    {
        return Risk::orderBy('ref_id')->get(['id', 'ref_id', 'title']);
    }
}
