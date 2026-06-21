<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use App\Models\Control;
use App\Models\Evidence;
use App\Models\Policy;
use App\Models\Risk;
use App\Support\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EvidenceController extends Controller
{
    public function index(Request $r)
    {
        $q = Evidence::with('uploader', 'control', 'risk', 'audit', 'policy')->orderByDesc('created_at');
        foreach (['control_id', 'risk_id', 'audit_id', 'policy_id'] as $f) {
            if ($r->filled($f)) {
                $q->where($f, $r->input($f));
            }
        }
        $items = $q->paginate(40)->withQueryString();

        return view('evidence.index', [
            'items' => $items,
            'controls' => Control::orderBy('clause')->get(['id', 'clause', 'title']),
            'risks' => Risk::orderBy('ref_id')->get(['id', 'ref_id', 'title']),
            'audits' => Audit::orderBy('ref_id')->get(['id', 'ref_id', 'title']),
            'policies' => Policy::orderBy('ref_id')->get(['id', 'ref_id', 'title']),
        ]);
    }

    public function store(Request $r)
    {
        $maxKb = (int) env('MAX_UPLOAD_SIZE_MB', 25) * 1024;
        $data = $r->validate([
            'title' => 'required|string|max:256',
            'description' => 'nullable|string',
            'file' => "required|file|max:$maxKb",
            'control_id' => 'nullable|uuid|exists:controls,id',
            'risk_id' => 'nullable|uuid|exists:risks,id',
            'audit_id' => 'nullable|uuid|exists:audits,id',
            'policy_id' => 'nullable|uuid|exists:policies,id',
        ]);
        $file = $r->file('file');
        $path = $file->store('files', 'evidence');
        $e = Evidence::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => Auth::id(),
            'control_id' => $data['control_id'] ?? null,
            'risk_id' => $data['risk_id'] ?? null,
            'audit_id' => $data['audit_id'] ?? null,
            'policy_id' => $data['policy_id'] ?? null,
        ]);
        Activity::log('CREATE', 'evidence', $e->id);

        return back()->with('status', 'Evidence uploaded.');
    }

    public function download(Evidence $evidence): StreamedResponse
    {
        abort_unless(Storage::disk('evidence')->exists($evidence->file_path), 404);

        return Storage::disk('evidence')->download($evidence->file_path, $evidence->file_name);
    }

    public function destroy(Evidence $evidence)
    {
        Storage::disk('evidence')->delete($evidence->file_path);
        Activity::log('DELETE', 'evidence', $evidence->id);
        $evidence->delete();

        return back()->with('status', 'Evidence deleted.');
    }
}
