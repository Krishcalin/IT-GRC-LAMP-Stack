@extends('layouts.app')
@section('title', $item->exists ? 'Edit Audit' : 'New Audit')
@section('content')
<div class="max-w-3xl space-y-4">
    <a href="{{ $item->exists ? route('audits.show', $item) : route('audits.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back</a>
    <x-card class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $item->exists ? 'Edit '.$item->ref_id : 'New Audit' }}</h2>
        <form method="POST" action="{{ $item->exists ? route('audits.update', $item) : route('audits.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @if($item->exists) @method('PUT') @endif
            <x-field name="title" :value="$item->title" :col="2" required/>
            <x-field name="audit_type" label="Type" type="select" :value="$item->audit_type" :options="['Internal','External','Surveillance']" required/>
            <x-field name="status" type="select" :value="$item->status" :options="['Planned','In Progress','Completed','Cancelled']" required/>
            <x-owner-select :users="$users" :selected="$item->lead_auditor_id" name="lead_auditor_id" label="Lead Auditor"/>
            <x-field name="start_date" label="Start Date" type="date" :value="optional($item->start_date)->format('Y-m-d')"/>
            <x-field name="end_date" label="End Date" type="date" :value="optional($item->end_date)->format('Y-m-d')"/>
            <x-field name="scope" type="textarea" :value="$item->scope" :col="2"/>
            <x-field name="conclusion" type="textarea" :value="$item->conclusion" :col="2"/>
            <x-field name="description" type="textarea" :value="$item->description" :col="2"/>
            <div class="sm:col-span-2 flex justify-end"><button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">{{ $item->exists ? 'Save' : 'Create' }}</button></div>
        </form>
    </x-card>
</div>
@endsection
