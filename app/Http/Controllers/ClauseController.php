<?php

namespace App\Http\Controllers;

use App\Models\ClauseRequirement;
use App\Models\User;
use App\Support\Activity;
use Illuminate\Http\Request;

class ClauseController extends Controller
{
    public function index(Request $r)
    {
        $q = ClauseRequirement::with('owner')->orderBy('clause_number')->orderBy('clause');
        if ($r->filled('section')) {
            $q->where('section', $r->input('section'));
        }
        if ($r->filled('status')) {
            $q->where('conformity_status', $r->input('status'));
        }
        if ($r->filled('search')) {
            $s = $r->input('search');
            $q->where(fn ($w) => $w->where('clause', 'like', "%$s%")->orWhere('title', 'like', "%$s%"));
        }
        $clauses = $q->paginate(50)->withQueryString();
        $sections = ClauseRequirement::query()->select('section')->distinct()->pluck('section');

        return view('clauses.index', compact('clauses', 'sections') + ['filters' => $r->only('section', 'status', 'search')]);
    }

    public function show(ClauseRequirement $clause)
    {
        $clause->load('owner');
        $users = User::orderBy('full_name')->get(['id', 'full_name']);

        return view('clauses.show', compact('clause', 'users'));
    }

    public function update(Request $r, ClauseRequirement $clause)
    {
        $data = $r->validate([
            'conformity_status' => 'required|in:Not Assessed,In Progress,Partially Conformant,Conformant,Nonconformant',
            'implementation_notes' => 'nullable|string',
            'owner_id' => 'nullable|uuid|exists:users,id',
            'review_date' => 'nullable|date',
        ]);
        $clause->update($data);
        Activity::log('UPDATE', 'clause', $clause->id);

        return back()->with('status', 'Clause updated.');
    }
}
