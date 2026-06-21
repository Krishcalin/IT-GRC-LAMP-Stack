@extends('layouts.app')
@section('title', 'Analytics')
@section('content')
<div class="space-y-6">
    <h2 class="text-xl font-semibold text-gray-900">Analytics</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <x-card class="p-5">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-900">Risk Heatmap</h3>
                <div class="flex gap-1 text-xs">
                    <a href="{{ route('analytics.index', ['basis' => 'inherent']) }}" class="px-3 py-1 rounded-lg {{ $basis === 'inherent' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600' }}">Inherent</a>
                    <a href="{{ route('analytics.index', ['basis' => 'residual']) }}" class="px-3 py-1 rounded-lg {{ $basis === 'residual' ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600' }}">Residual</a>
                </div>
            </div>
            <x-heatmap :heat="$heat"/>
        </x-card>

        <x-card class="p-5">
            <h3 class="font-semibold text-gray-900 mb-3">My Work</h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-2xl font-bold text-brand-600">{{ $myWork['open_tasks'] }}</span><p class="text-gray-400 text-xs">Open tasks</p></div>
                <div><span class="text-2xl font-bold text-red-600">{{ $myWork['overdue_tasks'] }}</span><p class="text-gray-400 text-xs">Overdue</p></div>
                <div><span class="text-2xl font-bold text-amber-600">{{ $myWork['pending_approvals'] }}</span><p class="text-gray-400 text-xs">Pending approvals</p></div>
                <div><span class="text-2xl font-bold text-gray-900">{{ $myWork['owned_controls'] }}</span><p class="text-gray-400 text-xs">Owned controls</p></div>
                <div><span class="text-2xl font-bold text-gray-900">{{ $myWork['owned_risks'] }}</span><p class="text-gray-400 text-xs">Owned risks</p></div>
                <div><span class="text-2xl font-bold text-gray-900">{{ $myWork['assigned_findings'] }}</span><p class="text-gray-400 text-xs">Assigned findings</p></div>
            </div>
        </x-card>
    </div>

    <x-card class="p-5">
        <h3 class="font-semibold text-gray-900 mb-3">ISMS Posture Trend</h3>
        @if($trend->count() > 1)
            <canvas id="trend" height="80"></canvas>
            <script>
                new Chart(document.getElementById('trend'), {
                    type: 'line',
                    data: {
                        labels: @json($trend->pluck('snapshot_date')->map(fn ($d) => $d->format('Y-m-d'))),
                        datasets: [
                            { label: 'Compliance', data: @json($trend->pluck('compliance_score')), borderColor: '#6366f1', tension: .3 },
                            { label: 'Conformity', data: @json($trend->pluck('isms_conformity_score')), borderColor: '#22c55e', tension: .3 },
                            { label: 'Doc readiness', data: @json($trend->pluck('document_readiness_score')), borderColor: '#f59e0b', tension: .3 },
                            { label: 'Training', data: @json($trend->pluck('training_completion_rate')), borderColor: '#06b6d4', tension: .3 }
                        ]
                    },
                    options: { plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true, max: 100 } } }
                });
            </script>
        @else
            <p class="text-sm text-gray-400">Trend builds as daily snapshots accumulate (the dashboard captures one per day).</p>
        @endif
    </x-card>
</div>
@endsection
