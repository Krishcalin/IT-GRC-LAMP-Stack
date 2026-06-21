@extends('layouts.app')
@section('title', 'Interested Parties')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2"><h2 class="text-xl font-semibold text-gray-900">Interested Parties</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $items->total() }}</span></div>
        <a href="{{ route('interested-parties.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ New Party</a>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="party_type" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Types</option>
            @foreach(['Internal','External'] as $t)<option value="{{ $t }}" @selected(($filters['party_type'] ?? '') === $t)>{{ $t }}</option>@endforeach</select>
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search…" class="{{ $sel }} flex-1 min-w-[200px]">
        <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>
    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Ref</th><th class="px-4 py-3">Name</th><th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Category</th><th class="px-4 py-3">Addressed in ISMS</th></tr></thead>
            <tbody>
                @forelse($items as $p)
                    <tr onclick="location='{{ route('interested-parties.edit', $p) }}'" class="border-t border-gray-100 hover:bg-brand-50/40 cursor-pointer">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $p->ref_id }}</td>
                        <td class="px-4 py-2.5">{{ $p->name }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $p->party_type }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $p->category }}</td>
                        <td class="px-4 py-2.5">{{ $p->addressed_in_isms ? '✓ Yes' : '— No' }}</td>
                    </tr>
                @empty<tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">No parties.</td></tr>@endforelse
            </tbody>
        </table>
    </x-card>
    {{ $items->links() }}
</div>
@endsection
