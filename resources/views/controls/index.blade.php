@extends('layouts.app')
@section('title', 'Controls')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4" x-data="{ create: {{ $errors->any() ? 'true' : 'false' }} }">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2">
            <h2 class="text-xl font-semibold text-gray-900">Controls</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $controls->total() }}</span>
        </div>
        <button @click="create=true" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ New Control</button>
    </div>

    <form method="GET" class="flex flex-wrap gap-2">
        <select name="framework" onchange="this.form.submit()" class="{{ $sel }}">
            <option value="">All Frameworks</option>
            @foreach($frameworks as $f)<option value="{{ $f }}" @selected(($filters['framework'] ?? '') === $f)>{{ $f }}</option>@endforeach
        </select>
        <select name="theme" onchange="this.form.submit()" class="{{ $sel }}">
            <option value="">All Themes</option>
            @foreach(['Organizational','People','Physical','Technological'] as $t)<option value="{{ $t }}" @selected(($filters['theme'] ?? '') === $t)>{{ $t }}</option>@endforeach
        </select>
        <select name="status" onchange="this.form.submit()" class="{{ $sel }}">
            <option value="">All Statuses</option>
            @foreach(['Not Started','In Progress','Implemented','Not Applicable'] as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach
        </select>
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search clause or title…" class="{{ $sel }} flex-1 min-w-[200px]">
        <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>

    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Clause</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Theme</th>
                <th class="px-4 py-3">Framework</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Owner</th>
            </tr></thead>
            <tbody>
                @forelse($controls as $c)
                    <tr onclick="location='{{ route('controls.show', $c) }}'" class="border-t border-gray-100 hover:bg-brand-50/40 cursor-pointer">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $c->clause }}</td>
                        <td class="px-4 py-2.5">{{ $c->title }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$c->theme"/></td>
                        <td class="px-4 py-2.5 text-xs text-gray-500">{{ $c->framework }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$c->status"/></td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $c->owner?->full_name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No controls found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
    {{ $controls->links() }}

    <div x-show="create" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
        <div @click.outside="create=false" class="bg-white rounded-xl w-full max-w-xl p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="font-semibold text-lg mb-4">New Control</h3>
            <form method="POST" action="{{ route('controls.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @csrf
                <x-field name="clause" required placeholder="A.5.1"/>
                <x-field name="framework" type="select" :options="['ISO 27001:2022','ISO 27019:2024','NIST CSF 2.0','SOC 2','IEC 62443-2-1:2024']" required/>
                <x-field name="title" :col="2" required/>
                <x-field name="theme" type="select" :options="['Organizational','People','Physical','Technological']" required/>
                <x-field name="description" type="textarea" :col="2" required/>
                <x-field name="implementation_guidance" label="Implementation Guidance" type="textarea" :col="2"/>
                <div class="sm:col-span-2 flex justify-end gap-2 mt-2">
                    <button type="button" @click="create=false" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                    <button class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm">Create</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
