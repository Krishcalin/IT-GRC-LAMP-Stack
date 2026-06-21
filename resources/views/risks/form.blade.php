@extends('layouts.app')
@section('title', $risk->exists ? 'Edit Risk' : 'New Risk')
@section('content')
@php $sel = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm'; @endphp
<div class="max-w-3xl space-y-4">
    <a href="{{ $risk->exists ? route('risks.show', $risk) : route('risks.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back</a>
    <x-card class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $risk->exists ? 'Edit '.$risk->ref_id : 'New Risk' }}</h2>
        <form method="POST" action="{{ $risk->exists ? route('risks.update', $risk) : route('risks.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            @if($risk->exists) @method('PUT') @endif
            <x-field name="title" :value="$risk->title" :col="2" required/>
            <x-field name="description" type="textarea" :value="$risk->description" :col="2" required/>
            <x-field name="category" type="select" :value="$risk->category" :options="['Strategic','Operational','Financial','Compliance','Technical','Reputational']" required/>
            <x-field name="status" type="select" :value="$risk->status ?? 'Open'" :options="['Open','In Treatment','Closed','Accepted']" required/>
            <x-field name="likelihood" type="number" :value="$risk->likelihood ?? 1" required placeholder="1-5"/>
            <x-field name="impact" type="number" :value="$risk->impact ?? 1" required placeholder="1-5"/>
            <x-field name="treatment" type="select" :value="$risk->treatment ?? 'Mitigate'" :options="['Mitigate','Accept','Transfer','Avoid']" required/>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Owner</label>
                <select name="owner_id" class="{{ $sel }}"><option value="">— Unassigned —</option>
                    @foreach($users as $u)<option value="{{ $u->id }}" @selected($risk->owner_id === $u->id)>{{ $u->full_name }}</option>@endforeach
                </select>
            </div>
            <x-field name="treatment_plan" label="Treatment Plan" type="textarea" :value="$risk->treatment_plan" :col="2"/>
            <x-field name="residual_likelihood" label="Residual Likelihood" type="number" :value="$risk->residual_likelihood" placeholder="1-5 (optional)"/>
            <x-field name="residual_impact" label="Residual Impact" type="number" :value="$risk->residual_impact" placeholder="1-5 (optional)"/>
            <x-field name="review_date" label="Review Date" type="date" :value="optional($risk->review_date)->format('Y-m-d')"/>
            <div class="sm:col-span-2 flex justify-end gap-2">
                <button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">{{ $risk->exists ? 'Save' : 'Create risk' }}</button>
            </div>
        </form>
    </x-card>
</div>
@endsection
