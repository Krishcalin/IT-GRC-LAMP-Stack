@extends('layouts.app')
@section('title', 'Risk Register')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2">
            <h2 class="text-xl font-semibold text-gray-900">Risk Register</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $risks->total() }}</span>
        </div>
        <a href="{{ route('risks.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ New Risk</a>
    </div>

    <form method="GET" class="flex flex-wrap gap-2">
        <select name="status" onchange="this.form.submit()" class="{{ $sel }}">
            <option value="">All Statuses</option>
            @foreach(['Open','In Treatment','Closed','Accepted'] as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach
        </select>
        <select name="inherent_risk_level" onchange="this.form.submit()" class="{{ $sel }}">
            <option value="">All Levels</option>
            @foreach(['Low','Medium','High','Critical'] as $l)<option value="{{ $l }}" @selected(($filters['inherent_risk_level'] ?? '') === $l)>{{ $l }}</option>@endforeach
        </select>
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search…" class="{{ $sel }} flex-1 min-w-[200px]">
        <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>

    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Ref</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">L×I</th><th class="px-4 py-3">Inherent</th><th class="px-4 py-3">Treatment</th>
                <th class="px-4 py-3">Status</th><th class="px-4 py-3">Owner</th>
            </tr></thead>
            <tbody>
                @forelse($risks as $rk)
                    <tr onclick="location='{{ route('risks.show', $rk) }}'" class="border-t border-gray-100 hover:bg-brand-50/40 cursor-pointer">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $rk->ref_id }}</td>
                        <td class="px-4 py-2.5">{{ \Illuminate\Support\Str::limit($rk->title, 50) }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $rk->category }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $rk->likelihood }}×{{ $rk->impact }} = {{ $rk->likelihood * $rk->impact }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$rk->inherent_risk_level"/></td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $rk->treatment }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$rk->status"/></td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $rk->owner?->full_name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-12 text-center text-gray-400">No risks yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
    {{ $risks->links() }}
</div>
@endsection
