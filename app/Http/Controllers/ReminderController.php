<?php

namespace App\Http\Controllers;

use App\Models\AuditFinding;
use App\Models\ClauseRequirement;
use App\Models\Control;
use App\Models\DocumentedInformation;
use App\Models\Policy;
use App\Models\Risk;
use App\Models\Supplier;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReminderController extends Controller
{
    public function index(Request $r): View
    {
        $days = (int) $r->input('days', 30);
        $today = Carbon::today();
        $horizon = $today->copy()->addDays($days);
        $items = collect();

        $push = function ($type, $ref, $title, $date, $url) use ($items, $today) {
            if (! $date) {
                return;
            }
            $d = Carbon::parse($date);
            $items->push([
                'type' => $type, 'ref' => $ref, 'title' => $title,
                'date' => $d, 'overdue' => $d->lt($today), 'url' => $url,
            ]);
        };

        foreach (Control::whereNotNull('review_date')->get() as $c) {
            $push('Control', $c->clause, $c->title, $c->review_date, route('controls.show', $c));
        }
        foreach (ClauseRequirement::whereNotNull('review_date')->get() as $c) {
            $push('Clause', $c->clause, $c->title, $c->review_date, route('clauses.show', $c));
        }
        foreach (DocumentedInformation::whereNotNull('next_review_date')->get() as $d) {
            $push('Document', $d->ref_id, $d->title, $d->next_review_date, route('documents.edit', $d));
        }
        foreach (Supplier::whereNotNull('next_review_date')->get() as $s) {
            $push('Supplier', $s->ref_id, $s->name, $s->next_review_date, route('suppliers.edit', $s));
        }
        foreach (Policy::whereNotNull('next_review_date')->get() as $p) {
            $push('Policy', $p->ref_id, $p->title, $p->next_review_date, route('policies.show', $p));
        }
        foreach (Risk::whereNotNull('review_date')->get() as $rk) {
            $push('Risk', $rk->ref_id, $rk->title, $rk->review_date, route('risks.show', $rk));
        }
        foreach (Task::whereIn('status', Task::OPEN_STATUSES)->whereNotNull('due_date')->get() as $t) {
            $push('Task', $t->ref_id, $t->title, $t->due_date, route('tasks.edit', $t));
        }
        foreach (AuditFinding::whereIn('status', ['Open', 'In Progress'])->whereNotNull('due_date')->with('audit')->get() as $f) {
            $push('Finding', $f->ref_id, $f->description, $f->due_date, $f->audit ? route('audits.show', $f->audit) : '#');
        }

        $items = $items->filter(fn ($i) => $i['date']->lte($horizon))->sortBy(fn ($i) => $i['date']->timestamp)->values();

        return view('reminders.index', ['items' => $items, 'days' => $days]);
    }
}
