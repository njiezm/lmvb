@extends('layouts.app')

@section('title', isset($category) ? 'Actualités : '.$category->name : 'Actualités')
@section('description', 'Toute l\'actualité du volley-ball en Martinique : championnats, sélections, beach-volley et vie des clubs.')

@section('content')
<x-page-header eyebrow="La vie de la ligue" :title="isset($category) ? $category->name : 'Actualités'" image="images/photos/indoor-celebration.jpg"
               :breadcrumbs="isset($category) ? ['Actualités' => route('news.index'), $category->name => null] : ['Actualités' => null]" />

<div class="container-x py-10">
    <nav class="scrollbar-thin -mx-4 mb-8 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:flex-wrap sm:px-0" aria-label="Catégories">
        <a href="{{ route('news.index') }}" @class(['shrink-0 rounded-full px-4 py-2 text-sm font-semibold ring-1', 'bg-navy-800 text-white ring-navy-800' => ! isset($category), 'text-slate-600 ring-slate-200 hover:bg-navy-50' => isset($category)])>Toutes</a>
        @foreach ($categories as $cat)
            @if ($cat->news_count)
                <a href="{{ route('news.category', $cat) }}" @class(['shrink-0 rounded-full px-4 py-2 text-sm font-semibold ring-1', 'bg-navy-800 text-white ring-navy-800' => isset($category) && $category->id === $cat->id, 'text-slate-600 ring-slate-200 hover:bg-navy-50' => ! isset($category) || $category->id !== $cat->id])>
                    {{ $cat->name }} <span class="opacity-60">{{ $cat->news_count }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    @if ($news->isNotEmpty())
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($news as $i => $item)
                <x-news-card :news="$item" :large="$i === 0 && $news->currentPage() === 1" :class="$i === 0 && $news->currentPage() === 1 ? 'sm:col-span-2' : ''" />
            @endforeach
        </div>
        <div class="mt-10">{{ $news->links() }}</div>
    @else
        <x-empty-state icon="fa-newspaper" title="Aucune actualité" />
    @endif
</div>
@endsection
