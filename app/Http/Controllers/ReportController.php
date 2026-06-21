<?php

namespace App\Http\Controllers;

use App\Models\AuditFinding;
use App\Models\Control;
use App\Models\Risk;
use App\Models\SoaEntry;
use App\Models\Supplier;
use App\Support\Activity;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function export(string $type): StreamedResponse
    {
        [$headers, $rows] = match ($type) {
            'controls' => $this->controls(),
            'risks' => $this->risks(),
            'soa' => $this->soa(),
            'findings' => $this->findings(),
            'suppliers' => $this->suppliers(),
            default => abort(404),
        };

        Activity::log('EXPORT', $type);

        return response()->streamDownload(function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        }, "$type-".now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function controls(): array
    {
        $rows = Control::with('owner')->orderBy('framework')->orderBy('clause')->get()
            ->map(fn ($c) => [$c->framework, $c->clause, $c->title, $c->theme, $c->status, $c->owner?->full_name, optional($c->review_date)->format('Y-m-d')]);

        return [['Framework', 'Clause', 'Title', 'Theme', 'Status', 'Owner', 'Review date'], $rows];
    }

    private function risks(): array
    {
        $rows = Risk::with('owner')->orderBy('ref_id')->get()
            ->map(fn ($r) => [$r->ref_id, $r->title, $r->category, $r->likelihood, $r->impact, $r->inherent_risk_level, $r->treatment, $r->status, $r->owner?->full_name]);

        return [['Ref', 'Title', 'Category', 'Likelihood', 'Impact', 'Inherent level', 'Treatment', 'Status', 'Owner'], $rows];
    }

    private function soa(): array
    {
        $rows = Control::with('soaEntry.responsible')->orderBy('framework')->orderBy('clause')->get()
            ->map(fn ($c) => [
                $c->clause, $c->title,
                $c->soaEntry ? ($c->soaEntry->applicable ? 'Yes' : 'No') : '',
                $c->soaEntry->implementation_status ?? '',
                $c->soaEntry->justification ?? '',
                $c->soaEntry?->responsible?->full_name ?? '',
            ]);

        return [['Clause', 'Title', 'Applicable', 'Implementation', 'Justification', 'Responsible'], $rows];
    }

    private function findings(): array
    {
        $rows = AuditFinding::with('audit', 'assignee')->orderBy('ref_id')->get()
            ->map(fn ($f) => [$f->ref_id, $f->audit?->ref_id, $f->finding_type, $f->severity, $f->status, optional($f->due_date)->format('Y-m-d'), $f->assignee?->full_name]);

        return [['Ref', 'Audit', 'Type', 'Severity', 'Status', 'Due', 'Assignee'], $rows];
    }

    private function suppliers(): array
    {
        $rows = Supplier::orderBy('ref_id')->get()
            ->map(fn ($s) => [$s->ref_id, $s->name, $s->category, $s->criticality, $s->status, $s->processes_pii ? 'Yes' : 'No', $s->certifications]);

        return [['Ref', 'Name', 'Category', 'Criticality', 'Status', 'PII', 'Certifications'], $rows];
    }
}
