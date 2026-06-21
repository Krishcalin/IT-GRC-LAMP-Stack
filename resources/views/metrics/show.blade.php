@extends('layouts.app')
@section('title', 'Metric ' . $metric->ref_id)
@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('metrics.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back to metrics</a>
        <a href="{{ route('metrics.edit', $metric) }}" class="text-sm bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">Edit</a>
    </div>
    <div class="flex items-center gap-2">
        <span class="font-mono text-lg font-bold text-brand-600">{{ $metric->ref_id }}</span>
        <x-badge :value="$metric->metric_type"/><x-badge :value="$metric->rag"/>
    </div>
    <h2 class="text-xl font-semibold text-gray-900">{{ $metric->name }}</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-card class="lg:col-span-2 p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Trend</h3>
            @if($metric->measurements->isNotEmpty())
                <canvas id="trend" height="110"></canvas>
                <script>
                    new Chart(document.getElementById('trend'), {
                        type: 'line',
                        data: {
                            labels: @json($metric->measurements->pluck('captured_at')->map(fn ($d) => $d->format('Y-m-d'))),
                            datasets: [
                                { label: 'Actual', data: @json($metric->measurements->pluck('value')), borderColor: '#6366f1', backgroundColor: 'rgba(99,102,241,.1)', tension: .3, fill: true },
                                { label: 'Target', data: @json($metric->measurements->map(fn () => $metric->target_value)), borderColor: '#f59e0b', borderDash: [5,5], pointRadius: 0 }
                            ]
                        },
                        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
                    });
                </script>
            @else
                <p class="text-sm text-gray-400">No measurements yet.</p>
            @endif
        </x-card>
        <div class="space-y-4">
            <x-card class="p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Record measurement</h3>
                <form method="POST" action="{{ route('metrics.measurements.store', $metric) }}" class="space-y-3">
                    @csrf
                    <x-field name="value" type="number" required/>
                    <x-field name="captured_at" label="Captured at" type="date"/>
                    <x-field name="note" type="textarea"/>
                    <button class="w-full bg-brand-600 text-white py-2 rounded-lg text-sm">Add measurement</button>
                </form>
            </x-card>
            <x-card class="p-5 text-sm text-gray-600 space-y-1.5">
                <div><span class="text-gray-400">Target:</span> {{ $metric->target_value }} {{ $metric->unit }}</div>
                <div><span class="text-gray-400">Current:</span> {{ $metric->current_value }} {{ $metric->unit }}</div>
                <div><span class="text-gray-400">Direction:</span> {{ $metric->direction }}</div>
                <div><span class="text-gray-400">Frequency:</span> {{ $metric->frequency ?? '—' }}</div>
                <div><span class="text-gray-400">Objective:</span> {{ $metric->objective?->ref_id ?? '—' }}</div>
            </x-card>
        </div>
    </div>
</div>
@endsection
