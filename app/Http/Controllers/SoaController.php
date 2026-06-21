<?php

namespace App\Http\Controllers;

use App\Models\Control;
use App\Models\SoaEntry;
use App\Models\User;
use App\Support\Activity;
use Illuminate\Http\Request;

class SoaController extends Controller
{
    public function index(Request $r)
    {
        $q = Control::with('soaEntry.responsible')->orderBy('framework')->orderBy('clause');
        if ($r->filled('framework')) {
            $q->where('framework', $r->input('framework'));
        }
        $controls = $q->paginate(40)->withQueryString();
        $frameworks = Control::query()->select('framework')->distinct()->orderBy('framework')->pluck('framework');

        return view('soa.index', compact('controls', 'frameworks') + ['filters' => $r->only('framework')]);
    }

    public function edit(Control $control)
    {
        $control->load('soaEntry');
        $users = User::orderBy('full_name')->get(['id', 'full_name']);

        return view('soa.edit', compact('control', 'users'));
    }

    public function update(Request $r, Control $control)
    {
        $data = $r->validate([
            'applicable' => 'required|boolean',
            'justification' => 'nullable|string',
            'implementation_status' => 'required|in:Not Implemented,Partially,Fully,N/A',
            'implementation_evidence' => 'nullable|string',
            'responsible_id' => 'nullable|uuid|exists:users,id',
            'notes' => 'nullable|string',
        ]);
        SoaEntry::updateOrCreate(['control_id' => $control->id], $data);
        Activity::log('UPDATE', 'soa', $control->id);

        return redirect()->route('soa.index')->with('status', 'SoA entry saved.');
    }
}
