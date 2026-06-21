@extends('layouts.app')
@section('title', 'Assets')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2"><h2 class="text-xl font-semibold text-gray-900">Asset Inventory</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $items->total() }}</span></div>
        <a href="{{ route('assets.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ New Asset</a>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="asset_type" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Types</option>
            @foreach(['Hardware','Software','Data','Service','People','Facility'] as $t)<option value="{{ $t }}" @selected(($filters['asset_type'] ?? '') === $t)>{{ $t }}</option>@endforeach</select>
        <select name="criticality" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Criticality</option>
            @foreach(['Low','Medium','High','Critical'] as $c)<option value="{{ $c }}" @selected(($filters['criticality'] ?? '') === $c)>{{ $c }}</option>@endforeach</select>
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search…" class="{{ $sel }} flex-1 min-w-[200px]">
        <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>
    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Ref</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Classification</th><th class="px-4 py-3">Criticality</th><th class="px-4 py-3">Owner</th></tr></thead>
            <tbody>
                @forelse($items as $a)
                    <tr onclick="location='{{ route('assets.edit', $a) }}'" class="border-t border-gray-100 hover:bg-brand-50/40 cursor-pointer">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $a->ref_id }}</td>
                        <td class="px-4 py-2.5">{{ $a->name }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $a->asset_type }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$a->classification"/></td>
                        <td class="px-4 py-2.5"><x-badge :value="$a->criticality"/></td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $a->owner?->full_name ?? '—' }}</td>
                    </tr>
                @empty<tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No assets.</td></tr>@endforelse
            </tbody>
        </table>
    </x-card>
    {{ $items->links() }}
</div>
@endsection
