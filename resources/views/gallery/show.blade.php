@extends('layouts.app')

@section('title', $item->title)
@section('description', $item->description ?: $item->title.' · Galerie de la Ligue Martiniquaise de Volley-Ball')
@if ($item->image_url) @section('og_image', $item->image_url) @endif

@section('content')
<div class="bg-navy-950 py-8">
    <div class="container-x max-w-5xl">
        <nav class="mb-4 text-xs text-navy-200" aria-label="Fil d'Ariane"><a href="{{ route('gallery.index') }}" class="hover:text-white"><i class="fa-solid fa-arrow-left mr-1"></i>Galerie</a> / <a href="{{ route('gallery.category', $item->category) }}" class="hover:text-white">{{ $item->category_name }}</a></nav>
        @if ($item->isVideo() && $item->youtube_id)
            <x-video-embed :item="$item" />
        @else
            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="mx-auto max-h-[75vh] w-auto rounded-2xl">
        @endif
    </div>
</div>
<div class="container-x max-w-5xl py-8">
    <h1 class="h-display text-3xl text-navy-900 sm:text-4xl">{{ $item->title }}</h1>
    <p class="mt-2 text-sm text-slate-500">
        {{ $item->category_name }} · {{ $item->created_at->translatedFormat('d F Y') }}
        @if ($item->club) · <a class="link" href="{{ route('clubs.show', $item->club) }}">{{ $item->club->name }}</a>@endif
        @if ($item->credit) · Crédit : {{ $item->credit }}@endif
    </p>
    @if ($item->description)<p class="mt-4 max-w-3xl text-slate-700">{{ $item->description }}</p>@endif
    @if ($item->isVideo() && $item->video_url)<a href="{{ $item->video_url }}" target="_blank" rel="noopener" class="btn-light mt-4"><i class="fa-brands fa-youtube text-red-600"></i> Voir sur YouTube</a>@endif

    @if ($related->isNotEmpty())
        <h2 class="h-display mb-4 mt-12 text-2xl text-navy-900">Dans la même catégorie</h2>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($related as $rel)
                <a href="{{ route('gallery.show', $rel) }}" class="aspect-square overflow-hidden rounded-xl bg-navy-100"><img src="{{ $rel->image_url }}" alt="{{ $rel->title }}" loading="lazy" class="h-full w-full object-cover transition hover:scale-105"></a>
            @endforeach
        </div>
    @endif
</div>
@endsection
