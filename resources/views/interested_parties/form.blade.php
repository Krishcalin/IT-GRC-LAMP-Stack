@extends('layouts.app')
@section('title', $item->exists ? 'Edit Party' : 'New Party')
@section('content')
<div class="max-w-2xl space-y-4">
    <a href="{{ route('interested-parties.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back</a>
    <x-card class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $item->exists ? 'Edit '.$item->ref_id : 'New Interested Party' }}</h2>
        <form method="POST" action="{{ $item->exists ? route('interested-parties.update', $item) : route('interested-parties.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @if($item->exists) @method('PUT') @endif
            <x-field name="name" :value="$item->name" :col="2" required/>
            <x-field name="party_type" label="Type" type="select" :value="$item->party_type" :options="['Internal','External']" required/>
            <x-field name="category" type="select" :value="$item->category" :options="['Customer','Regulator','Employee','Supplier','Partner','Owner','Other']" required/>
            <x-owner-select :users="$users" :selected="$item->owner_id"/>
            <x-field name="addressed_in_isms" label="Addressed in ISMS (4.2c)" type="checkbox" :value="$item->addressed_in_isms"/>
            <x-field name="requirements" type="textarea" :value="$item->requirements" :col="2"/>
            <x-field name="notes" type="textarea" :value="$item->notes" :col="2"/>
            <div class="sm:col-span-2 flex justify-end"><button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">{{ $item->exists ? 'Save' : 'Create' }}</button></div>
        </form>
    </x-card>
</div>
@endsection
