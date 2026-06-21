@extends('layouts.app')
@section('title', 'Suppliers')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2"><h2 class="text-xl font-semibold text-gray-900">Suppliers &amp; Third Parties</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $items->total() }}</span></div>
        <a href="{{ route('suppliers.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ New Supplier</a>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="criticality" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Criticality</option>
            @foreach(['Low','Medium','High','Critical'] as $c)<option value="{{ $c }}" @selected(($filters['criticality'] ?? '') === $c)>{{ $c }}</option>@endforeach</select>
        <select name="status" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Statuses</option>
            @foreach(['Active','Onboarding','Under Review','Offboarded'] as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach</select>
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search…" class="{{ $sel }} flex-1 min-w-[200px]">
        <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>
    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Ref</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Criticality</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">PII</th><th class="px-4 py-3">Certs</th></tr></thead>
            <tbody>
                @forelse($items as $s)
                    <tr onclick="location='{{ route('suppliers.edit', $s) }}'" class="border-t border-gray-100 hover:bg-brand-50/40 cursor-pointer">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $s->ref_id }}</td>
                        <td class="px-4 py-2.5">{{ $s->name }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $s->category }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$s->criticality"/></td>
                        <td class="px-4 py-2.5"><x-badge :value="$s->status"/></td>
                        <td class="px-4 py-2.5">{{ $s->processes_pii ? '⚠️' : '—' }}</td>
                        <td class="px-4 py-2.5 text-xs text-gray-400">{{ \Illuminate\Support\Str::limit($s->certifications, 24) ?: '—' }}</td>
                    </tr>
                @empty<tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No suppliers.</td></tr>@endforelse
            </tbody>
        </table>
    </x-card>
    {{ $items->links() }}
</div>
@endsection
