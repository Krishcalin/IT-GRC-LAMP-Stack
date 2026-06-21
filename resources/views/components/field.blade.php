@props(['name', 'label' => null, 'type' => 'text', 'value' => null, 'options' => [], 'required' => false, 'placeholder' => '', 'col' => 1])
@php
    $label = $label ?? \Illuminate\Support\Str::headline($name);
    $val = old($name, $value);
    $base = 'w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none';
@endphp
<div class="{{ $col === 2 ? 'sm:col-span-2' : '' }}">
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}@if($required)<span class="text-red-500"> *</span>@endif</label>
    @if($type === 'textarea')
        <textarea name="{{ $name }}" @if($required) required @endif rows="3" class="{{ $base }}" placeholder="{{ $placeholder }}">{{ $val }}</textarea>
    @elseif($type === 'select')
        @php $isList = array_is_list($options); @endphp
        <select name="{{ $name }}" @if($required) required @endif class="{{ $base }}">
            @foreach($options as $key => $opt)
                @php $optVal = $isList ? $opt : $key; @endphp
                <option value="{{ $optVal }}" @selected((string) $val === (string) $optVal)>{{ $opt === '' ? '—' : $opt }}</option>
            @endforeach
        </select>
    @elseif($type === 'checkbox')
        <label class="inline-flex items-center gap-2 mt-1">
            <input type="hidden" name="{{ $name }}" value="0">
            <input type="checkbox" name="{{ $name }}" value="1" @checked($val) class="rounded border-gray-300 text-brand-600 focus:ring-brand-500">
            <span class="text-sm text-gray-600">{{ $placeholder ?: 'Yes' }}</span>
        </label>
    @else
        <input type="{{ $type }}" name="{{ $name }}" value="{{ $val }}" @if($required) required @endif class="{{ $base }}" placeholder="{{ $placeholder }}">
    @endif
    @error($name)<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
</div>
