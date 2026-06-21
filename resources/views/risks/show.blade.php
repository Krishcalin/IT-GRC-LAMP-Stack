@extends('layouts.app')
@section('title', 'Risk ' . $risk->ref_id)
@section('content')
@php $sel = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('risks.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back to risks</a>
        <div class="flex gap-2">
            <a href="{{ route('risks.edit', $risk) }}" class="text-sm bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">Edit</a>
            <form method="POST" action="{{ route('risks.destroy', $risk) }}" onsubmit="return confirm('Delete this risk?')">
                @csrf @method('DELETE')
                <button class="text-sm bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg">Delete</button>
            </form>
        </div>
    </div>

    <div class="flex items-center gap-2">
        <span class="font-mono text-lg font-bold text-brand-600">{{ $risk->ref_id }}</span>
        <x-badge :value="$risk->inherent_risk_level"/>
        <x-badge :value="$risk->status"/>
    </div>
    <h2 class="text-xl font-semibold text-gray-900">{{ $risk->title }}</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <x-card class="p-5">
                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $risk->description }}</p>
                <div class="grid grid-cols-2 gap-3 mt-4 text-sm">
                    <div><span class="text-gray-400">Category</span><br>{{ $risk->category }}</div>
                    <div><span class="text-gray-400">Treatment</span><br>{{ $risk->treatment }}</div>
                    <div><span class="text-gray-400">Owner</span><br>{{ $risk->owner?->full_name ?? '—' }}</div>
                    <div><span class="text-gray-400">Review date</span><br>{{ optional($risk->review_date)->format('Y-m-d') ?? '—' }}</div>
                </div>
                @if($risk->treatment_plan)
                    <div class="mt-4 text-sm"><span class="text-gray-400">Treatment plan</span><p class="text-gray-600 whitespace-pre-line mt-1">{{ $risk->treatment_plan }}</p></div>
                @endif
            </x-card>

            <x-card class="p-5" x-data="{ add:false }">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900">Linked Controls</h3>
                    <button @click="add=!add" class="text-xs text-brand-600">+ Link control</button>
                </div>
                @forelse($risk->controls as $c)
                    <div class="flex items-center justify-between text-sm border border-gray-100 rounded-lg px-3 py-2 mb-1">
                        <a href="{{ route('controls.show', $c) }}" class="hover:text-brand-600"><span class="font-mono font-semibold text-brand-600">{{ $c->clause }}</span> {{ \Illuminate\Support\Str::limit($c->title, 50) }}</a>
                        <form method="POST" action="{{ route('risks.controls.destroy', [$risk, $c]) }}">@csrf @method('DELETE')<button class="text-gray-300 hover:text-red-500 text-xs">✕</button></form>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No linked controls.</p>
                @endforelse
                <form x-show="add" x-cloak method="POST" action="{{ route('risks.controls.store', $risk) }}" class="mt-3 flex gap-2 border-t border-gray-100 pt-3">
                    @csrf
                    <select name="control_id" required class="{{ $sel }}">
                        <option value="">Select control…</option>
                        @foreach($controls as $c)<option value="{{ $c->id }}">{{ $c->clause }} — {{ \Illuminate\Support\Str::limit($c->title, 40) }}</option>@endforeach
                    </select>
                    <button class="bg-brand-600 text-white px-4 rounded-lg text-sm">Link</button>
                </form>
            </x-card>
        </div>

        <div class="space-y-4">
            <x-card class="p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Risk Scoring (5×5)</h3>
                <div class="text-sm space-y-2">
                    <div class="flex justify-between"><span class="text-gray-400">Inherent</span><span>{{ $risk->likelihood }} × {{ $risk->impact }} = <b>{{ $risk->likelihood * $risk->impact }}</b> <x-badge :value="$risk->inherent_risk_level"/></span></div>
                    @if($risk->residual_risk_level)
                        <div class="flex justify-between"><span class="text-gray-400">Residual</span><span>{{ $risk->residual_likelihood }} × {{ $risk->residual_impact }} = <b>{{ $risk->residual_likelihood * $risk->residual_impact }}</b> <x-badge :value="$risk->residual_risk_level"/></span></div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
