<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private array $rules = [
        'name' => 'required|string|max:256',
        'description' => 'nullable|string',
        'category' => 'required|in:Product,Service,ICT Supply Chain,Cloud Service',
        'service_description' => 'nullable|string',
        'criticality' => 'required|in:Low,Medium,High,Critical',
        'data_classification' => 'required|in:Public,Internal,Confidential,Restricted',
        'status' => 'required|in:Active,Onboarding,Under Review,Offboarded',
        'is_requirements_agreed' => 'boolean',
        'right_to_audit' => 'boolean',
        'processes_pii' => 'boolean',
        'certifications' => 'nullable|string|max:256',
        'owner_id' => 'nullable|uuid|exists:users,id',
        'contract_start' => 'nullable|date',
        'contract_end' => 'nullable|date',
        'last_review_date' => 'nullable|date',
        'next_review_date' => 'nullable|date',
        'notes' => 'nullable|string',
    ];

    public function index(Request $r)
    {
        $q = Supplier::with('owner')->orderBy('ref_id');
        foreach (['category', 'criticality', 'status'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        if ($r->filled('search')) {
            $q->where('name', 'like', '%'.$r->input('search').'%');
        }
        $items = $q->paginate(40)->withQueryString();

        return view('suppliers.index', ['items' => $items, 'filters' => $r->only('category', 'criticality', 'status', 'search')]);
    }

    public function create()
    {
        return view('suppliers.form', ['item' => new Supplier(['category' => 'Service', 'criticality' => 'Medium', 'data_classification' => 'Internal', 'status' => 'Active']), 'users' => $this->users()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $data['ref_id'] = Refs::next(Supplier::class, 'SUP');
        $m = Supplier::create($data);
        Activity::log('CREATE', 'supplier', $m->id);

        return redirect()->route('suppliers.index')->with('status', 'Supplier created.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.form', ['item' => $supplier, 'users' => $this->users()]);
    }

    public function update(Request $r, Supplier $supplier)
    {
        $supplier->update($r->validate($this->rules));
        Activity::log('UPDATE', 'supplier', $supplier->id);

        return redirect()->route('suppliers.index')->with('status', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        Activity::log('DELETE', 'supplier', $supplier->id);
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('status', 'Supplier deleted.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }
}
