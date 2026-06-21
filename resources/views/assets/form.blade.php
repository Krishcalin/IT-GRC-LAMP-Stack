@extends('layouts.app')
@section('title', $item->exists ? 'Edit Asset' : 'New Asset')
@section('content')
<div class="max-w-3xl space-y-4">
    <a href="{{ route('assets.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back</a>
    <x-card class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $item->exists ? 'Edit '.$item->ref_id : 'New Asset' }}</h2>
        <form method="POST" action="{{ $item->exists ? route('assets.update', $item) : route('assets.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @if($item->exists) @method('PUT') @endif
            <x-field name="name" :value="$item->name" :col="2" required/>
            <x-field name="asset_type" label="Type" type="select" :value="$item->asset_type" :options="['Hardware','Software','Data','Service','People','Facility']" required/>
            <x-field name="classification" type="select" :value="$item->classification" :options="['Public','Internal','Confidential','Restricted']" required/>
            <x-field name="criticality" type="select" :value="$item->criticality" :options="['Low','Medium','High','Critical']" required/>
            <x-field name="status" type="select" :value="$item->status" :options="['Active','Inactive','Decommissioned']" required/>
            <x-owner-select :users="$users" :selected="$item->owner_id"/>
            <x-field name="department" :value="$item->department"/>
            <x-field name="location" :value="$item->location"/>
            <x-field name="description" type="textarea" :value="$item->description" :col="2"/>
            <div class="sm:col-span-2 flex justify-end"><button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">{{ $item->exists ? 'Save' : 'Create' }}</button></div>
        </form>
    </x-card>
</div>
@endsection
