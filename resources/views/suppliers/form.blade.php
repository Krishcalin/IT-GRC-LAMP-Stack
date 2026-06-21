@extends('layouts.app')
@section('title', $item->exists ? 'Edit Supplier' : 'New Supplier')
@section('content')
<div class="max-w-3xl space-y-4">
    <a href="{{ route('suppliers.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back</a>
    <x-card class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $item->exists ? 'Edit '.$item->ref_id : 'New Supplier' }}</h2>
        <form method="POST" action="{{ $item->exists ? route('suppliers.update', $item) : route('suppliers.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @if($item->exists) @method('PUT') @endif
            <x-field name="name" :value="$item->name" :col="2" required/>
            <x-field name="category" type="select" :value="$item->category" :options="['Product','Service','ICT Supply Chain','Cloud Service']" required/>
            <x-field name="criticality" type="select" :value="$item->criticality" :options="['Low','Medium','High','Critical']" required/>
            <x-field name="data_classification" label="Data Classification" type="select" :value="$item->data_classification" :options="['Public','Internal','Confidential','Restricted']" required/>
            <x-field name="status" type="select" :value="$item->status" :options="['Active','Onboarding','Under Review','Offboarded']" required/>
            <x-field name="certifications" :value="$item->certifications" placeholder="ISO 27001, SOC 2…"/>
            <x-owner-select :users="$users" :selected="$item->owner_id"/>
            <x-field name="contract_start" label="Contract Start" type="date" :value="optional($item->contract_start)->format('Y-m-d')"/>
            <x-field name="contract_end" label="Contract End" type="date" :value="optional($item->contract_end)->format('Y-m-d')"/>
            <x-field name="next_review_date" label="Next Review" type="date" :value="optional($item->next_review_date)->format('Y-m-d')"/>
            <x-field name="is_requirements_agreed" label="IS Requirements Agreed (5.20)" type="checkbox" :value="$item->is_requirements_agreed"/>
            <x-field name="right_to_audit" label="Right to Audit" type="checkbox" :value="$item->right_to_audit"/>
            <x-field name="processes_pii" label="Processes PII" type="checkbox" :value="$item->processes_pii"/>
            <x-field name="service_description" label="Service Description" type="textarea" :value="$item->service_description" :col="2"/>
            <x-field name="notes" type="textarea" :value="$item->notes" :col="2"/>
            <div class="sm:col-span-2 flex justify-end"><button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">{{ $item->exists ? 'Save' : 'Create' }}</button></div>
        </form>
    </x-card>
</div>
@endsection
