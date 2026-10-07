@extends('layouts.app')

@section('title', $competition->display_name.' '.$season->name)
@section('description', 'Classement, calendrier et résultats : '.$competition->display_name.', saison '.$season->name.', Ligue Martiniquaise de Volley-Ball.')

@section('content')
<x-page-header :eyebrow="$competition->category_label.' · Saison '.$season->name" :title="$competition->display_name"
               image="{{ $competition->gender === 'F' ? 'images/photos/indoor-smash-2.jpg' : 'images/photos/indoor-smash-1.jpg' }}"
               :breadcrumbs="['Compétitions' => route('competitions.index', ['saison' => $season->slug]), $competition->display_name => null]">
    <div class="mt-6 flex flex-wrap items-center gap-3 text-sm">
        @if ($competition->synced_at)
            <span class="chip bg-white/10 py-1 text-navy-100 ring-1 ring-white/20"><i class="fa-solid fa-rotate"></i> Mis à jour {{ $competition->synced_at->diffForHumans() }}</span>
        @endif
        @if ($competition->is_ffvb)
            <a href="{{ $competition->ffvbUrl() }}" target="_blank" rel="noopener" class="chip bg-white/10 py-1 text-navy-100 ring-1 ring-white/20 hover:bg-white/20">Source officielle FFVolley <i class="fa-solid fa-arrow-up-right-from-square text-[0.6rem]"></i></a>
        @endif
    </div>
</x-page-header>

@if ($siblings->count() > 1)
    <nav class="border-b border-slate-200 bg-white" aria-label="Autres poules">
        <div class="container-x scrollbar-thin flex gap-2 overflow-x-auto py-3">
            @foreach ($siblings as $sibling)
                <a href="{{ route('competitions.show', [$season, $sibling]) }}"
                   @class(['shrink-0 rounded-full px-4 py-1.5 text-sm font-semibold ring-1',
                           'bg-navy-800 text-white ring-navy-800' => $sibling->id === $competition->id,
                           'text-slate-600 ring-slate-200 hover:bg-navy-50' => $sibling->id !== $competition->id])>
                    {{ $sibling->display_name }}
                </a>
            @endforeach
        </div>
    </nav>
@endif

<div class="container-x grid gap-10 py-12 lg:grid-cols-12">
    {{-- Classement --}}
    <section class="lg:col-span-7" aria-labelledby="classement">
        <h2 id="classement" class="h-display mb-4 text-3xl text-navy-900">Classement</h2>
        @if ($competition->standings->isNotEmpty())
            <div class="card p-4 sm:p-6">
                <x-standings-table :standings="$competition->standings" />
                <p class="mt-4 text-xs text-slate-500">
                    Victoire 3-0 / 3-1 : 3 pts · Victoire 3-2 : 2 pts · Défaite 2-3 : 1 pt · Défaite 1-3 / 0-3 : 0 pt · Forfait : pénalité.
                    J = joués, G = gagnés, P = perdus, F = forfaits.
                </p>
            </div>
        @else
            <x-empty-state icon="fa-ranking-star" title="Classement non disponible">Le classement sera affiché après les premières rencontres.</x-empty-state>
        @endif
    </section>

    {{-- Calendrier par journée --}}
    <section class="lg:col-span-5" aria-labelledby="calendrier" data-tabs>
        <h2 id="calendrier" class="h-display mb-4 text-3xl text-navy-900">Calendrier & résultats</h2>
        @if ($byMatchday->isNotEmpty())
            <div class="scrollbar-thin -mx-4 mb-4 flex gap-1.5 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0" role="tablist">
                @foreach ($byMatchday as $day => $games)
                    <button type="button" role="tab" data-tab="day-{{ $day }}" aria-selected="{{ $day == $currentDay ? 'true' : 'false' }}"
                            class="shrink-0 rounded-lg px-3 py-1.5 text-sm font-semibold text-slate-600 ring-1 ring-slate-200 aria-selected:bg-navy-800 aria-selected:text-white aria-selected:ring-navy-800">
                        {{ $day ? 'J'.$day : 'Phase' }}
                    </button>
                @endforeach
            </div>
            @foreach ($byMatchday as $day => $games)
                <div data-panel="day-{{ $day }}" @class(['space-y-3', 'hidden' => $day != $currentDay])>
                    <p class="text-sm font-semibold text-slate-500">{{ $day ? 'Journée '.$day : 'Rencontres' }}</p>
                    @foreach ($games as $game)
                        <x-game-card :game="$game" :show-competition="false" />
                    @endforeach
                </div>
            @endforeach
        @else
            <x-empty-state icon="fa-calendar" title="Calendrier à venir" />
        @endif
    </section>
</div>
@endsection
