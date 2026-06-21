@extends('layouts.app')
@section('title', 'Reminders')
@section('content')
@php $sel = 'rounded-lg border border-gray-300 px-3 py-2 text-sm bg-white'; @endphp
<div class="space-y-4">
    <div class="flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Reminders</h2>
            <p class="text-sm text-gray-400">Overdue and upcoming reviews, due dates and renewals across the ISMS.</p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <label class="text-sm text-gray-500">Horizon</label>
            <select name="days" onchange="this.form.submit()" class="{{ $sel }}">
                @foreach([7, 14, 30, 60, 90] as $d)<option value="{{ $d }}" @selected($days === $d)>{{ $d }} days</option>@endforeach
            </select>
        </form>
    </div>

    <x-card class="overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-50 text-left text-xs uppercase tracking-wide text-gray-500">
                <th class="px-4 py-3">When</th><th class="px-4 py-3">Type</th><th class="px-4 py-3">Ref</th>
                <th class="px-4 py-3">Item</th><th class="px-4 py-3">Status</th></tr></thead>
            <tbody>
                @forelse($items as $i)
                    <tr class="border-t border-gray-100 {{ $i['overdue'] ? 'bg-red-50/40' : '' }}">
                        <td class="px-4 py-2.5 {{ $i['overdue'] ? 'text-red-600 font-semibold' : 'text-gray-600' }}">{{ $i['date']->format('Y-m-d') }}</td>
                        <td class="px-4 py-2.5"><x-badge :value="$i['type']"/></td>
                        <td class="px-4 py-2.5 font-mono text-brand-600">{{ $i['ref'] }}</td>
                        <td class="px-4 py-2.5"><a href="{{ $i['url'] }}" class="hover:text-brand-600">{{ \Illuminate\Support\Str::limit($i['title'], 60) }}</a></td>
                        <td class="px-4 py-2.5">@if($i['overdue'])<x-badge value="Overdue"/>@else<span class="text-xs text-gray-400">in {{ $i['date']->diffInDays(now()) }}d</span>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-gray-400">Nothing due in the next {{ $days }} days. 🎉</td></tr>
                @endforelse
            </tbody>
        </table>
    </x-card>
</div>
@endsection
