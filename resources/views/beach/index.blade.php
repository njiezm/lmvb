@extends('layouts.app')

@section('title', 'Beach-volley')
@section('description', 'Le beach-volley en Martinique : tournois, championnat, inscriptions et paires martiniquaises.')

@section('content')
<section class="relative isolate overflow-hidden text-white">
    <video class="absolute inset-0 -z-20 h-full w-full object-cover" autoplay muted loop playsinline preload="none" poster="{{ asset('images/photos/beach-3.jpg') }}" aria-hidden="true">
        <source src="{{ asset('videos/hero-volley-2.mp4') }}" type="video/mp4">
    </video>
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-navy-950/90 via-navy-900/60 to-transparent"></div>
    <div class="container-x py-20 sm:py-28">
        <nav class="mb-4 text-xs text-navy-100" aria-label="Fil d'Ariane"><a href="{{ route('home') }}" class="hover:text-white">Accueil</a> / Beach</nav>
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-sable-200">Sable · soleil · 2x2</p>
        <h1 class="h-display mt-2 max-w-2xl text-5xl sm:text-7xl">Beach-volley en Martinique</h1>
        <p class="mt-4 max-w-xl text-lg text-navy-50">Championnat de beach, tournois ouverts et paires martiniquaises engagées sur la scène caribéenne.</p>
        <div class="mt-8 flex flex-wrap gap-3">
            <a href="#evenements" class="btn bg-sable-100 px-6 py-3 text-navy-900 hover:bg-white">Prochains tournois</a>
            <a href="{{ route('beach.calendar') }}" class="btn px-6 py-3 text-white ring-1 ring-white/50 hover:bg-white/10"><i class="fa-regular fa-calendar"></i> Calendrier</a>
        </div>
    </div>
</section>

<div class="container-x py-14" id="evenements">
    <x-section-heading eyebrow="Inscriptions" title="Prochains événements" />
    @if ($upcomingEvents->isNotEmpty())
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($upcomingEvents as $event)
                @include('beach._card', ['event' => $event])
            @endforeach
        </div>
    @else
        <x-empty-state icon="fa-umbrella-beach" title="Pas de tournoi programmé">Les prochains tournois seront annoncés ici et sur les réseaux de la ligue.</x-empty-state>
    @endif
</div>

@if ($videos->isNotEmpty())
    <section class="bg-sable-50 py-14">
        <div class="container-x">
            <x-section-heading eyebrow="En vidéo" title="Les finales de beach" />
            <div class="grid gap-6 md:grid-cols-2">@foreach ($videos as $video)<x-video-embed :item="$video" />@endforeach</div>
        </div>
    </section>
@endif

@if ($finishedEvents->isNotEmpty())
    <div class="container-x py-14">
        <x-section-heading title="Événements passés" />
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach ($finishedEvents as $event)
                @include('beach._card', ['event' => $event])
            @endforeach
        </div>
    </div>
@endif

@if ($photos->isNotEmpty())
    <div class="container-x pb-14">
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            @foreach ($photos as $photo)
                <a href="{{ route('gallery.show', $photo) }}" class="aspect-square overflow-hidden rounded-2xl"><img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" loading="lazy" class="h-full w-full object-cover transition hover:scale-105"></a>
            @endforeach
        </div>
    </div>
@endif
@endsection
