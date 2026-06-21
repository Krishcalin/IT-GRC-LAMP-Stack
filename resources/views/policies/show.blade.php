@extends('layouts.app')
@section('title', 'Policy ' . $policy->ref_id)
@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('policies.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back to policies</a>
        <div class="flex gap-2">
            <a href="{{ route('policies.edit', $policy) }}" class="text-sm bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">Edit</a>
            <form method="POST" action="{{ route('policies.destroy', $policy) }}" onsubmit="return confirm('Delete this policy?')">@csrf @method('DELETE')<button class="text-sm bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg">Delete</button></form>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <span class="font-mono text-lg font-bold text-brand-600">{{ $policy->ref_id }}</span>
        <x-badge :value="$policy->status"/><x-badge :value="$policy->category"/>
        <span class="text-xs text-gray-400">v{{ $policy->version }}</span>
    </div>
    <h2 class="text-xl font-semibold text-gray-900">{{ $policy->title }}</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-card class="lg:col-span-2 p-6">
            @if($policy->description)<p class="text-sm text-gray-600 mb-4">{{ $policy->description }}</p>@endif
            <div class="prose prose-sm max-w-none text-gray-700 whitespace-pre-line border-t border-gray-100 pt-4">{{ $policy->content ?: 'No content yet.' }}</div>
        </x-card>
        <div class="space-y-4">
            <x-card class="p-5">
                <h3 class="font-semibold text-gray-900 mb-2">Details</h3>
                <div class="text-sm space-y-1.5 text-gray-600">
                    <div><span class="text-gray-400">Owner:</span> {{ $policy->owner?->full_name ?? '—' }}</div>
                    <div><span class="text-gray-400">Effective:</span> {{ optional($policy->effective_date)->format('Y-m-d') ?? '—' }}</div>
                    <div><span class="text-gray-400">Next review:</span> {{ optional($policy->next_review_date)->format('Y-m-d') ?? '—' }}</div>
                    <div><span class="text-gray-400">Approved by:</span> {{ $policy->approver?->full_name ?? '—' }}</div>
                </div>
            </x-card>
            <x-card class="p-5">
                <h3 class="font-semibold text-gray-900 mb-2">Acknowledgments ({{ $policy->acknowledgments->count() }})</h3>
                @unless($acknowledged)
                    <form method="POST" action="{{ route('policies.acknowledge', $policy) }}" class="mb-3">@csrf
                        <button class="w-full bg-brand-600 text-white py-2 rounded-lg text-sm">I acknowledge this policy</button></form>
                @else
                    <p class="text-xs text-green-600 mb-3">✓ You have acknowledged this policy.</p>
                @endunless
                <div class="space-y-1 text-sm text-gray-500 max-h-48 overflow-y-auto">
                    @forelse($policy->acknowledgments as $ack)
                        <div class="flex justify-between"><span>{{ $ack->user?->full_name ?? '—' }}</span><span class="text-xs text-gray-400">{{ optional($ack->acknowledged_at)->format('Y-m-d') }}</span></div>
                    @empty<p class="text-gray-400">No acknowledgments yet.</p>@endforelse
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
