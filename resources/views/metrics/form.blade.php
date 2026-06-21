@extends('layouts.app')
@section('title', $item->exists ? 'Edit Metric' : 'New Metric')
@section('content')
@php $sel = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm'; @endphp
<div class="max-w-3xl space-y-4">
    <a href="{{ route('metrics.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back</a>
    <x-card class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $item->exists ? 'Edit '.$item->ref_id : 'New Metric' }}</h2>
        <form method="POST" action="{{ $item->exists ? route('metrics.update', $item) : route('metrics.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @if($item->exists) @method('PUT') @endif
            <x-field name="name" :value="$item->name" :col="2" required/>
            <x-field name="metric_type" label="Type" type="select" :value="$item->metric_type" :options="['KPI','KRI','KCI']" required/>
            <x-field name="direction" type="select" :value="$item->direction" :options="['higher_is_better' => 'Higher is better', 'lower_is_better' => 'Lower is better']" required/>
            <x-field name="target_value" label="Target" type="number" :value="$item->target_value"/>
            <x-field name="current_value" label="Current" type="number" :value="$item->current_value"/>
            <x-field name="unit" :value="$item->unit"/>
            <x-field name="frequency" type="select" :value="$item->frequency" :options="['', 'Monthly', 'Quarterly', 'Annual', 'Continuous']"/>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Objective</label>
                <select name="objective_id" class="{{ $sel }}"><option value="">— None —</option>
                    @foreach($objectives as $o)<option value="{{ $o->id }}" @selected($item->objective_id === $o->id)>{{ $o->ref_id }} — {{ \Illuminate\Support\Str::limit($o->title, 30) }}</option>@endforeach</select>
            </div>
            <x-owner-select :users="$users" :selected="$item->owner_id"/>
            <x-field name="description" type="textarea" :value="$item->description" :col="2"/>
            <div class="sm:col-span-2 flex justify-end"><button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">{{ $item->exists ? 'Save' : 'Create' }}</button></div>
        </form>
    </x-card>
</div>
@endsection
