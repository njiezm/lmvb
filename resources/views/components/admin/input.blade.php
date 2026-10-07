@props(['name', 'label', 'type' => 'text', 'value' => null, 'help' => null, 'required' => false])
@php $id = $attributes->get('id', str_replace(['[', ']', '.'], '_', $name)); @endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="block text-sm font-medium text-slate-700">{{ $label }}@if ($required) <span class="text-bordeaux-600">*</span>@endif</label>
    <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" @if ($type !== 'password' && $type !== 'file') value="{{ old($name, $value) }}" @endif @required($required)
           {{ $attributes->except(['class', 'id'])->merge(['class' => 'input mt-1'.($errors->has($name) ? ' !border-red-500' : '')]) }}>
    @if ($help)<p class="mt-1 text-xs text-slate-500">{{ $help }}</p>@endif
    @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
