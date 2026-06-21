@extends('layouts.app')
@section('title', $item->exists ? 'Edit Incident' : 'Log Incident')
@section('content')
@php $sel = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm'; @endphp
<div class="max-w-3xl space-y-4">
    <a href="{{ route('incidents.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back</a>
    <x-card class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $item->exists ? 'Edit '.$item->ref_id : 'Log Incident' }}</h2>
        <form method="POST" action="{{ $item->exists ? route('incidents.update', $item) : route('incidents.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @if($item->exists) @method('PUT') @endif
            <x-field name="title" :value="$item->title" :col="2" required/>
            <x-field name="category" type="select" :value="$item->category" :options="['Malware','Phishing','Unauthorized Access','Data Breach','DoS','Misconfiguration','Lost/Stolen Device','Insider','Other']" required/>
            <x-field name="severity" type="select" :value="$item->severity" :options="['Low','Medium','High','Critical']" required/>
            <x-field name="status" type="select" :value="$item->status" :options="['New','Triaged','In Progress','Resolved','Closed']" required/>
            <x-field name="reporter" :value="$item->reporter"/>
            <x-owner-select :users="$users" :selected="$item->owner_id" label="Handler"/>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Related Risk</label>
                <select name="risk_id" class="{{ $sel }}"><option value="">— None —</option>
                    @foreach($risks as $rk)<option value="{{ $rk->id }}" @selected($item->risk_id === $rk->id)>{{ $rk->ref_id }} — {{ \Illuminate\Support\Str::limit($rk->title, 30) }}</option>@endforeach</select>
            </div>
            <x-field name="data_breach" label="Personal-data / reportable breach" type="checkbox" :value="$item->data_breach"/>
            <x-field name="description" type="textarea" :value="$item->description" :col="2"/>
            <x-field name="affected_assets" label="Affected Assets" type="textarea" :value="$item->affected_assets" :col="2"/>
            <x-field name="containment_actions" label="Containment (5.26)" type="textarea" :value="$item->containment_actions" :col="2"/>
            <x-field name="root_cause" label="Root Cause (5.27)" type="textarea" :value="$item->root_cause"/>
            <x-field name="lessons_learned" label="Lessons Learned (5.27)" type="textarea" :value="$item->lessons_learned"/>
            <x-field name="evidence_notes" label="Evidence Notes (5.28)" type="textarea" :value="$item->evidence_notes" :col="2"/>
            <div class="sm:col-span-2 flex justify-end"><button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">{{ $item->exists ? 'Save' : 'Log' }}</button></div>
        </form>
    </x-card>
</div>
@endsection
