@extends('layouts.app')
@section('title', 'Documented Information')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2"><h2 class="text-xl font-semibold text-gray-900">Documented Information</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $items->total() }}</span></div>
        <a href="{{ route('documents.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ New Document</a>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="status" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Statuses</option>
            @foreach(['Draft','Under Review','Approved','Retired'] as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach</select>
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search…" class="{{ $sel }} flex-1 min-w-[200px]">
        <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>
    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Ref</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Clause</th><th class="px-4 py-3">Version</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Owner</th></tr></thead>
            <tbody>
                @forelse($items as $d)
                    <tr onclick="location='{{ route('documents.edit', $d) }}'" class="border-t border-gray-100 hover:bg-brand-50/40 cursor-pointer">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $d->ref_id }}</td>
                        <td class="px-4 py-2.5">{{ $d->title }} @if($d->mandatory)<span class="text-[10px] bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded ml-1">mandatory</span>@endif</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $d->doc_type }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $d->clause_ref ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $d->version }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$d->status"/></td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $d->owner?->full_name ?? '—' }}</td>
                    </tr>
                @empty<tr><td colspan="7" class="px-4 py-12 text-center text-gray-400">No documents.</td></tr>@endforelse
            </tbody>
        </table>
    </x-card>
    {{ $items->links() }}
</div>
@endsection
