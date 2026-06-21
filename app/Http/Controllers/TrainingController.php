<?php

namespace App\Http\Controllers;

use App\Models\TrainingCampaign;
use App\Models\TrainingRecord;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    private array $rules = [
        'title' => 'required|string|max:256',
        'description' => 'nullable|string',
        'training_type' => 'required|in:Awareness Campaign,Onboarding,Role-based Training,Phishing Simulation,Policy Acknowledgment,Other',
        'topic' => 'nullable|string|max:128',
        'status' => 'required|in:Planned,In Progress,Completed,Cancelled',
        'audience' => 'nullable|string|max:256',
        'materials_link' => 'nullable|string|max:512',
        'owner_id' => 'nullable|uuid|exists:users,id',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
    ];

    public function index(Request $r)
    {
        $q = TrainingCampaign::with('owner', 'records')->orderBy('ref_id');
        foreach (['training_type', 'status'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        if ($r->filled('search')) {
            $q->where('title', 'like', '%'.$r->input('search').'%');
        }
        $items = $q->paginate(40)->withQueryString();

        return view('training.index', ['items' => $items, 'filters' => $r->only('training_type', 'status', 'search')]);
    }

    public function create()
    {
        return view('training.form', ['item' => new TrainingCampaign(['training_type' => 'Awareness Campaign', 'status' => 'Planned']), 'users' => $this->users()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $data['ref_id'] = Refs::next(TrainingCampaign::class, 'TRN');
        $c = TrainingCampaign::create($data);
        Activity::log('CREATE', 'training_campaign', $c->id);

        return redirect()->route('training.show', $c)->with('status', 'Campaign created.');
    }

    public function show(TrainingCampaign $training)
    {
        $training->load('owner', 'records');

        return view('training.show', ['campaign' => $training]);
    }

    public function edit(TrainingCampaign $training)
    {
        return view('training.form', ['item' => $training, 'users' => $this->users()]);
    }

    public function update(Request $r, TrainingCampaign $training)
    {
        $training->update($r->validate($this->rules));
        Activity::log('UPDATE', 'training_campaign', $training->id);

        return redirect()->route('training.show', $training)->with('status', 'Campaign updated.');
    }

    public function destroy(TrainingCampaign $training)
    {
        Activity::log('DELETE', 'training_campaign', $training->id);
        $training->delete();

        return redirect()->route('training.index')->with('status', 'Campaign deleted.');
    }

    public function storeRecord(Request $r, TrainingCampaign $training)
    {
        $data = $r->validate([
            'participant' => 'required|string|max:128',
            'status' => 'required|in:Assigned,Completed,Overdue,Exempt',
            'score' => 'nullable|numeric',
            'completed_at' => 'nullable|date',
        ]);
        $data['ref_id'] = Refs::next(TrainingRecord::class, 'TRR');
        $training->records()->create($data);

        return back()->with('status', 'Participant added.');
    }

    public function updateRecord(Request $r, TrainingCampaign $training, TrainingRecord $record)
    {
        $data = $r->validate([
            'status' => 'required|in:Assigned,Completed,Overdue,Exempt',
            'score' => 'nullable|numeric',
            'completed_at' => 'nullable|date',
        ]);
        $record->update($data);

        return back()->with('status', 'Record updated.');
    }

    public function destroyRecord(TrainingCampaign $training, TrainingRecord $record)
    {
        $record->delete();

        return back()->with('status', 'Record removed.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }
}
