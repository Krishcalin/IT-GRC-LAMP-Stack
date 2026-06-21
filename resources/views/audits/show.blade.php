@extends('layouts.app')
@section('title', 'Audit ' . $audit->ref_id)
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-2 py-1.5 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('audits.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back to audits</a>
        <div class="flex gap-2">
            <a href="{{ route('audits.edit', $audit) }}" class="text-sm bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg">Edit</a>
            <form method="POST" action="{{ route('audits.destroy', $audit) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-sm bg-red-50 text-red-600 px-4 py-2 rounded-lg">Delete</button></form>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <span class="font-mono text-lg font-bold text-brand-600">{{ $audit->ref_id }}</span>
        <x-badge :value="$audit->audit_type"/><x-badge :value="$audit->status"/>
    </div>
    <h2 class="text-xl font-semibold text-gray-900">{{ $audit->title }}</h2>
    <p class="text-sm text-gray-500">Lead: {{ $audit->leadAuditor?->full_name ?? '—' }} · {{ optional($audit->start_date)->format('Y-m-d') }} → {{ optional($audit->end_date)->format('Y-m-d') }}</p>
    @if($audit->scope)<x-card class="p-4 text-sm"><span class="text-gray-400">Scope:</span> {{ $audit->scope }}</x-card>@endif

    <h3 class="font-semibold text-gray-900">Findings ({{ $audit->findings->count() }})</h3>
    <div class="space-y-2">
        @forelse($audit->findings as $f)
            <x-card class="p-3">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="text-sm">
                        <span class="font-mono font-semibold text-brand-600">{{ $f->ref_id }}</span>
                        <x-badge :value="$f->finding_type"/> <x-badge :value="$f->severity"/>
                        @if($f->control)<span class="text-xs text-gray-400">{{ $f->control->clause }}</span>@endif
                        <p class="text-gray-600 mt-1">{{ $f->description }}</p>
                    </div>
                    <form method="POST" action="{{ route('audits.findings.destroy', [$audit, $f]) }}">@csrf @method('DELETE')<button class="text-gray-300 hover:text-red-500 text-xs">✕</button></form>
                </div>
                <form method="POST" action="{{ route('audits.findings.update', [$audit, $f]) }}" class="flex flex-wrap gap-2 items-center">
                    @csrf @method('PUT')
                    <select name="status" class="{{ $sel }}">@foreach(['Open','In Progress','Resolved','Verified','Overdue'] as $s)<option value="{{ $s }}" @selected($f->status === $s)>{{ $s }}</option>@endforeach</select>
                    <input name="corrective_action" value="{{ $f->corrective_action }}" placeholder="Corrective action" class="{{ $sel }} flex-1 min-w-[200px]">
                    <input type="date" name="due_date" value="{{ optional($f->due_date)->format('Y-m-d') }}" class="{{ $sel }}">
                    <button class="bg-gray-800 text-white px-3 py-1.5 rounded-lg text-xs">Save</button>
                </form>
            </x-card>
        @empty
            <x-card class="p-8 text-center text-gray-400">No findings recorded.</x-card>
        @endforelse
    </div>

    <x-card class="p-4">
        <h4 class="font-semibold text-gray-900 mb-2 text-sm">Add finding</h4>
        <form method="POST" action="{{ route('audits.findings.store', $audit) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @csrf
            <select name="finding_type" required class="{{ $sel }}">@foreach(['Major NC','Minor NC','Observation','OFI'] as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach</select>
            <select name="severity" required class="{{ $sel }}">@foreach(['Low','Medium','High','Critical'] as $s)<option value="{{ $s }}" @selected($s==='Medium')>{{ $s }}</option>@endforeach</select>
            <select name="control_id" class="{{ $sel }} sm:col-span-2"><option value="">— Related control (optional) —</option>
                @foreach($controls as $c)<option value="{{ $c->id }}">{{ $c->clause }} — {{ \Illuminate\Support\Str::limit($c->title, 40) }}</option>@endforeach</select>
            <input type="hidden" name="status" value="Open">
            <textarea name="description" required rows="2" placeholder="Finding description" class="{{ $sel }} sm:col-span-2"></textarea>
            <div class="sm:col-span-2 flex justify-end"><button class="bg-brand-600 text-white px-4 py-1.5 rounded-lg text-sm">+ Add finding</button></div>
        </form>
    </x-card>
</div>
@endsection
