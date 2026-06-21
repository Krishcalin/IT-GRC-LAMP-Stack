@extends('layouts.app')
@section('title', 'Reports')
@section('content')
@php
    $reports = [
        ['controls', 'Controls Catalogue', 'All controls (5 frameworks) with theme, status, owner, review date.'],
        ['risks', 'Risk Register', 'All risks with likelihood × impact, inherent level, treatment and status.'],
        ['soa', 'Statement of Applicability', 'Per-control applicability, implementation status and justification.'],
        ['findings', 'Audit Findings', 'All findings with type, severity, status, due date and assignee.'],
        ['suppliers', 'Suppliers', 'Third parties with category, criticality, PII flag and certifications.'],
    ];
@endphp
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">Reports &amp; Exports</h2>
        <p class="text-sm text-gray-400">Download register extracts as CSV for auditors or offline analysis.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($reports as [$type, $title, $desc])
            <x-card class="p-5 flex flex-col">
                <h3 class="font-semibold text-gray-900">{{ $title }}</h3>
                <p class="text-sm text-gray-500 mt-1 flex-1">{{ $desc }}</p>
                <a href="{{ route('reports.export', $type) }}" class="mt-4 inline-flex items-center justify-center gap-2 bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Download CSV
                </a>
            </x-card>
        @endforeach
    </div>
</div>
@endsection
