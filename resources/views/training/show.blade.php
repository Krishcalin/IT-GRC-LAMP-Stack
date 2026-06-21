@extends('layouts.app')
@section('title', 'Campaign ' . $campaign->ref_id)
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-2 py-1.5 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('training.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back to campaigns</a>
        <a href="{{ route('training.edit', $campaign) }}" class="text-sm bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">Edit</a>
    </div>
    <div class="flex items-center gap-2">
        <span class="font-mono text-lg font-bold text-brand-600">{{ $campaign->ref_id }}</span>
        <x-badge :value="$campaign->status"/>
        <span class="text-sm text-gray-500">{{ $campaign->completion_rate }}% complete ({{ $campaign->completed_participants }}/{{ $campaign->total_participants }})</span>
    </div>
    <h2 class="text-xl font-semibold text-gray-900">{{ $campaign->title }}</h2>
    <p class="text-xs text-gray-400">{{ $campaign->training_type }} @if($campaign->topic)· {{ $campaign->topic }}@endif @if($campaign->audience)· {{ $campaign->audience }}@endif</p>

    <h3 class="font-semibold text-gray-900">Participants</h3>
    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Participant</th><th class="px-4 py-3 w-40">Status</th><th class="px-4 py-3 w-24">Score</th><th class="px-4 py-3"></th></tr></thead>
            <tbody>
                @forelse($campaign->records as $rec)
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-2">{{ $rec->participant }}</td>
                        <td class="px-4 py-2" colspan="3">
                            <form method="POST" action="{{ route('training.records.update', [$campaign, $rec]) }}" class="flex flex-wrap gap-2 items-center">
                                @csrf @method('PUT')
                                <select name="status" class="{{ $sel }}">@foreach(['Assigned','Completed','Overdue','Exempt'] as $s)<option value="{{ $s }}" @selected($rec->status === $s)>{{ $s }}</option>@endforeach</select>
                                <input type="number" name="score" value="{{ $rec->score }}" placeholder="Score" class="{{ $sel }} w-24">
                                <input type="date" name="completed_at" value="{{ optional($rec->completed_at)->format('Y-m-d') }}" class="{{ $sel }}">
                                <button class="bg-gray-800 text-white px-3 py-1.5 rounded-lg text-xs">Save</button>
                            </form>
                        </td>
                    </tr>
                @empty<tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No participants yet.</td></tr>@endforelse
            </tbody>
        </table>
    </x-card>

    <x-card class="p-4">
        <form method="POST" action="{{ route('training.records.store', $campaign) }}" class="flex flex-wrap gap-2 items-center">
            @csrf
            <input name="participant" required placeholder="Participant name" class="{{ $sel }} flex-1 min-w-[200px]">
            <select name="status" class="{{ $sel }}">@foreach(['Assigned','Completed','Overdue','Exempt'] as $s)<option value="{{ $s }}">{{ $s }}</option>@endforeach</select>
            <button class="bg-brand-600 text-white px-4 py-1.5 rounded-lg text-sm">+ Add participant</button>
        </form>
    </x-card>
</div>
@endsection
