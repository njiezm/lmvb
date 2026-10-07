@props(['name', 'label', 'value' => null, 'rows' => 4, 'help' => null, 'required' => false])
<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="block text-sm font-medium text-slate-700">{{ $label }}@if ($required) <span class="text-bordeaux-600">*</span>@endif</label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" @required($required)
              {{ $attributes->except('class')->merge(['class' => 'input mt-1'.($errors->has($name) ? ' !border-red-500' : '')]) }}>{{ old($name, $value) }}</textarea>
    @if ($help)<p class="mt-1 text-xs text-slate-500">{{ $help }}</p>@endif
    @error($name)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
</div>
