@props(['club' => null, 'name' => null, 'size' => 'md'])
@php
    $sizes = ['xs' => 'h-6 w-6 text-[0.55rem]', 'sm' => 'h-8 w-8 text-[0.65rem]', 'md' => 'h-11 w-11 text-xs', 'lg' => 'h-16 w-16 text-base', 'xl' => 'h-28 w-28 text-2xl'];
    $label = $club?->name ?? $name ?? '?';
    $initials = $club?->initials ?? \App\Support\Text::initials($label);
    $color = $club?->display_color ?? '#94a3b8';
@endphp
@if ($club?->logo_url)
    <img src="{{ $club->logo_url }}" alt="{{ $label }}" loading="lazy"
         {{ $attributes->class([$sizes[$size], 'shrink-0 rounded-full bg-white object-contain ring-1 ring-slate-200']) }}>
@else
    <span {{ $attributes->class([$sizes[$size], 'inline-flex shrink-0 select-none items-center justify-center rounded-full font-display font-extrabold leading-none tracking-tight text-white ring-2 ring-white']) }}
          style="background: linear-gradient(135deg, {{ $color }}, {{ $color }}cc)" title="{{ $label }}" aria-hidden="true">{{ $initials }}</span>
@endif
