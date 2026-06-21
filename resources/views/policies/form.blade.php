@extends('layouts.app')
@section('title', $item->exists ? 'Edit Policy' : 'New Policy')
@section('content')
<div class="max-w-3xl space-y-4">
    <a href="{{ $item->exists ? route('policies.show', $item) : route('policies.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back</a>
    <x-card class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $item->exists ? 'Edit '.$item->ref_id : 'New Policy' }}</h2>
        <form method="POST" action="{{ $item->exists ? route('policies.update', $item) : route('policies.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @if($item->exists) @method('PUT') @endif
            <x-field name="title" :value="$item->title" :col="2" required/>
            <x-field name="category" type="select" :value="$item->category" :options="['Information Security','Access Control','Data Protection','Acceptable Use','Business Continuity','Supplier Management','HR Security','Operations']" required/>
            <x-field name="status" type="select" :value="$item->status" :options="['Draft','Under Review','Approved','Retired']" required/>
            <x-field name="version" :value="$item->version"/>
            <x-owner-select :users="$users" :selected="$item->owner_id"/>
            <x-field name="effective_date" label="Effective Date" type="date" :value="optional($item->effective_date)->format('Y-m-d')"/>
            <x-field name="next_review_date" label="Next Review" type="date" :value="optional($item->next_review_date)->format('Y-m-d')"/>
            <x-field name="description" type="textarea" :value="$item->description" :col="2"/>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Content (Markdown)</label>
                <textarea name="content" rows="10" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono">{{ old('content', $item->content) }}</textarea>
            </div>
            <div class="sm:col-span-2 flex justify-end"><button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">{{ $item->exists ? 'Save' : 'Create' }}</button></div>
        </form>
    </x-card>
</div>
@endsection
