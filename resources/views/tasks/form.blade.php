@extends('layouts.app')
@section('title', $item->exists ? 'Edit Task' : 'New Task')
@section('content')
<div class="max-w-2xl space-y-4">
    <a href="{{ route('tasks.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back</a>
    <x-card class="p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">{{ $item->exists ? 'Edit '.$item->ref_id : 'New Task' }}</h2>
        <form method="POST" action="{{ $item->exists ? route('tasks.update', $item) : route('tasks.store') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf @if($item->exists) @method('PUT') @endif
            <x-field name="title" :value="$item->title" :col="2" required/>
            <x-field name="task_type" label="Type" type="select" :value="$item->task_type" :options="['Action','Approval','Review','Remediation']" required/>
            <x-field name="priority" type="select" :value="$item->priority" :options="['Low','Medium','High','Critical']" required/>
            <x-field name="status" type="select" :value="$item->status" :options="['Open','In Progress','Blocked','Done','Cancelled']" required/>
            <x-owner-select :users="$users" :selected="$item->assignee_id" name="assignee_id" label="Assignee"/>
            <x-field name="due_date" label="Due Date" type="date" :value="optional($item->due_date)->format('Y-m-d')"/>
            <x-field name="resource_type" label="Linked Type" type="select" :value="$item->resource_type" :options="['', 'control', 'risk', 'finding', 'incident', 'document', 'supplier', 'policy', 'assessment', 'objective', 'other']"/>
            <x-field name="resource_label" label="Linked Label" :value="$item->resource_label" :col="2"/>
            <x-field name="description" type="textarea" :value="$item->description" :col="2"/>
            <div class="sm:col-span-2 flex justify-end"><button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">{{ $item->exists ? 'Save' : 'Create' }}</button></div>
        </form>
    </x-card>
</div>
@endsection
