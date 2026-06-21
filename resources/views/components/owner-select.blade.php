@props(['users', 'selected' => null, 'name' => 'owner_id', 'label' => 'Owner'])
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
    <select name="{{ $name }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
        <option value="">— Unassigned —</option>
        @foreach($users as $u)
            <option value="{{ $u->id }}" @selected((string) old($name, $selected) === (string) $u->id)>{{ $u->full_name }}</option>
        @endforeach
    </select>
</div>
