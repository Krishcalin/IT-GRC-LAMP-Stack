@extends('layouts.app')
@section('title', 'Tasks')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2"><h2 class="text-xl font-semibold text-gray-900">Workflow Tasks</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $items->total() }}</span></div>
        <a href="{{ route('tasks.create') }}" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ New Task</a>
    </div>
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="status" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Statuses</option>
            @foreach(['Open','In Progress','Blocked','Done','Cancelled'] as $s)<option value="{{ $s }}" @selected(($filters['status'] ?? '') === $s)>{{ $s }}</option>@endforeach</select>
        <select name="task_type" onchange="this.form.submit()" class="{{ $sel }}"><option value="">All Types</option>
            @foreach(['Action','Approval','Review','Remediation'] as $t)<option value="{{ $t }}" @selected(($filters['task_type'] ?? '') === $t)>{{ $t }}</option>@endforeach</select>
        <label class="inline-flex items-center gap-1 text-sm text-gray-600"><input type="checkbox" name="overdue" value="1" onchange="this.form.submit()" @checked($filters['overdue'] ?? false)> Overdue</label>
        <input name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search…" class="{{ $sel }} flex-1 min-w-[160px]">
        <button class="bg-gray-800 text-white text-sm px-4 py-2 rounded-lg">Search</button>
    </form>
    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Ref</th><th class="px-4 py-3">Title</th><th class="px-4 py-3">Type</th>
                <th class="px-4 py-3">Priority</th><th class="px-4 py-3">Due</th><th class="px-4 py-3">Assignee</th>
                <th class="px-4 py-3">Status</th><th class="px-4 py-3"></th></tr></thead>
            <tbody>
                @forelse($items as $t)
                    <tr class="border-t border-gray-100 hover:bg-brand-50/40">
                        <td class="px-4 py-2.5 font-mono font-semibold text-brand-600">{{ $t->ref_id }}</td>
                        <td class="px-4 py-2.5">{{ \Illuminate\Support\Str::limit($t->title, 40) }}@if($t->resource_label)<span class="block text-xs text-gray-400">{{ $t->resource_label }}</span>@endif</td>
                        <td class="px-4 py-2.5"><x-badge :value="$t->task_type"/></td>
                        <td class="px-4 py-2.5"><x-badge :value="$t->priority"/></td>
                        <td class="px-4 py-2.5 text-xs {{ $t->overdue ? 'text-red-600 font-semibold' : 'text-gray-400' }}">{{ optional($t->due_date)->format('Y-m-d') ?? '—' }}{{ $t->overdue ? ' ⚠' : '' }}</td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $t->assignee?->full_name ?? '—' }}</td>
                        <td class="px-4 py-2.5">@if($t->decision)<x-badge :value="$t->decision"/>@else<x-badge :value="$t->status"/>@endif</td>
                        <td class="px-4 py-2.5 text-right whitespace-nowrap">
                            @if($t->task_type === 'Approval' && in_array($t->status, ['Open','In Progress','Blocked']))
                                <form method="POST" action="{{ route('tasks.decision', $t) }}" class="inline">@csrf<input type="hidden" name="decision" value="Approved"><button class="text-green-600 hover:underline text-xs">Approve</button></form>
                                <form method="POST" action="{{ route('tasks.decision', $t) }}" class="inline ml-1">@csrf<input type="hidden" name="decision" value="Rejected"><button class="text-red-600 hover:underline text-xs">Reject</button></form>
                            @else
                                <a href="{{ route('tasks.edit', $t) }}" class="text-brand-600 hover:underline text-xs">Edit</a>
                            @endif
                        </td>
                    </tr>
                @empty<tr><td colspan="8" class="px-4 py-12 text-center text-gray-400">No tasks.</td></tr>@endforelse
            </tbody>
        </table>
    </x-card>
    {{ $items->links() }}
</div>
@endsection
