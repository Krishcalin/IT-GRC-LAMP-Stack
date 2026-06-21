@extends('layouts.app')
@section('title', 'Dashboard')
@section('content')
@php
    $cards = [
        ['Compliance', $h['compliance'], 'brand', 'SoA fully-implemented'],
        ['ISMS Conformity', $h['conformity'], 'green', 'Clauses 4–10 conformant'],
        ['Document Readiness', $h['readiness'], 'amber', 'Mandatory docs approved'],
        ['Training Completion', $h['training'], 'cyan', 'Records completed'],
    ];
    $cardCls = ['brand' => 'text-brand-600', 'green' => 'text-green-600', 'amber' => 'text-amber-600', 'cyan' => 'text-cyan-600'];
@endphp
<div class="space-y-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($cards as [$label, $val, $color, $hint])
            <div class="bg-white rounded-xl border border-gray-200 p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</p>
                <p class="text-3xl font-bold {{ $cardCls[$color] }} mt-1">{{ $val }}%</p>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2"><div class="h-1.5 rounded-full bg-{{ $color === 'brand' ? 'brand' : $color }}-500" style="width: {{ min(100, $val) }}%"></div></div>
                <p class="text-[11px] text-gray-400 mt-1.5">{{ $hint }}</p>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-2xl font-bold text-gray-900">{{ $h['implemented'] }}/{{ $h['total_controls'] }}</p><p class="text-xs text-gray-400">Controls implemented</p></div>
        <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-2xl font-bold text-amber-600">{{ $h['open_risks'] }}</p><p class="text-xs text-gray-400">Open risks</p></div>
        <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-2xl font-bold text-red-600">{{ $h['critical_risks'] }}</p><p class="text-xs text-gray-400">Critical risks</p></div>
        <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-2xl font-bold text-gray-900">{{ $h['open_findings'] }}</p><p class="text-xs text-gray-400">Open findings</p></div>
        <div class="bg-white rounded-xl border border-gray-200 p-4"><p class="text-2xl font-bold {{ $h['overdue_tasks'] ? 'text-red-600' : 'text-gray-900' }}">{{ $h['open_tasks'] }} <span class="text-sm text-gray-400">/ {{ $h['overdue_tasks'] }} overdue</span></p><p class="text-xs text-gray-400">Open tasks</p></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <x-card class="p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Controls by status</h3>
            <canvas id="byStatus" height="180"></canvas>
        </x-card>
        <x-card class="p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Annex A by theme</h3>
            <canvas id="byTheme" height="180"></canvas>
        </x-card>
        <x-card class="p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Risk heatmap (inherent)</h3>
            <x-heatmap :heat="$heat"/>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
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
        <x-card class="p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Open incidents</h3>
            @forelse($openIncidents as $i)
                <div class="flex justify-between text-sm py-1 border-b border-gray-50 last:border-0">
                    <span>{{ \Illuminate\Support\Str::limit($i->title, 32) }}</span><x-badge :value="$i->severity"/>
                </div>
            @empty<p class="text-sm text-gray-400">No open incidents.</p>@endforelse
        </x-card>
        <x-card class="p-5">
            <h3 class="font-semibold text-gray-900 mb-3">Recent activity</h3>
            <div class="space-y-1.5 text-sm">
                @forelse($recent as $a)
                    <div class="flex justify-between text-gray-600"><span>{{ $a->action }} <span class="text-gray-400">{{ $a->resource_type }}</span></span><span class="text-xs text-gray-300">{{ optional($a->created_at)->diffForHumans() }}</span></div>
                @empty<p class="text-gray-400">No activity yet.</p>@endforelse
            </div>
        </x-card>
    </div>
</div>

<script>
    new Chart(document.getElementById('byStatus'), {
        type: 'doughnut',
        data: {
            labels: @json($byStatus->keys()),
            datasets: [{ data: @json($byStatus->values()), backgroundColor: ['#9ca3af', '#60a5fa', '#22c55e', '#e5e7eb'] }]
        },
        options: { plugins: { legend: { position: 'bottom' } } }
    });
    new Chart(document.getElementById('byTheme'), {
        type: 'bar',
        data: {
            labels: @json($byTheme->keys()),
            datasets: [{ label: 'Controls', data: @json($byTheme->values()), backgroundColor: '#6366f1' }]
        },
        options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });
</script>
@endsection
