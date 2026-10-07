@props(['title', 'eyebrow' => null, 'link' => null, 'linkLabel' => 'Tout voir'])
<div {{ $attributes->class(['mb-6 flex flex-wrap items-end justify-between gap-3']) }}>
    <div>
        @if ($eyebrow)<p class="eyebrow">{{ $eyebrow }}</p>@endif
        <h2 class="h-display mt-1 text-3xl text-navy-900 sm:text-4xl">{{ $title }}</h2>
    </div>
    @if ($link)
        <a href="{{ $link }}" class="group inline-flex items-center gap-2 text-sm font-semibold text-navy-600 hover:text-bordeaux-600">
            {{ $linkLabel }} <i class="fa-solid fa-arrow-right transition group-hover:translate-x-0.5"></i>
        </a>
    @endif
</div>
