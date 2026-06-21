@extends('layouts.app')
@section('title', 'Clause ' . $clause->clause)
@section('content')
@php $sel = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm'; @endphp
<div class="space-y-4 max-w-4xl">
    <a href="{{ route('clauses.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back to clauses</a>
    <div class="flex items-center gap-2">
        <span class="font-mono text-lg font-bold text-brand-600">{{ $clause->clause }}</span>
        <x-badge :value="$clause->section"/>
        <x-badge :value="$clause->conformity_status"/>
    </div>
    <h2 class="text-xl font-semibold text-gray-900">{{ $clause->title }}</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-card class="lg:col-span-2 p-5">
            <h3 class="font-semibold text-gray-900 mb-2">Requirement</h3>
            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $clause->requirement }}</p>
            @if($clause->documented_info)
                <h3 class="font-semibold text-gray-900 mb-2 mt-4">Mandatory documented information</h3>
                <p class="text-sm text-gray-600">{{ $clause->documented_info }}</p>
            @endif
        </x-card>
        <x-card class="p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Conformity</h3>
            <form method="POST" action="{{ route('clauses.update', $clause) }}" class="space-y-3">
                @csrf @method('PUT')
                <x-field name="conformity_status" type="select" :value="$clause->conformity_status" :options="['Not Assessed','In Progress','Partially Conformant','Conformant','Nonconformant']" required/>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Owner</label>
                    <select name="owner_id" class="{{ $sel }}"><option value="">— Unassigned —</option>
                        @foreach($users as $u)<option value="{{ $u->id }}" @selected($clause->owner_id === $u->id)>{{ $u->full_name }}</option>@endforeach
                    </select>
                </div>
                <x-field name="review_date" label="Review Date" type="date" :value="optional($clause->review_date)->format('Y-m-d')"/>
                <x-field name="implementation_notes" label="Notes" type="textarea" :value="$clause->implementation_notes"/>
                <button class="w-full bg-brand-600 text-white py-2 rounded-lg text-sm">Save</button>
            </form>
        </x-card>
    </div>
</div>
@endsection
