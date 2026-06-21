@extends('layouts.app')
@section('title', 'Frameworks')
@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">Frameworks &amp; Crosswalk Coverage</h2>
        <p class="text-sm text-gray-400">Map one control set across standards — "test once, comply many".</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach($cov['frameworks'] as $f)
            <a href="{{ route('controls.index', ['framework' => $f['framework']]) }}" class="bg-white rounded-xl border border-gray-200 p-4 hover:border-brand-300 transition">
                <p class="text-sm font-semibold text-gray-900">{{ $f['framework'] }}</p>
                <p class="text-3xl font-bold text-brand-600 mt-1">{{ $f['total'] }}</p>
                <p class="text-xs text-gray-400">controls</p>
                <p class="text-xs text-gray-500 mt-2">{{ $f['mapped_any'] }} mapped ({{ $f['pct'] }}%)</p>
            </a>
        @endforeach
    </div>

    <x-card class="p-5 overflow-x-auto">
        <h3 class="font-semibold text-gray-900 mb-3">Coverage matrix</h3>
        <p class="text-xs text-gray-400 mb-3">Each cell: % of the row framework's controls that map to the column framework.</p>
        <table class="text-sm border-collapse">
            <thead>
                <tr>
                    <th class="p-2 text-left text-xs text-gray-400">source ↓ / target →</th>
                    @foreach($cov['fwList'] as $col)<th class="p-2 text-xs text-gray-600 font-semibold">{{ $col }}</th>@endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($cov['fwList'] as $src)
                    <tr class="border-t border-gray-100">
                        <th class="p-2 text-left text-xs text-gray-600 font-semibold whitespace-nowrap">{{ $src }}</th>
                        @foreach($cov['fwList'] as $tgt)
                            <td class="p-2 text-center">
                                @if($src === $tgt)
                                    <span class="text-gray-200">—</span>
                                @else
                                    @php $m = $cov['matrix']["$src|$tgt"] ?? ['mapped' => 0, 'total' => 0, 'pct' => 0]; @endphp
                                    @if($m['mapped'] > 0)
                                        <span class="inline-block px-2 py-1 rounded {{ $m['pct'] >= 30 ? 'bg-green-100 text-green-700' : 'bg-amber-50 text-amber-700' }}">{{ $m['pct'] }}%</span>
                                        <span class="block text-[10px] text-gray-400">{{ $m['mapped'] }}/{{ $m['total'] }}</span>
                                    @else
                                        <span class="text-gray-300 text-xs">0</span>
                                    @endif
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>
</div>
@endsection
