@extends('layouts.app')

@section('title', 'Prochains matchs')
@section('description', 'Les prochains matchs de volley-ball en Martinique : championnats seniors et jeunes, dates, horaires et salles.')

@section('content')
<x-page-header eyebrow="Saison {{ $season?->name }}" title="Prochains matchs" image="images/photos/indoor-service.jpg"
               subtitle="Dates, horaires et salles de toutes les rencontres programmées par la ligue." :breadcrumbs="['Matchs' => null]" />

@include('games._filters')

<div class="container-x py-10">
    @if ($games->isNotEmpty())
        @foreach ($games->groupBy(fn ($g) => $g->date_time->translatedFormat('l d F Y')) as $day => $dayGames)
            <h2 class="mb-3 mt-8 flex items-center gap-2 font-display text-xl font-bold uppercase text-navy-800 first:mt-0">
                <i class="fa-regular fa-calendar text-bordeaux-600"></i>{{ ucfirst($day) }}
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($dayGames as $game)
                    <x-game-card :game="$game" />
                @endforeach
            </div>
        @endforeach
        <div class="mt-10">{{ $games->links() }}</div>
    @else
        <x-empty-state icon="fa-calendar-xmark" title="Aucun match à venir">Aucune rencontre programmée avec ces critères. Consultez les <a class="link" href="{{ route('games.results') }}">derniers résultats</a>.</x-empty-state>
    @endif
</div>
@endsection
