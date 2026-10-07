@props(['icon' => 'fa-volleyball', 'title' => 'Rien à afficher pour le moment'])
<div {{ $attributes->class(['rounded-2xl border-2 border-dashed border-slate-200 bg-white/60 px-6 py-12 text-center']) }}>
    <i class="fa-solid {{ $icon }} text-4xl text-navy-200"></i>
    <p class="mt-3 font-display text-xl font-bold uppercase text-navy-800">{{ $title }}</p>
    @if (trim($slot))<div class="mx-auto mt-1 max-w-md text-sm text-slate-500">{{ $slot }}</div>@endif
</div>
