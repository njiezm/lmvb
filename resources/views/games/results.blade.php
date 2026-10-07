@extends('layouts.app')

@section('title', 'Résultats')
@section('description', 'Tous les résultats des championnats de volley-ball en Martinique, avec le détail des sets.')

@section('content')
<x-page-header eyebrow="Saison {{ $season?->name }}" title="Résultats" image="images/photos/indoor-filet-silhouette.jpg"
               subtitle="Scores et détails des sets, synchronisés automatiquement avec la FFVolley." :breadcrumbs="['Matchs' => route('games.index'), 'Résultats' => null]" />

@include('games._filters')

<div class="container-x py-10">
    @if ($games->isNotEmpty())
        <div class="card divide-y divide-slate-100 overflow-hidden">
            @foreach ($games as $game)
                <a href="{{ route('games.show', $game) }}" class="grid grid-cols-[1fr_auto_1fr] items-center gap-2 px-3 py-3 transition hover:bg-navy-50/60 sm:grid-cols-[8rem_1fr_auto_1fr_10rem] sm:gap-4 sm:px-5">
                    <div class="col-span-3 flex items-center justify-between text-xs text-slate-500 sm:col-span-1 sm:block">
                        <span class="font-semibold text-slate-700">{{ $game->date_time->translatedFormat('d M Y') }}</span>
                        <span class="truncate sm:mt-0.5 sm:block">{{ $game->competitionModel?->code }} · {{ $game->competitionModel?->gender_label }}</span>
                    </div>
                    <div class="flex min-w-0 items-center justify-end gap-2 text-right">
                        <span @class(['truncate text-sm', 'font-bold text-slate-900' => $game->winner === 'home', 'text-slate-600' => $game->winner !== 'home'])>{{ $game->home_name }}</span>
                        <x-club-badge :club="$game->homeTeam" :name="$game->home_name" size="sm" class="hidden sm:inline-flex" />
                    </div>
                    <div class="tabular flex items-center gap-1 font-display text-2xl font-extrabold">
                        <span @class(['rounded-md px-2', 'bg-navy-800 text-white' => $game->winner === 'home', 'bg-slate-100 text-slate-500' => $game->winner !== 'home'])>{{ $game->home_score }}</span>
                        <span @class(['rounded-md px-2', 'bg-navy-800 text-white' => $game->winner === 'away', 'bg-slate-100 text-slate-500' => $game->winner !== 'away'])>{{ $game->away_score }}</span>
                    </div>
                    <div class="flex min-w-0 items-center gap-2">
                        <x-club-badge :club="$game->awayTeam" :name="$game->away_name" size="sm" class="hidden sm:inline-flex" />
                        <span @class(['truncate text-sm', 'font-bold text-slate-900' => $game->winner === 'away', 'text-slate-600' => $game->winner !== 'away'])>{{ $game->away_name }}</span>
                    </div>
                    <div class="tabular col-span-3 text-center text-xs text-slate-400 sm:col-span-1 sm:text-right">
                        @if ($game->isForfeit())<span class="chip bg-amber-100 text-amber-800">Forfait</span>
                        @elseif ($game->set_scores){{ collect($game->set_scores)->map(fn ($s) => $s[0].'-'.$s[1])->implode('  ') }}@endif
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-10">{{ $games->links() }}</div>
    @else
        <x-empty-state icon="fa-list-ol" title="Aucun résultat">Aucun résultat ne correspond à ces critères.</x-empty-state>
    @endif
</div>
@endsection
