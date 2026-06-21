<?php

namespace App\Http\Controllers;

use App\Models\Control;
use App\Models\Risk;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    private array $rules = [
        'title' => 'required|string|max:256',
        'description' => 'required|string',
        'category' => 'required|string|max:64',
        'likelihood' => 'required|integer|min:1|max:5',
        'impact' => 'required|integer|min:1|max:5',
        'treatment' => 'required|in:Mitigate,Accept,Transfer,Avoid',
        'treatment_plan' => 'nullable|string',
        'residual_likelihood' => 'nullable|integer|min:1|max:5',
        'residual_impact' => 'nullable|integer|min:1|max:5',
        'status' => 'required|in:Open,In Treatment,Closed,Accepted',
        'owner_id' => 'nullable|uuid|exists:users,id',
        'review_date' => 'nullable|date',
    ];

    public function index(Request $r)
    {
        $q = Risk::with('owner')->orderByDesc('ref_id');
        foreach (['category', 'status', 'inherent_risk_level'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        if ($r->filled('search')) {
            $s = $r->input('search');
            $q->where(fn ($w) => $w->where('ref_id', 'like', "%$s%")->orWhere('title', 'like', "%$s%"));
        }
        $risks = $q->paginate(40)->withQueryString();

        return view('risks.index', ['risks' => $risks, 'filters' => $r->only('category', 'status', 'inherent_risk_level', 'search')]);
    }

    public function create()
    {
        return view('risks.form', ['risk' => new Risk(['likelihood' => 1, 'impact' => 1]), 'users' => $this->users()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $risk = new Risk($data);
        $risk->ref_id = Refs::next(Risk::class, 'RISK');
        $risk->recalculateLevels();
        $risk->save();
        Activity::log('CREATE', 'risk', $risk->id);

        return redirect()->route('risks.show', $risk)->with('status', 'Risk created.');
    }

    public function show(Risk $risk)
    {
        $risk->load('owner', 'controls');
        $controls = Control::orderBy('clause')->get(['id', 'clause', 'title']);

        return view('risks.show', compact('risk', 'controls'));
    }

    public function edit(Risk $risk)
    {
        return view('risks.form', ['risk' => $risk, 'users' => $this->users()]);
    }

    public function update(Request $r, Risk $risk)
    {
        $data = $r->validate($this->rules);
        $risk->fill($data);
        $risk->recalculateLevels();
        $risk->save();
        Activity::log('UPDATE', 'risk', $risk->id);

        return redirect()->route('risks.show', $risk)->with('status', 'Risk updated.');
    }

    public function destroy(Risk $risk)
    {
        Activity::log('DELETE', 'risk', $risk->id);
        $risk->delete();

        return redirect()->route('risks.index')->with('status', 'Risk deleted.');
    }

    public function linkControl(Request $r, Risk $risk)
    {
        $data = $r->validate(['control_id' => 'required|uuid|exists:controls,id']);
        $risk->controls()->syncWithoutDetaching([$data['control_id']]);

        return back()->with('status', 'Control linked.');
    }

    public function unlinkControl(Risk $risk, Control $control)
    {
        $risk->controls()->detach($control->id);

        return back()->with('status', 'Control unlinked.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }
}
