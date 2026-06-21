@extends('layouts.app')
@section('title', 'Evidence')
@section('content')
@php $sel = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm'; @endphp
<div class="space-y-4" x-data="{ up: {{ $errors->any() ? 'true' : 'false' }} }">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2"><h2 class="text-xl font-semibold text-gray-900">Evidence</h2>
            <span class="bg-brand-100 text-brand-700 text-xs font-semibold px-2.5 py-1 rounded-full">{{ $items->total() }}</span></div>
        <button @click="up=true" class="bg-brand-600 hover:bg-brand-700 text-white text-sm px-4 py-2 rounded-lg">+ Upload Evidence</button>
    </div>

    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Title</th><th class="px-4 py-3">File</th><th class="px-4 py-3">Linked to</th>
                <th class="px-4 py-3">Uploaded by</th><th class="px-4 py-3">Date</th><th class="px-4 py-3"></th></tr></thead>
            <tbody>
                @forelse($items as $e)
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-2.5">{{ $e->title }}</td>
                        <td class="px-4 py-2.5 text-gray-500">{{ \Illuminate\Support\Str::limit($e->file_name, 30) }} <span class="text-xs text-gray-400">({{ $e->file_size ? round($e->file_size / 1024) . ' KB' : '' }})</span></td>
                        <td class="px-4 py-2.5 text-xs text-gray-500">
                            @if($e->control)Control {{ $e->control->clause }}@elseif($e->risk)Risk {{ $e->risk->ref_id }}@elseif($e->audit)Audit {{ $e->audit->ref_id }}@elseif($e->policy)Policy {{ $e->policy->ref_id }}@else—@endif
                        </td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $e->uploader?->full_name ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-gray-400 text-xs">{{ optional($e->created_at)->format('Y-m-d') }}</td>
                        <td class="px-4 py-2.5 text-right whitespace-nowrap">
                            <a href="{{ route('evidence.download', $e) }}" class="text-brand-600 hover:underline text-xs">Download</a>
                            <form method="POST" action="{{ route('evidence.destroy', $e) }}" class="inline ml-1" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="text-gray-300 hover:text-red-500 text-xs">✕</button></form>
                        </td>
                    </tr>
                @empty<tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No evidence uploaded.</td></tr>@endforelse
            </tbody>
        </table>
    </x-card>
    {{ $items->links() }}

    <div x-show="up" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
        <div @click.outside="up=false" class="bg-white rounded-xl w-full max-w-lg p-6">
            <h3 class="font-semibold text-lg mb-4">Upload Evidence</h3>
            <form method="POST" action="{{ route('evidence.store') }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <x-field name="title" required/>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">File <span class="text-red-500">*</span></label>
                    <input type="file" name="file" required class="{{ $sel }}">
                    @error('file')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <select name="control_id" class="{{ $sel }}"><option value="">Link control…</option>@foreach($controls as $c)<option value="{{ $c->id }}">{{ $c->clause }}</option>@endforeach</select>
                    <select name="risk_id" class="{{ $sel }}"><option value="">Link risk…</option>@foreach($risks as $r)<option value="{{ $r->id }}">{{ $r->ref_id }}</option>@endforeach</select>
                    <select name="audit_id" class="{{ $sel }}"><option value="">Link audit…</option>@foreach($audits as $a)<option value="{{ $a->id }}">{{ $a->ref_id }}</option>@endforeach</select>
                    <select name="policy_id" class="{{ $sel }}"><option value="">Link policy…</option>@foreach($policies as $p)<option value="{{ $p->id }}">{{ $p->ref_id }}</option>@endforeach</select>
                </div>
                <x-field name="description" type="textarea"/>
                <div class="flex justify-end gap-2"><button type="button" @click="up=false" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                    <button class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm">Upload</button></div>
            </form>
        </div>
    </div>
</div>
@endsection
