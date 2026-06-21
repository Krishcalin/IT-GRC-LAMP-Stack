@extends('layouts.app')
@section('title', $item->exists ? 'Edit Campaign' : 'New Campaign')
@section('content')
<div class="max-w-3xl space-y-4">
    <a href="{{ $item->exists ? route('training.show', $item) : route('training.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back</a>
    <x-card class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $item->exists ? 'Edit '.$item->ref_id : 'New Campaign' }}</h2>
        <form method="POST" action="{{ $item->exists ? route('training.update', $item) : route('training.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @if($item->exists) @method('PUT') @endif
            <x-field name="title" :value="$item->title" :col="2" required/>
            <x-field name="training_type" label="Type" type="select" :value="$item->training_type" :options="['Awareness Campaign','Onboarding','Role-based Training','Phishing Simulation','Policy Acknowledgment','Other']" required/>
            <x-field name="status" type="select" :value="$item->status" :options="['Planned','In Progress','Completed','Cancelled']" required/>
            <x-field name="topic" :value="$item->topic"/>
            <x-field name="audience" :value="$item->audience"/>
            <x-owner-select :users="$users" :selected="$item->owner_id"/>
            <x-field name="materials_link" label="Materials Link" :value="$item->materials_link"/>
            <x-field name="start_date" label="Start" type="date" :value="optional($item->start_date)->format('Y-m-d')"/>
            <x-field name="end_date" label="End" type="date" :value="optional($item->end_date)->format('Y-m-d')"/>
            <x-field name="description" type="textarea" :value="$item->description" :col="2"/>
            <div class="sm:col-span-2 flex justify-end"><button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">{{ $item->exists ? 'Save' : 'Create' }}</button></div>
        </form>
    </x-card>
</div>
@endsection
