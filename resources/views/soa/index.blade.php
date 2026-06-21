@extends('layouts.app')
@section('title', 'Statement of Applicability')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center gap-2">
        <h2 class="text-xl font-semibold text-gray-900">Statement of Applicability</h2>
    </div>
    <form method="GET" class="flex gap-2">
        <select name="framework" onchange="this.form.submit()" class="{{ $sel }}">
            <option value="">All Frameworks</option>
            @foreach($frameworks as $f)<option value="{{ $f }}" @selected(($filters['framework'] ?? '') === $f)>{{ $f }}</option>@endforeach
        </select>
    </form>

    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">Control</th><th class="px-4 py-3">Applicable</th>
                <th class="px-4 py-3">Implementation</th><th class="px-4 py-3">Responsible</th><th class="px-4 py-3"></th>
            </tr></thead>
            <tbody>
                @foreach($controls as $c)
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-2.5"><span class="font-mono font-semibold text-brand-600">{{ $c->clause }}</span> {{ \Illuminate\Support\Str::limit($c->title, 45) }}</td>
                        <td class="px-4 py-2.5">@if($c->soaEntry)<x-badge :value="$c->soaEntry->applicable ? 'Applicable' : 'Not Applicable'"/>@else <span class="text-gray-300">—</span> @endif</td>
                        <td class="px-4 py-2.5">@if($c->soaEntry)<x-badge :value="$c->soaEntry->implementation_status"/>@else <span class="text-gray-300">—</span> @endif</td>
                        <td class="px-4 py-2.5 text-gray-400">{{ $c->soaEntry?->responsible?->full_name ?? '—' }}</td>
                        <td class="px-4 py-2.5 text-right"><a href="{{ route('soa.edit', $c) }}" class="text-brand-600 hover:underline text-xs">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
    {{ $controls->links() }}
</div>
@endsection
