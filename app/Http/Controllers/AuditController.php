<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\AuditFinding;
use App\Models\Control;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    private array $rules = [
        'title' => 'required|string|max:256',
        'description' => 'nullable|string',
        'audit_type' => 'required|in:Internal,External,Surveillance',
        'status' => 'required|in:Planned,In Progress,Completed,Cancelled',
        'lead_auditor_id' => 'nullable|uuid|exists:users,id',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date',
        'scope' => 'nullable|string',
        'conclusion' => 'nullable|string',
    ];

    public function index(Request $r)
    {
        $q = Audit::with('leadAuditor', 'findings')->orderByDesc('ref_id');
        foreach (['status', 'audit_type'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        $items = $q->paginate(40)->withQueryString();

        return view('audits.index', ['items' => $items, 'filters' => $r->only('status', 'audit_type')]);
    }

    public function create()
    {
        return view('audits.form', ['item' => new Audit(['audit_type' => 'Internal', 'status' => 'Planned']), 'users' => $this->users()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $data['ref_id'] = Refs::next(Audit::class, 'AUDIT');
        $a = Audit::create($data);
        Activity::log('CREATE', 'audit', $a->id);

        return redirect()->route('audits.show', $a)->with('status', 'Audit created.');
    }

    public function show(Audit $audit)
    {
        $audit->load('leadAuditor', 'findings.control', 'findings.assignee');
        $users = $this->users();
        $controls = Control::orderBy('clause')->get(['id', 'clause', 'title']);

        return view('audits.show', compact('audit', 'users', 'controls'));
    }

    public function edit(Audit $audit)
    {
        return view('audits.form', ['item' => $audit, 'users' => $this->users()]);
    }

    public function update(Request $r, Audit $audit)
    {
        $audit->update($r->validate($this->rules));
        Activity::log('UPDATE', 'audit', $audit->id);

        return redirect()->route('audits.show', $audit)->with('status', 'Audit updated.');
    }

    public function destroy(Audit $audit)
    {
        Activity::log('DELETE', 'audit', $audit->id);
        $audit->delete();

        return redirect()->route('audits.index')->with('status', 'Audit deleted.');
    }

    public function storeFinding(Request $r, Audit $audit)
    {
        $data = $r->validate([
            'finding_type' => 'required|in:Major NC,Minor NC,Observation,OFI',
            'description' => 'required|string',
            'severity' => 'required|in:Low,Medium,High,Critical',
            'control_id' => 'nullable|uuid|exists:controls,id',
            'corrective_action' => 'nullable|string',
            'due_date' => 'nullable|date',
            'status' => 'required|in:Open,In Progress,Resolved,Verified,Overdue',
            'assigned_to' => 'nullable|uuid|exists:users,id',
        ]);
        $data['ref_id'] = Refs::next(AuditFinding::class, 'FIND');
        $audit->findings()->create($data);

        return back()->with('status', 'Finding added.');
    }

    public function updateFinding(Request $r, Audit $audit, AuditFinding $finding)
    {
        $data = $r->validate([
            'status' => 'required|in:Open,In Progress,Resolved,Verified,Overdue',
            'corrective_action' => 'nullable|string',
            'assigned_to' => 'nullable|uuid|exists:users,id',
            'due_date' => 'nullable|date',
        ]);
        if (in_array($data['status'], ['Resolved', 'Verified'], true) && ! $finding->closed_at) {
            $data['closed_at'] = now();
        }
        $finding->update($data);

        return back()->with('status', 'Finding updated.');
    }

    public function destroyFinding(Audit $audit, AuditFinding $finding)
    {
        $finding->delete();

        return back()->with('status', 'Finding removed.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }
}
