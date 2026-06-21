<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    private array $rules = [
        'name' => 'required|string|max:256',
        'description' => 'nullable|string',
        'asset_type' => 'required|in:Hardware,Software,Data,Service,People,Facility',
        'classification' => 'required|in:Public,Internal,Confidential,Restricted',
        'owner_id' => 'nullable|uuid|exists:users,id',
        'department' => 'nullable|string|max:128',
        'location' => 'nullable|string|max:256',
        'status' => 'required|in:Active,Inactive,Decommissioned',
        'criticality' => 'required|in:Low,Medium,High,Critical',
    ];

    public function index(Request $r)
    {
        $q = Asset::with('owner')->orderBy('ref_id');
        foreach (['asset_type', 'classification', 'status', 'criticality'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        if ($r->filled('search')) {
            $q->where('name', 'like', '%'.$r->input('search').'%');
        }
        $items = $q->paginate(40)->withQueryString();

        return view('assets.index', ['items' => $items, 'filters' => $r->only('asset_type', 'classification', 'status', 'criticality', 'search')]);
    }

    public function create()
    {
        return view('assets.form', ['item' => new Asset(['asset_type' => 'Hardware', 'classification' => 'Internal', 'status' => 'Active', 'criticality' => 'Medium']), 'users' => $this->users()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $data['ref_id'] = Refs::next(Asset::class, 'ASSET');
        $m = Asset::create($data);
        Activity::log('CREATE', 'asset', $m->id);

        return redirect()->route('assets.index')->with('status', 'Asset created.');
    }

    public function edit(Asset $asset)
    {
        return view('assets.form', ['item' => $asset, 'users' => $this->users()]);
    }

    public function update(Request $r, Asset $asset)
    {
        $asset->update($r->validate($this->rules));
        Activity::log('UPDATE', 'asset', $asset->id);

        return redirect()->route('assets.index')->with('status', 'Asset updated.');
    }

    public function destroy(Asset $asset)
    {
        Activity::log('DELETE', 'asset', $asset->id);
        $asset->delete();

        return redirect()->route('assets.index')->with('status', 'Asset deleted.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }
}
