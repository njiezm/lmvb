@extends('layouts.app')

@section('title', $news->title)
@section('description', $news->excerpt)
@section('og_type', 'article')
@if ($news->image_url) @section('og_image', $news->image_url) @endif

@push('head')
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $news->title,
            'description' => $news->excerpt,
            'image' => $news->image_url ? [$news->image_url] : [asset('images/brand/og-default.jpg')],
            'datePublished' => $news->published_at?->toIso8601String(),
            'dateModified' => $news->updated_at?->toIso8601String(),
            'publisher' => ['@type' => 'Organization', 'name' => 'Ligue Martiniquaise de Volley-Ball', 'logo' => ['@type' => 'ImageObject', 'url' => asset('images/brand/logo-lmvb-512.png')]],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('content')
<article>
    <header class="bg-white">
        <div class="container-x max-w-4xl pb-8 pt-10">
            <nav class="mb-5 text-xs text-slate-500" aria-label="Fil d'Ariane">
                <a href="{{ route('home') }}" class="hover:text-navy-700">Accueil</a> / <a href="{{ route('news.index') }}" class="hover:text-navy-700">Actualités</a>
                @if ($news->category) / <a href="{{ route('news.category', $news->category) }}" class="hover:text-navy-700">{{ $news->category->name }}</a>@endif
            </nav>
            @if ($news->category)<span class="chip text-white" style="background: {{ $news->category->color }}">{{ $news->category->name }}</span>@endif
            <h1 class="h-display mt-3 text-4xl text-navy-900 sm:text-5xl">{{ $news->title }}</h1>
            <p class="mt-4 text-lg text-slate-600">{{ $news->excerpt }}</p>
            <div class="mt-5 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-slate-500">
                <span><i class="fa-regular fa-calendar mr-1"></i><time datetime="{{ $news->published_at?->toIso8601String() }}">{{ $news->published_at?->translatedFormat('d F Y') }}</time></span>
                <span><i class="fa-regular fa-clock mr-1"></i>{{ $news->reading_time }} min de lecture</span>
                @if ($news->club)<a href="{{ route('clubs.show', $news->club) }}" class="link"><i class="fa-solid fa-people-group mr-1"></i>{{ $news->club->name }}</a>@endif
            </div>
        </div>
    </header>

    @if ($news->image_url)
        <figure class="container-x max-w-5xl">
            <img src="{{ $news->image_url }}" alt="" class="aspect-[16/9] w-full rounded-3xl object-cover shadow-lg">
            @if ($news->image_credit)<figcaption class="mt-2 text-right text-xs text-slate-400">Photo : {{ $news->image_credit }}</figcaption>@endif
        </figure>
    @endif

    <div class="container-x max-w-3xl py-10">
        <div class="prose prose-lg prose-slate max-w-none prose-headings:font-display prose-headings:uppercase prose-headings:text-navy-900 prose-a:text-navy-600 prose-img:rounded-2xl">
            {!! safe_html($news->content) !!}
        </div>

        @if ($news->source_url)
            <p class="mt-8 rounded-xl bg-slate-100 px-4 py-3 text-sm text-slate-600"><i class="fa-solid fa-link mr-1"></i>Source : <a href="{{ $news->source_url }}" class="link break-all" target="_blank" rel="noopener">{{ parse_url($news->source_url, PHP_URL_HOST) }}</a></p>
        @endif

        <div class="mt-10 flex flex-wrap items-center gap-3 border-t border-slate-200 pt-6">
            <span class="text-sm font-semibold text-slate-600">Partager :</span>
            <a class="btn-light !px-3" target="_blank" rel="noopener" aria-label="Partager sur Facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"><i class="fa-brands fa-facebook-f"></i></a>
            <a class="btn-light !px-3" target="_blank" rel="noopener" aria-label="Partager sur WhatsApp" href="https://wa.me/?text={{ urlencode($news->title.' '.url()->current()) }}"><i class="fa-brands fa-whatsapp"></i></a>
            <a class="btn-light !px-3" target="_blank" rel="noopener" aria-label="Partager sur X" href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($news->title) }}"><i class="fa-brands fa-x-twitter"></i></a>
            <button type="button" class="btn-light !px-3" aria-label="Copier le lien" onclick="navigator.clipboard.writeText(location.href);this.innerHTML='<i class=\'fa-solid fa-check\'></i>'"><i class="fa-solid fa-link"></i></button>
        </div>
    </div>
</article>

@if ($relatedNews->isNotEmpty())
    <section class="bg-white py-14">
        <div class="container-x">
            <x-section-heading title="À lire aussi" :link="route('news.index')" />
            <div class="grid gap-6 md:grid-cols-3">@foreach ($relatedNews as $item)<x-news-card :news="$item" />@endforeach</div>
        </div>
    </section>
@endif
@endsection
