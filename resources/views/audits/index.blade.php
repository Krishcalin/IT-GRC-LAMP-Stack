@extends('layouts.app')
@section('title', 'Audits')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2"><h2 class="text-xl font-semibold text-gray-900">Audits</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $items->total() }}</span></div>
        <a href="{{ route('audits.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ New Audit</a>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="audit_type" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Types</option>
            @foreach(['Internal','External','Surveillance'] as $t)<option value="{{ $t }}" @selected(($filters['audit_type'] ?? '') === $t)>{{ $t }}</option>@endforeach</select>
        <select name="status" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Statuses</option>
            @foreach(['Planned','In Progress','Completed','Cancelled'] as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach</select>
    </form>
    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Ref</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Findings</th><th class="px-4 py-3">Lead</th><th class="px-4 py-3">Status</th></tr></thead>
            <tbody>
                @forelse($items as $a)
                    <tr onclick="location='{{ route('audits.show', $a) }}'" class="border-t border-gray-100 hover:bg-brand-50/40 cursor-pointer">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $a->ref_id }}</td>
                        <td class="px-4 py-2.5">{{ $a->title }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $a->audit_type }}</td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $a->findings->count() }}</td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $a->leadAuditor?->full_name ?? '—' }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$a->status"/></td>
                    </tr>
                @empty<tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No audits.</td></tr>@endforelse
            </tbody>
        </table>
    </x-card>
    {{ $items->links() }}
</div>
@endsection
