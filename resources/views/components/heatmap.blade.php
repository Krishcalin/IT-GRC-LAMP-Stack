@props(['heat'])
@php
    $colors = [
        'Low' => 'bg-green-100 text-green-800 border-green-200',
        'Medium' => 'bg-amber-100 text-amber-800 border-amber-200',
        'High' => 'bg-orange-200 text-orange-900 border-orange-300',
        'Critical' => 'bg-red-200 text-red-900 border-red-300',
    ];
@endphp
<div class="inline-block">
    <div class="flex">
        <div class="flex flex-col justify-around mr-1 text-[10px] text-gray-400 font-medium" style="height:13rem">
            <span>5</span><span>4</span><span>3</span><span>2</span><span>1</span>
        </div>
        <div>
            @foreach(array_chunk($heat['cells'], 5) as $row)
                <div class="flex gap-1 mb-1">
                    @foreach($row as $cell)
                        <div class="w-10 h-10 rounded border flex items-center justify-center text-sm font-semibold {{ $colors[$cell['level']] ?? 'bg-gray-100' }}"
                             title="Likelihood {{ $cell['l'] }} × Impact {{ $cell['i'] }} — {{ $cell['level'] }}">{{ $cell['count'] ?: '' }}</div>
                    @endforeach
                </div>
            @endforeach
            <div class="flex gap-1 mt-1 ml-0 text-[10px] text-gray-400 font-medium">
                @for($i = 1; $i <= 5; $i++)<span class="w-10 text-center">{{ $i }}</span>@endfor
            </div>
        </div>
    </div>
    <p class="text-[10px] text-gray-400 mt-1 text-center">Impact &rarr; (Likelihood &uarr;) · {{ $heat['total'] }} risks</p>
</div>
