@extends('layouts.app')
@section('title', 'SoA — ' . $control->clause)
@section('content')
@php $sel = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm'; $e = $control->soaEntry; @endphp
<div class="max-w-2xl space-y-4">
    <a href="{{ route('soa.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back to SoA</a>
    <x-card class="p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-1"><span class="font-mono text-brand-600">{{ $control->clause }}</span> {{ $control->title }}</h2>
        <p class="text-xs text-gray-400 mb-4">{{ $control->framework }}</p>
        <form method="POST" action="{{ route('soa.update', $control) }}" class="space-y-4">
            @csrf @method('PUT')
            @php $appVal = $e ? (int) $e->applicable : 1; @endphp
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Applicable <span class="text-red-500">*</span></label>
                <select name="applicable" required class="{{ $sel }}">
                    <option value="1" @selected((int) old('applicable', $appVal) === 1)>Applicable</option>
                    <option value="0" @selected((int) old('applicable', $appVal) === 0)>Not Applicable</option>
                </select>
            </div>
            <x-field name="implementation_status" type="select" :value="$e->implementation_status ?? 'Not Implemented'" :options="['Not Implemented','Partially','Fully','N/A']" required/>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Responsible</label>
                <select name="responsible_id" class="{{ $sel }}"><option value="">— Unassigned —</option>
                    @foreach($users as $u)<option value="{{ $u->id }}" @selected(($e->responsible_id ?? null) === $u->id)>{{ $u->full_name }}</option>@endforeach
                </select>
            </div>
            <x-field name="justification" type="textarea" :value="$e->justification ?? null" placeholder="Required when not applicable"/>
            <x-field name="implementation_evidence" label="Implementation Evidence" type="textarea" :value="$e->implementation_evidence ?? null"/>
            <x-field name="notes" type="textarea" :value="$e->notes ?? null"/>
            <button class="bg-brand-600 text-white px-5 py-2 rounded-lg text-sm">Save SoA entry</button>
        </form>
    </x-card>
</div>
@endsection
