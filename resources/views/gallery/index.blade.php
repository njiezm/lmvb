@extends('layouts.app')

@section('title', $category ? 'Galerie : '.(\App\Models\Gallery::CATEGORIES[$category] ?? $category) : 'Galerie')
@section('description', 'Photos et vidéos du volley-ball en Martinique : matchs, beach-volley, sélections et événements de la ligue.')

@section('content')
<x-page-header eyebrow="Photos & vidéos" title="Galerie" image="images/photos/beach-4.jpg"
               :breadcrumbs="$category ? ['Galerie' => route('gallery.index'), (\App\Models\Gallery::CATEGORIES[$category] ?? $category) => null] : ['Galerie' => null]" />

<div class="container-x py-10">
    <nav class="scrollbar-thin -mx-4 mb-8 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:flex-wrap sm:px-0" aria-label="Catégories">
        <a href="{{ route('gallery.index') }}" @class(['shrink-0 rounded-full px-4 py-2 text-sm font-semibold ring-1', 'bg-navy-800 text-white ring-navy-800' => ! $category, 'text-slate-600 ring-slate-200 hover:bg-navy-50' => $category])>Tout</a>
        @foreach ($categories as $key => $count)
            <a href="{{ route('gallery.category', $key) }}" @class(['shrink-0 rounded-full px-4 py-2 text-sm font-semibold ring-1', 'bg-navy-800 text-white ring-navy-800' => $category === $key, 'text-slate-600 ring-slate-200 hover:bg-navy-50' => $category !== $key])>
                {{ \App\Models\Gallery::CATEGORIES[$key] ?? $key }} <span class="opacity-60">{{ $count }}</span>
            </a>
        @endforeach
    </nav>

    @if ($items->isNotEmpty())
        <div class="columns-2 gap-3 sm:columns-3 lg:columns-4 [&>*]:mb-3">
            @foreach ($items as $item)
                <a href="{{ route('gallery.show', $item) }}" class="group relative block break-inside-avoid overflow-hidden rounded-2xl bg-navy-100">
                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" loading="lazy" class="w-full transition duration-500 group-hover:scale-105">
                    @if ($item->isVideo())
                        <span class="absolute inset-0 m-auto flex h-12 w-12 items-center justify-center rounded-full bg-bordeaux-600 text-white shadow-lg"><i class="fa-solid fa-play ml-0.5"></i></span>
                    @endif
                    <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/75 to-transparent p-3 text-sm font-semibold text-white opacity-0 transition group-hover:opacity-100">{{ $item->title }}</span>
                </a>
            @endforeach
        </div>
        <div class="mt-10">{{ $items->links() }}</div>
    @else
        <x-empty-state icon="fa-images" title="Galerie vide" />
    @endif
</div>
@endsection
