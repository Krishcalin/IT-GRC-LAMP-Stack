<?php

namespace App\Http\Controllers;

use App\Models\InterestedParty;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;

class InterestedPartyController extends Controller
{
    private array $rules = [
        'name' => 'required|string|max:256',
        'party_type' => 'required|in:Internal,External',
        'category' => 'required|in:Customer,Regulator,Employee,Supplier,Partner,Owner,Other',
        'requirements' => 'nullable|string',
        'addressed_in_isms' => 'boolean',
        'notes' => 'nullable|string',
        'owner_id' => 'nullable|uuid|exists:users,id',
    ];

    public function index(Request $r)
    {
        $q = InterestedParty::with('owner')->orderBy('ref_id');
        foreach (['party_type', 'category'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        if ($r->filled('search')) {
            $q->where('name', 'like', '%'.$r->input('search').'%');
        }
        $items = $q->paginate(40)->withQueryString();

        return view('interested_parties.index', ['items' => $items, 'filters' => $r->only('party_type', 'category', 'search')]);
    }

    public function create()
    {
        return view('interested_parties.form', ['item' => new InterestedParty(['party_type' => 'External']), 'users' => $this->users()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $data['ref_id'] = Refs::next(InterestedParty::class, 'PARTY');
        $m = InterestedParty::create($data);
        Activity::log('CREATE', 'interested_party', $m->id);

        return redirect()->route('interested-parties.index')->with('status', 'Interested party created.');
    }

    public function edit(InterestedParty $interested_party)
    {
        return view('interested_parties.form', ['item' => $interested_party, 'users' => $this->users()]);
    }

    public function update(Request $r, InterestedParty $interested_party)
    {
        $interested_party->update($r->validate($this->rules));
        Activity::log('UPDATE', 'interested_party', $interested_party->id);

        return redirect()->route('interested-parties.index')->with('status', 'Interested party updated.');
    }

    public function destroy(InterestedParty $interested_party)
    {
        Activity::log('DELETE', 'interested_party', $interested_party->id);
        $interested_party->delete();

        return redirect()->route('interested-parties.index')->with('status', 'Interested party deleted.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }
}
