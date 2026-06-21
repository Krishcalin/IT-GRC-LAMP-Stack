@extends('layouts.app')
@section('title', 'Assessment ' . $assessment->ref_id)
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-2 py-1.5 text-sm bg-white'; $results = ['', 'Compliant', 'Partial', 'Non-Compliant', 'N/A', 'Yes', 'No']; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('assessments.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back to assessments</a>
        <div class="flex gap-2">
            <a href="{{ route('assessments.edit', $assessment) }}" class="text-sm bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">Edit</a>
            <form method="POST" action="{{ route('assessments.destroy', $assessment) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-sm bg-red-50 text-red-600 px-4 py-2 rounded-lg">Delete</button></form>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <span class="font-mono text-lg font-bold text-brand-600">{{ $assessment->ref_id }}</span>
        <x-badge :value="$assessment->status"/>
        <span class="text-sm text-gray-500">Score: <b>{{ $assessment->score }}%</b> · {{ $assessment->answered_count }}/{{ $assessment->item_count }} answered</span>
    </div>
    <h2 class="text-xl font-semibold text-gray-900">{{ $assessment->title }}</h2>
    <p class="text-xs text-gray-400">{{ $assessment->assessment_type }} @if($assessment->framework)· {{ $assessment->framework }}@endif @if($assessment->supplier)· {{ $assessment->supplier->name }}@endif</p>

    <div class="flex flex-wrap gap-3">
        <form method="POST" action="{{ route('assessments.populate', $assessment) }}" class="flex gap-2 items-center bg-white border border-gray-200 rounded-lg px-3 py-2">
            @csrf
            <span class="text-sm text-gray-500">Populate from</span>
            <select name="framework" required class="{{ $sel }}">
                @foreach(['ISO 27001:2022','ISO 27019:2024','NIST CSF 2.0','SOC 2','IEC 62443-2-1:2024'] as $f)<option value="{{ $f }}">{{ $f }}</option>@endforeach
            </select>
            <button class="bg-brand-600 text-white px-3 py-1.5 rounded-lg text-sm">Add controls</button>
        </form>
    </div>

    <div class="space-y-2">
        @forelse($assessment->items as $it)
            <x-card class="p-3">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="text-sm">
                        <span class="font-semibold text-brand-600">{{ $it->control?->clause }}</span>
                        <span class="text-gray-600">{{ $it->question ?? $it->control?->title }}</span>
                        @if($it->response)<p class="text-xs text-gray-400 mt-0.5">{{ $it->response }}</p>@endif
                    </div>
                    <form method="POST" action="{{ route('assessments.items.destroy', [$assessment, $it]) }}">@csrf @method('DELETE')<button class="text-gray-300 hover:text-red-500 text-xs">✕</button></form>
                </div>
                <form method="POST" action="{{ route('assessments.items.update', [$assessment, $it]) }}" class="flex flex-wrap gap-2 items-center">
                    @csrf @method('PUT')
                    <select name="maturity" class="{{ $sel }}"><option value="">Maturity —</option>
                        @for($m = 0; $m <= 5; $m++)<option value="{{ $m }}" @selected($it->maturity === $m)>{{ $m }}</option>@endfor</select>
                    <select name="result" class="{{ $sel }}">@foreach($results as $rr)<option value="{{ $rr }}" @selected($it->result === $rr)>{{ $rr === '' ? 'Result —' : $rr }}</option>@endforeach</select>
                    <input name="comment" value="{{ $it->comment }}" placeholder="Comment" class="{{ $sel }} flex-1 min-w-[200px]">
                    <button class="bg-gray-800 text-white px-3 py-1.5 rounded-lg text-xs">Save</button>
                </form>
            </x-card>
        @empty
            <x-card class="p-10 text-center text-gray-400">No items yet — populate from a framework or add one below.</x-card>
        @endforelse
    </div>

    <x-card class="p-4">
        <form method="POST" action="{{ route('assessments.items.store', $assessment) }}" class="flex flex-wrap gap-2 items-center">
            @csrf
            <input name="question" placeholder="Question / item…" class="{{ $sel }} flex-1 min-w-[240px]">
            <select name="result" class="{{ $sel }}">@foreach($results as $rr)<option value="{{ $rr }}">{{ $rr === '' ? 'Result —' : $rr }}</option>@endforeach</select>
            <input name="comment" placeholder="Comment" class="{{ $sel }}">
            <button class="bg-brand-600 text-white px-4 py-1.5 rounded-lg text-sm">+ Add item</button>
        </form>
    </x-card>
</div>
@endsection
