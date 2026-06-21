<?php

namespace App\Http\Controllers;

use App\Models\DocumentedInformation;
use App\Models\User;
use App\Support\Activity;
use App\Support\Refs;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    private array $rules = [
        'title' => 'required|string|max:256',
        'doc_type' => 'required|string|max:32',
        'clause_ref' => 'nullable|string|max:32',
        'description' => 'nullable|string',
        'status' => 'required|in:Draft,Under Review,Approved,Retired',
        'classification' => 'required|in:Public,Internal,Confidential,Restricted',
        'version' => 'nullable|string|max:16',
        'location' => 'nullable|string|max:512',
        'owner_id' => 'nullable|uuid|exists:users,id',
        'mandatory' => 'boolean',
        'review_date' => 'nullable|date',
        'next_review_date' => 'nullable|date',
    ];

    public function index(Request $r)
    {
        $q = DocumentedInformation::with('owner')->orderBy('ref_id');
        foreach (['doc_type', 'status', 'clause_ref'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        if ($r->filled('search')) {
            $s = $r->input('search');
            $q->where(fn ($w) => $w->where('title', 'like', "%$s%")->orWhere('ref_id', 'like', "%$s%"));
        }
        $items = $q->paginate(40)->withQueryString();

        return view('documents.index', ['items' => $items, 'filters' => $r->only('doc_type', 'status', 'clause_ref', 'search')]);
    }

    public function create()
    {
        return view('documents.form', ['item' => new DocumentedInformation(['status' => 'Draft', 'classification' => 'Internal', 'version' => '0.1']), 'users' => $this->users()]);
    }

    public function store(Request $r)
    {
        $data = $r->validate($this->rules);
        $data['ref_id'] = Refs::next(DocumentedInformation::class, 'DOC');
        $m = DocumentedInformation::create($data);
        Activity::log('CREATE', 'document', $m->id);

        return redirect()->route('documents.index')->with('status', 'Document created.');
    }

    public function edit(DocumentedInformation $document)
    {
        return view('documents.form', ['item' => $document, 'users' => $this->users()]);
    }

    public function update(Request $r, DocumentedInformation $document)
    {
        $document->update($r->validate($this->rules));
        Activity::log('UPDATE', 'document', $document->id);

        return redirect()->route('documents.index')->with('status', 'Document updated.');
    }

    public function destroy(DocumentedInformation $document)
    {
        Activity::log('DELETE', 'document', $document->id);
        $document->delete();

        return redirect()->route('documents.index')->with('status', 'Document deleted.');
    }

    private function users()
    {
        return User::orderBy('full_name')->get(['id', 'full_name']);
    }
}
