<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use App\Models\PolicyAcknowledgment;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PolicyController extends Controller
{
    private array $rules = [
        'title' => 'required|string|max:256',
        'description' => 'nullable|string',
        'version' => 'nullable|string|max:16',
        'status' => 'required|in:Draft,Under Review,Approved,Retired',
        'category' => 'required|string|max:64',
        'owner_id' => 'nullable|uuid|exists:users,id',
        'effective_date' => 'nullable|date',
        'review_date' => 'nullable|date',
        'next_review_date' => 'nullable|date',
        'content' => 'nullable|string',
    ];

    public function index(Request $r)
    {
        $q = Policy::with('owner')->orderBy('ref_id');
        foreach (['status', 'category'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        $items = $q->paginate(40)->withQueryString();

        return view('policies.index', ['items' => $items, 'filters' => $r->only('status', 'category')]);
    }

    public function create()
    {
        return view('policies.form', ['item' => new Policy(['status' => 'Draft', 'version' => '1.0']), 'users' => $this->users()]);
    }

    public function store(Request $r)
    {
        $data = $this->prepare($r->validate($this->rules), null);
        $data['ref_id'] = Refs::next(Policy::class, 'POL');
        $p = Policy::create($data);
        Activity::log('CREATE', 'policy', $p->id);

        return redirect()->route('policies.show', $p)->with('status', 'Policy created.');
    }

    public function show(Policy $policy)
    {
        $policy->load('owner', 'approver', 'acknowledgments.user');
        $acknowledged = $policy->acknowledgments->contains('user_id', Auth::id());

        return view('policies.show', compact('policy', 'acknowledged'));
    }

    public function edit(Policy $policy)
    {
        return view('policies.form', ['item' => $policy, 'users' => $this->users()]);
    }

    public function update(Request $r, Policy $policy)
    {
        $policy->update($this->prepare($r->validate($this->rules), $policy));
        Activity::log('UPDATE', 'policy', $policy->id);

        return redirect()->route('policies.show', $policy)->with('status', 'Policy updated.');
    }

    public function destroy(Policy $policy)
    {
        Activity::log('DELETE', 'policy', $policy->id);
        $policy->delete();

        return redirect()->route('policies.index')->with('status', 'Policy deleted.');
    }

    public function acknowledge(Policy $policy)
    {
        PolicyAcknowledgment::firstOrCreate(
            ['policy_id' => $policy->id, 'user_id' => Auth::id()],
            ['acknowledged_at' => now()]
        );

        return back()->with('status', 'Policy acknowledged.');
    }

    /** Stamp approver/approved_at when a policy first becomes Approved. */
    private function prepare(array $data, ?Policy $policy): array
    {
        $wasApproved = $policy && $policy->status === 'Approved';
        if (($data['status'] ?? null) === 'Approved' && ! $wasApproved) {
            $data['approved_by'] = Auth::id();
            $data['approved_at'] = now();
        }

        return $data;
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }
}
