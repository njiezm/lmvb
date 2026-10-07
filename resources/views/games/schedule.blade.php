@extends('layouts.app')

@section('title', 'Calendrier des matchs')
@section('description', 'Calendrier complet des rencontres de volley-ball en Martinique, mois par mois.')

@section('content')
<x-page-header eyebrow="Saison {{ $season?->name }}" title="Calendrier" image="images/photos/indoor-gymnase-ballon.jpg"
               subtitle="Toutes les rencontres à venir, mois par mois." :breadcrumbs="['Matchs' => route('games.index'), 'Calendrier' => null]" />

@include('games._filters')

<div class="container-x py-10">
    @forelse ($months as $month => $games)
        <section class="mb-10">
            <h2 class="h-display mb-4 text-3xl text-navy-900">{{ ucfirst($month) }} <span class="text-base font-semibold normal-case text-slate-400">· {{ $games->count() }} match{{ $games->count() > 1 ? 's' : '' }}</span></h2>
            <div class="card divide-y divide-slate-100">
                @foreach ($games as $game)
                    <a href="{{ route('games.show', $game) }}" class="flex flex-col gap-1 px-4 py-3 transition hover:bg-navy-50/60 sm:flex-row sm:items-center sm:gap-4">
                        <div class="flex w-full shrink-0 items-center gap-3 sm:w-36">
                            <span class="flex h-12 w-12 flex-col items-center justify-center rounded-xl bg-navy-800 leading-none text-white">
                                <span class="font-display text-xl font-extrabold">{{ $game->date_time->format('d') }}</span>
                                <span class="text-[0.6rem] uppercase">{{ $game->date_time->translatedFormat('D') }}</span>
                            </span>
                            <span class="text-sm font-semibold text-slate-700">{{ $game->date_time->format('H\hi') }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-slate-900">{{ $game->home_name }} <span class="px-1 text-slate-400">vs</span> {{ $game->away_name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $game->competition_label }}@if ($game->venue) · <i class="fa-solid fa-location-dot"></i> {{ $game->venue_label }}@endif</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @empty
        <x-empty-state icon="fa-calendar-xmark" title="Aucun match à venir" />
    @endforelse
</div>
@endsection
