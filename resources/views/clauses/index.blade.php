@extends('layouts.app')
@section('title', 'ISMS Clauses (4–10)')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center gap-2">
        <h2 class="text-xl font-semibold text-gray-900">ISMS Clauses 4–10</h2>
        <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $clauses->total() }}</span>
    </div>
    <p class="text-sm text-gray-400">Mandatory management-system requirements (tracked for conformity, distinct from Annex A controls).</p>

    <form method="GET" class="flex flex-wrap gap-2">
        <select name="section" onchange="this.form.submit()" class="{{ $sel }}">
            <option value="">All Sections</option>
            @foreach($sections as $s)<option value="{{ $s }}" @selected(($filters['section'] ?? '') === $s)>{{ $s }}</option>@endforeach
        </select>
        <select name="status" onchange="this.form.submit()" class="{{ $sel }}">
            <option value="">All Statuses</option>
            @foreach(['Not Assessed','In Progress','Partially Conformant','Conformant','Nonconformant'] as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach
        </select>
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search…" class="{{ $sel }} flex-1 min-w-[200px]">
        <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>

    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Clause</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Section</th>
                <th class="px-4 py-3">Conformity</th><th class="px-4 py-3">Owner</th>
            </tr></thead>
            <tbody>
                @foreach($clauses as $c)
                    <tr onclick="location='{{ route('clauses.show', $c) }}'" class="border-t border-gray-100 hover:bg-brand-50/40 cursor-pointer">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $c->clause }}</td>
                        <td class="px-4 py-2.5">{{ $c->title }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $c->section }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$c->conformity_status"/></td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $c->owner?->full_name ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
    {{ $clauses->links() }}
</div>
@endsection
