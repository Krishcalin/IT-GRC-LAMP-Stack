<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use App\Support\Scoring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    private array $rules = [
        'title' => 'required|string|max:256',
        'description' => 'nullable|string',
        'task_type' => 'required|in:Action,Approval,Review,Remediation',
        'status' => 'required|in:Open,In Progress,Blocked,Done,Cancelled',
        'priority' => 'required|in:Low,Medium,High,Critical',
        'assignee_id' => 'nullable|uuid|exists:users,id',
        'due_date' => 'nullable|date',
        'resource_type' => 'nullable|string|max:32',
        'resource_label' => 'nullable|string|max:256',
    ];

    public function index(Request $r)
    {
        $q = Task::with('assignee')->orderByRaw('due_date is null, due_date');
        foreach (['status', 'task_type', 'priority', 'assignee_id'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        if ($r->boolean('open_only')) {
            $q->whereIn('status', Task::OPEN_STATUSES);
        }
        if ($r->filled('search')) {
            $q->where('title', 'like', '%'.$r->input('search').'%');
        }
        $items = $q->paginate(40)->withQueryString();
        if ($r->boolean('overdue')) {
            $items->setCollection($items->getCollection()->filter(fn ($t) => $t->overdue)->values());
        }

        return view('tasks.index', ['items' => $items, 'users' => $this->users(), 'filters' => $r->only('status', 'task_type', 'priority', 'assignee_id', 'open_only', 'overdue', 'search')]);
    }

    public function create()
    {
        return view('tasks.form', ['item' => new Task(['task_type' => 'Action', 'status' => 'Open', 'priority' => 'Medium']), 'users' => $this->users()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $data['ref_id'] = Refs::next(Task::class, 'TASK');
        $data['created_by_id'] = Auth::id();
        if ($data['status'] === 'Done') {
            $data['completed_at'] = now();
        }
        $t = Task::create($data);
        Activity::log('CREATE', 'task', $t->id);

        return redirect()->route('tasks.index')->with('status', 'Task created.');
    }

    public function edit(Task $task)
    {
        return view('tasks.form', ['item' => $task, 'users' => $this->users()]);
    }

    public function update(Request $r, Task $task)
    {
        $data = $r->validate($this->rules);
        if ($data['status'] === 'Done' && ! $task->completed_at) {
            $data['completed_at'] = now();
        } elseif ($data['status'] !== 'Done') {
            $data['completed_at'] = null;
        }
        $task->update($data);
        Activity::log('UPDATE', 'task', $task->id);

        return redirect()->route('tasks.index')->with('status', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        Activity::log('DELETE', 'task', $task->id);
        $task->delete();

        return redirect()->route('tasks.index')->with('status', 'Task deleted.');
    }

    public function decision(Request $r, Task $task)
    {
        $data = $r->validate([
            'decision' => 'required|in:Approved,Rejected',
            'decision_comment' => 'nullable|string',
        ]);
        $task->update([
            'decision' => $data['decision'],
            'decision_comment' => $data['decision_comment'] ?? null,
            'decided_by_id' => Auth::id(),
            'decided_at' => now(),
            'status' => 'Done',
            'completed_at' => now(),
        ]);
        Activity::log('DECISION:'.$data['decision'], 'task', $task->id);

        return back()->with('status', 'Decision recorded.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }
}
