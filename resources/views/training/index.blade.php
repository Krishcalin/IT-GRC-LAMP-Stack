@extends('layouts.app')
@section('title', 'Awareness & Training')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2"><h2 class="text-xl font-semibold text-gray-900">Awareness &amp; Training</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $items->total() }}</span></div>
        <a href="{{ route('training.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ New Campaign</a>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="status" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Statuses</option>
            @foreach(['Planned','In Progress','Completed','Cancelled'] as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach</select>
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search…" class="{{ $sel }} flex-1 min-w-[200px]">
        <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>
    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Ref</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Completion</th><th class="px-4 py-3">Status</th></tr></thead>
            <tbody>
                @forelse($items as $c)
                    <tr onclick="location='{{ route('training.show', $c) }}'" class="border-t border-gray-100 hover:bg-brand-50/40 cursor-pointer">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $c->ref_id }}</td>
                        <td class="px-4 py-2.5">{{ $c->title }}</td>
                        <td class="px-4 py-2.5 text-xs text-gray-500">{{ $c->training_type }}</td>
                        <td class="px-4 py-2.5"><span class="text-gray-600">{{ $c->completion_rate }}%</span> <span class="text-xs text-gray-400">({{ $c->completed_participants }}/{{ $c->total_participants }})</span></td>
                        <td class="px-4 py-2.5"><x-badge :value="$c->status"/></td>
                    </tr>
                @empty<tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">No campaigns.</td></tr>@endforelse
            </tbody>
        </table>
    </x-card>
    {{ $items->links() }}
</div>
@endsection
