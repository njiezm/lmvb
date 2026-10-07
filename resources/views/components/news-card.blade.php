@props(['news', 'large' => false])
<article {{ $attributes->class(['group relative flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70 transition hover:shadow-lg']) }}>
    <div @class(['relative overflow-hidden bg-navy-100', 'aspect-[16/9]' => ! $large, 'aspect-[16/10] lg:aspect-[16/9]' => $large])>
        @if ($news->image_url)
            <img src="{{ $news->image_url }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center bg-gradient-to-br from-navy-700 to-navy-900"><img src="{{ asset('images/brand/logo-lmvb-128.png') }}" alt="" class="h-20 opacity-90"></div>
        @endif
        @if ($news->category)
            <span class="chip absolute left-3 top-3 text-white shadow" style="background: {{ $news->category->color }}">{{ $news->category->name }}</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-5">
        <p class="text-xs text-slate-500">
            <time datetime="{{ $news->published_at?->toIso8601String() }}">{{ $news->published_at?->translatedFormat('d F Y') }}</time>
            @if ($news->club) · <span class="font-semibold text-navy-600">{{ $news->club->short_name ?? $news->club->name }}</span>@endif
        </p>
        <h3 @class(['mt-2 font-display font-bold uppercase leading-tight text-navy-900 group-hover:text-bordeaux-600', 'text-2xl sm:text-3xl' => $large, 'text-xl' => ! $large])>
            <a href="{{ route('news.show', $news->slug) }}" class="after:absolute after:inset-0">{{ $news->title }}</a>
        </h3>
        <p @class(['mt-2 text-sm leading-relaxed text-slate-600', 'line-clamp-3' => ! $large, 'line-clamp-4' => $large])>{{ $news->excerpt }}</p>
        <span class="mt-auto pt-4 text-sm font-semibold text-navy-600">Lire l'article <i class="fa-solid fa-arrow-right ml-1 text-xs"></i></span>
    </div>
</article>
