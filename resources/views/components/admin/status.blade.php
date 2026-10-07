@props(['value', 'label' => null])
@php
    $map = [
        'published' => 'bg-emerald-100 text-emerald-800', 'finished' => 'bg-emerald-100 text-emerald-800', 'success' => 'bg-emerald-100 text-emerald-800', 'confirmed' => 'bg-emerald-100 text-emerald-800', 'active' => 'bg-emerald-100 text-emerald-800', 'upcoming' => 'bg-sky-100 text-sky-800',
        'draft' => 'bg-amber-100 text-amber-800', 'scheduled' => 'bg-sky-100 text-sky-800', 'pending' => 'bg-amber-100 text-amber-800', 'partial' => 'bg-amber-100 text-amber-800', 'running' => 'bg-sky-100 text-sky-800', 'ongoing' => 'bg-sky-100 text-sky-800',
        'live' => 'bg-red-100 text-red-700', 'failed' => 'bg-red-100 text-red-700', 'cancelled' => 'bg-slate-200 text-slate-600', 'archived' => 'bg-slate-200 text-slate-600', 'inactive' => 'bg-slate-200 text-slate-600',
    ];
@endphp
<span {{ $attributes->merge(['class' => 'chip whitespace-nowrap '.($map[$value] ?? 'bg-slate-100 text-slate-700')]) }}>{{ $label ?? $value }}</span>
