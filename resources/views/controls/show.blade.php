@extends('layouts.app')
@section('title', 'Control ' . $control->clause)
@section('content')
@php $sel = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm'; @endphp
<div class="space-y-4">
    <a href="{{ route('controls.index') }}" class="text-sm text-brand-600 hover:underline">&larr; Back to controls</a>

    <div class="flex items-start justify-between gap-4 flex-wrap">
        <div>
            <div class="flex items-center gap-2">
                <span class="font-mono text-lg font-bold text-brand-600">{{ $control->clause }}</span>
                <x-badge :value="$control->theme"/>
                <x-badge :value="$control->status"/>
            </div>
            <h2 class="text-xl font-semibold text-gray-900 mt-1">{{ $control->title }}</h2>
            <p class="text-xs text-gray-400 mt-0.5">{{ $control->framework }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <x-card class="p-5">
                <h3 class="font-semibold text-gray-900 mb-2">Description</h3>
                <p class="text-sm text-gray-600 whitespace-pre-line">{{ $control->description }}</p>
                @if($control->implementation_guidance)
                    <h3 class="font-semibold text-gray-900 mb-2 mt-4">Implementation Guidance</h3>
                    <p class="text-sm text-gray-600 whitespace-pre-line">{{ $control->implementation_guidance }}</p>
                @endif
            </x-card>

            <x-card class="p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Manage</h3>
                <form method="POST" action="{{ route('controls.update', $control) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @csrf @method('PUT')
                    <x-field name="status" type="select" :value="$control->status" :options="['Not Started','In Progress','Implemented','Not Applicable']" required/>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Owner</label>
                        <select name="owner_id" class="{{ $sel }}">
                            <option value="">— Unassigned —</option>
                            @foreach($users as $u)<option value="{{ $u->id }}" @selected($control->owner_id === $u->id)>{{ $u->full_name }}</option>@endforeach
                        </select>
                    </div>
                    <x-field name="review_date" label="Review Date" type="date" :value="optional($control->review_date)->format('Y-m-d')"/>
                    <x-field name="theme" type="select" :value="$control->theme" :options="['Organizational','People','Physical','Technological']" required/>
                    <x-field name="description" type="textarea" :value="$control->description" :col="2" required/>
                    <x-field name="implementation_guidance" label="Implementation Guidance" type="textarea" :value="$control->implementation_guidance" :col="2"/>
                    <div class="sm:col-span-2 flex justify-between items-center mt-1">
                        <button class="bg-brand-600 text-white px-4 py-2 rounded-lg text-sm">Save changes</button>
                    </div>
                </form>
            </x-card>
        </div>

        <div class="space-y-4">
            <x-card class="p-5" x-data="{ add:false }">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-900">Framework Crosswalk</h3>
                    <button @click="add=!add" class="text-xs text-brand-600">+ Map</button>
                </div>
                <div class="space-y-2">
                    @forelse($cross as $x)
                        <div class="flex items-center justify-between text-sm border border-gray-100 rounded-lg px-3 py-2">
                            <div>
                                <span class="text-gray-400 mr-1">{{ $x['dir'] }}</span>
                                <span class="font-mono font-semibold text-brand-600">{{ $x['ctrl']->clause }}</span>
                                <span class="text-xs text-gray-400 ml-1">{{ $x['ctrl']->framework }}</span>
                                <x-badge :value="$x['rel']" class="ml-1"/>
                            </div>
                            <form method="POST" action="{{ route('controls.mappings.destroy', [$control, $x['id']]) }}">
                                @csrf @method('DELETE')
                                <button class="text-gray-300 hover:text-red-500 text-xs">✕</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400">No mappings yet.</p>
                    @endforelse
                </div>
                <form x-show="add" x-cloak method="POST" action="{{ route('controls.mappings.store', $control) }}" class="mt-3 space-y-2 border-t border-gray-100 pt-3">
                    @csrf
                    <select name="target_control_id" required class="{{ $sel }}">
                        <option value="">Select control…</option>
                        @foreach($allControls as $ac)@if($ac->id !== $control->id)<option value="{{ $ac->id }}">{{ $ac->clause }} — {{ \Illuminate\Support\Str::limit($ac->title, 40) }} ({{ $ac->framework }})</option>@endif @endforeach
                    </select>
                    <select name="relationship_type" required class="{{ $sel }}">
                        @foreach(['related','equivalent','broader','narrower'] as $rt)<option value="{{ $rt }}">{{ $rt }}</option>@endforeach
                    </select>
                    <button class="w-full bg-brand-600 text-white py-2 rounded-lg text-sm">Add mapping</button>
                </form>
            </x-card>

            <x-card class="p-5">
                <h3 class="font-semibold text-gray-900 mb-2">Statement of Applicability</h3>
                @if($control->soaEntry)
                    <p class="text-sm"><x-badge :value="$control->soaEntry->applicable ? 'Applicable' : 'Not Applicable'"/> <x-badge :value="$control->soaEntry->implementation_status"/></p>
                @else
                    <p class="text-sm text-gray-400">No SoA entry.</p>
                @endif
                <a href="{{ route('soa.edit', $control) }}" class="text-xs text-brand-600 hover:underline mt-2 inline-block">Edit SoA entry &rarr;</a>
            </x-card>

            <x-card class="p-5">
                <h3 class="font-semibold text-gray-900 mb-2">Linked Risks</h3>
                @forelse($control->risks as $rk)
                    <a href="{{ route('risks.show', $rk) }}" class="block text-sm text-gray-600 hover:text-brand-600">{{ $rk->ref_id }} — {{ \Illuminate\Support\Str::limit($rk->title, 40) }}</a>
                @empty
                    <p class="text-sm text-gray-400">No linked risks.</p>
                @endforelse
            </x-card>
        </div>
    </div>
</div>
@endsection
