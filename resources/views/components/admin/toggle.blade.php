@props(['name', 'label', 'checked' => false, 'help' => null])
<div {{ $attributes->only('class') }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <label class="inline-flex cursor-pointer items-center gap-3">
        <input type="checkbox" name="{{ $name }}" value="1" class="peer sr-only" @checked(old($name, $checked))>
        <span class="relative h-6 w-11 rounded-full bg-slate-300 transition after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition peer-checked:bg-emerald-500 peer-checked:after:translate-x-5 peer-focus-visible:ring-2 peer-focus-visible:ring-navy-500"></span>
        <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
    </label>
    @if ($help)<p class="mt-1 text-xs text-slate-500">{{ $help }}</p>@endif
</div>
