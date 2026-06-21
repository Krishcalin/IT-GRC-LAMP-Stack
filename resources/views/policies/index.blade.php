@extends('layouts.app')
@section('title', 'Policies')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2"><h2 class="text-xl font-semibold text-gray-900">Policies</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $items->total() }}</span></div>
        <a href="{{ route('policies.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ New Policy</a>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="status" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Statuses</option>
            @foreach(['Draft','Under Review','Approved','Retired'] as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach</select>
    </form>
    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Ref</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Category</th>
                <th class="px-4 py-3">Version</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Owner</th></tr></thead>
            <tbody>
                @forelse($items as $p)
                    <tr onclick="location='{{ route('policies.show', $p) }}'" class="border-t border-gray-100 hover:bg-brand-50/40 cursor-pointer">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $p->ref_id }}</td>
                        <td class="px-4 py-2.5">{{ $p->title }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $p->category }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ $p->version }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$p->status"/></td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $p->owner?->full_name ?? '—' }}</td>
                    </tr>
                @empty<tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No policies.</td></tr>@endforelse
            </tbody>
        </table>
    </x-card>
    {{ $items->links() }}
</div>
@endsection
