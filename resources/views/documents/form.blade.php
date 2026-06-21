@extends('layouts.app')
@section('title', $item->exists ? 'Edit Document' : 'New Document')
@section('content')
<div class="max-w-3xl space-y-4">
    <a href="{{ route('documents.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back</a>
    <x-card class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $item->exists ? 'Edit '.$item->ref_id : 'New Document' }}</h2>
        <form method="POST" action="{{ $item->exists ? route('documents.update', $item) : route('documents.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @if($item->exists) @method('PUT') @endif
            <x-field name="title" :value="$item->title" :col="2" required/>
            <x-field name="doc_type" label="Type" type="select" :value="$item->doc_type" :options="['Policy','Process','Procedure','Plan','Register','Record','Statement','Guideline']" required/>
            <x-field name="clause_ref" label="Clause Ref" :value="$item->clause_ref" placeholder="e.g. 6.1.3 / A.5.1"/>
            <x-field name="status" type="select" :value="$item->status" :options="['Draft','Under Review','Approved','Retired']" required/>
            <x-field name="classification" type="select" :value="$item->classification" :options="['Public','Internal','Confidential','Restricted']" required/>
            <x-field name="version" :value="$item->version"/>
            <x-field name="location" :value="$item->location" placeholder="Link / repository path"/>
            <x-owner-select :users="$users" :selected="$item->owner_id"/>
            <x-field name="mandatory" type="checkbox" :value="$item->mandatory" placeholder="ISO-mandated document"/>
            <x-field name="review_date" label="Review Date" type="date" :value="optional($item->review_date)->format('Y-m-d')"/>
            <x-field name="next_review_date" label="Next Review" type="date" :value="optional($item->next_review_date)->format('Y-m-d')"/>
            <x-field name="description" type="textarea" :value="$item->description" :col="2"/>
            <div class="sm:col-span-2 flex justify-end"><button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">{{ $item->exists ? 'Save' : 'Create' }}</button></div>
        </form>
    </x-card>
</div>
@endsection
