@extends('layouts.app')

@section('title', 'Sélections de Martinique')
@section('description', 'Les sélections régionales de volley-ball de Martinique : seniors et jeunes, compétitions caribéennes (CAZOVA, NORCECA) et Coupe Antilles.')

@section('content')
<x-page-header eyebrow="Équipes régionales" title="Sélections de Martinique" image="images/photos/divers-supporters.jpg"
               subtitle="Les meilleurs joueurs et joueuses de l'île représentent la Martinique dans les compétitions caribéennes (CAZOVA, NORCECA) et en Coupe Antilles."
               :breadcrumbs="['Sélections' => null]" />

<div class="container-x py-12">
    @foreach (['Seniors' => $seniorTeams, 'Jeunes' => $youthTeams] as $label => $teams)
        @if ($teams->isNotEmpty())
            <section class="mb-12">
                <h2 class="h-display mb-5 text-3xl text-navy-900">{{ $label }}</h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($teams as $team)
                        <a href="{{ route('selections.show', $team) }}" class="group card overflow-hidden transition hover:-translate-y-0.5 hover:shadow-lg">
                            <div class="relative aspect-[16/10] overflow-hidden bg-navy-900">
                                <img src="{{ $team->photo ? asset($team->photo) : asset($team->gender === 'F' ? 'images/photos/indoor-smash-2.jpg' : 'images/photos/indoor-bloc.jpg') }}" alt="" loading="lazy" class="h-full w-full object-cover opacity-80 transition duration-500 group-hover:scale-105">
                                <span @class(['chip absolute left-3 top-3 text-white', 'bg-pink-600' => $team->gender === 'F', 'bg-sky-600' => $team->gender === 'M'])>{{ $team->category_name }}</span>
                            </div>
                            <div class="p-5">
                                <h3 class="font-display text-xl font-bold uppercase text-navy-900 group-hover:text-bordeaux-600">{{ $team->name }}</h3>
                                <p class="mt-1 text-sm text-slate-500">{{ $team->players_count ? $team->players_count.' joueur·ses' : 'Effectif à venir' }}@if ($team->coach) · Coach : {{ $team->coach }}@endif</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach

    @if ($seniorTeams->isEmpty() && $youthTeams->isEmpty())
        <x-empty-state icon="fa-flag" title="Sélections à venir" />
    @endif

    @if ($videos->isNotEmpty())
        <section>
            <h2 class="h-display mb-5 text-3xl text-navy-900">En vidéo</h2>
            <div class="grid gap-6 md:grid-cols-2">@foreach ($videos as $video)<x-video-embed :item="$video" />@endforeach</div>
        </section>
    @endif
</div>
@endsection
