@extends('layouts.app')
@section('title', 'IS Objectives')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2"><h2 class="text-xl font-semibold text-gray-900">Information Security Objectives</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $items->total() }}</span></div>
        <a href="{{ route('objectives.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ New Objective</a>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="status" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Statuses</option>
            @foreach(['Not Started','On Track','At Risk','Achieved','Missed'] as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach</select>
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search…" class="{{ $sel }} flex-1 min-w-[200px]">
        <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>
    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Ref</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Target</th>
                <th class="px-4 py-3">Current</th><th class="px-4 py-3">Metrics</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Owner</th></tr></thead>
            <tbody>
                @forelse($items as $o)
                    <tr onclick="location='{{ route('objectives.edit', $o) }}'" class="border-t border-gray-100 hover:bg-brand-50/40 cursor-pointer">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $o->ref_id }}</td>
                        <td class="px-4 py-2.5">{{ $o->title }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $o->target_value ?? '—' }} {{ $o->unit }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $o->current_value ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $o->metrics->count() }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$o->status"/></td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $o->owner?->full_name ?? '—' }}</td>
                    </tr>
                @empty<tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No objectives.</td></tr>@endforelse
            </tbody>
        </table>
    </x-card>
    {{ $items->links() }}
</div>
@endsection
