@props(['name', 'label', 'options' => [], 'value' => null, 'placeholder' => null, 'help' => null, 'required' => false])
@php $current = (string) old($name, $value); @endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700">{{ $label }}@if ($required) <span class="text-bordeaux-600">*</span>@endif</label>
    <select id="{{ $name }}" name="{{ $name }}" @required($required)
            {{ $attributes->except('class')->merge(['class' => 'input mt-1'.($errors->has($name) ? ' !border-red-500' : '')]) }}>
        @if ($placeholder !== null)<option value="">{{ $placeholder }}</option>@endif
        @foreach ($options as $key => $label)
            <option value="{{ $key }}" @selected($current === (string) $key)>{{ $label }}</option>
        @endforeach
    </select>
    @if ($help)<p class="mt-1 text-xs text-slate-500">{{ $help }}</p>@endif
    @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
