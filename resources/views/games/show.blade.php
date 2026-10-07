@extends('layouts.app')

@section('title', $game->home_name.' – '.$game->away_name)
@section('description', ($game->status === 'finished' ? 'Résultat : '.$game->home_name.' '.$game->home_score.'-'.$game->away_score.' '.$game->away_name : 'Match '.$game->home_name.' contre '.$game->away_name).' · '.$game->competition_label)

@push('head')
    <script type="application/ld+json">
        {!! json_encode(array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'SportsEvent',
            'name' => $game->home_name.' – '.$game->away_name,
            'sport' => 'Volleyball',
            'startDate' => $game->date_time?->toIso8601String(),
            'location' => $game->venue ? ['@type' => 'Place', 'name' => $game->venue_label] : null,
            'homeTeam' => ['@type' => 'SportsTeam', 'name' => $game->home_name],
            'awayTeam' => ['@type' => 'SportsTeam', 'name' => $game->away_name],
            'organizer' => ['@type' => 'SportsOrganization', 'name' => 'Ligue Martiniquaise de Volley-Ball'],
        ]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
@endpush

@section('content')
@php $competition = $game->competitionModel; @endphp
<section class="relative isolate overflow-hidden bg-navy-950 text-white">
    <img src="{{ asset('images/photos/indoor-terrain-vue-aerienne.jpg') }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-20">
    <div class="absolute inset-0 -z-10 bg-gradient-to-b from-navy-950/60 to-navy-950"></div>
    <div class="container-x py-10 sm:py-14">
        <nav class="mb-6 text-xs text-navy-200" aria-label="Fil d'Ariane">
            <a href="{{ route('home') }}" class="hover:text-white">Accueil</a> /
            <a href="{{ route('games.index') }}" class="hover:text-white">Matchs</a>
            @if ($competition) / <a href="{{ route('competitions.show', [$competition->season, $competition]) }}" class="hover:text-white">{{ $competition->display_name }}</a>@endif
        </nav>
        <p class="text-center text-xs font-bold uppercase tracking-[0.2em] text-lavande-300">
            {{ $game->competition_label }}@if ($game->matchday) · Journée {{ $game->matchday }}@endif
        </p>

        <div class="mt-8 grid grid-cols-[1fr_auto_1fr] items-center gap-3 sm:gap-8">
            @foreach (['home' => [$game->homeTeam, $game->home_name], 'away' => [$game->awayTeam, $game->away_name]] as $side => [$club, $name])
                <div @class(['flex flex-col items-center gap-3 text-center', 'order-3' => $side === 'away'])>
                    <x-club-badge :club="$club" :name="$name" size="xl" class="!h-20 !w-20 !text-xl sm:!h-28 sm:!w-28 sm:!text-2xl" />
                    @if ($club && $club->active)
                        <a href="{{ route('clubs.show', $club) }}" class="font-display text-lg font-bold uppercase leading-tight hover:underline sm:text-2xl">{{ $name }}</a>
                    @else
                        <span class="font-display text-lg font-bold uppercase leading-tight sm:text-2xl">{{ $name }}</span>
                    @endif
                    <span class="text-xs uppercase tracking-wider text-navy-300">{{ $side === 'home' ? 'Domicile' : 'Extérieur' }}</span>
                </div>
                @if ($side === 'home')
                    <div class="order-2 text-center">
                        @if ($game->status === 'finished')
                            <p class="tabular font-display text-6xl font-extrabold sm:text-8xl">{{ $game->home_score }}<span class="mx-2 text-navy-400">-</span>{{ $game->away_score }}</p>
                            <p class="mt-1 text-sm text-navy-200">{{ $game->isForfeit() ? 'Victoire par forfait' : 'Terminé' }}</p>
                        @else
                            <p class="font-display text-4xl font-extrabold text-lavande-200 sm:text-6xl">VS</p>
                            <p class="mt-1 text-sm text-navy-200">{{ $game->status_label }}</p>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        @if ($game->set_scores && ! $game->isForfeit())
            <div class="mx-auto mt-10 max-w-xl overflow-hidden rounded-2xl bg-white/5 ring-1 ring-white/10">
                <table class="tabular w-full text-center text-sm">
                    <thead class="bg-white/5 text-xs uppercase tracking-wider text-navy-300">
                        <tr><th class="px-3 py-2 text-left font-semibold">Set</th>@foreach ($game->set_scores as $i => $s)<th class="px-2 py-2 font-semibold">{{ $i + 1 }}</th>@endforeach<th class="px-3 py-2 font-semibold">Total</th></tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ([0 => $game->home_name, 1 => $game->away_name] as $idx => $name)
                            <tr>
                                <td class="max-w-[8rem] truncate px-3 py-2 text-left font-semibold">{{ $name }}</td>
                                @foreach ($game->set_scores as $s)
                                    <td @class(['px-2 py-2', 'font-bold text-white' => $s[$idx] > $s[1 - $idx], 'text-navy-300' => $s[$idx] < $s[1 - $idx]])>{{ $s[$idx] }}</td>
                                @endforeach
                                <td class="px-3 py-2 font-bold">{{ $idx === 0 ? $game->home_points : $game->away_points }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</section>

<div class="container-x grid gap-8 py-12 lg:grid-cols-3">
    <section class="card p-6 lg:col-span-1">
        <h2 class="font-display text-xl font-bold uppercase text-navy-900">Infos pratiques</h2>
        <dl class="mt-4 space-y-4 text-sm">
            <div class="flex gap-3"><dt class="w-5 text-navy-400"><i class="fa-regular fa-calendar"></i></dt><dd>{{ $game->date_time ? ucfirst($game->date_time->translatedFormat('l d F Y \à H\hi')) : 'Date à définir' }}</dd></div>
            @if ($game->venue)
                <div class="flex gap-3"><dt class="w-5 text-navy-400"><i class="fa-solid fa-location-dot"></i></dt>
                    <dd>{{ $game->venue_label }}@if ($city = config('lmvb.venues.'.$game->venue)), {{ $city }}@endif<br>
                        <a class="link text-xs" target="_blank" rel="noopener" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($game->venue_label.' '.(config('lmvb.venues.'.$game->venue) ?? '').' Martinique') }}">Itinéraire</a></dd></div>
            @endif
            @if ($game->referee_1 || $game->referee_2)
                <div class="flex gap-3"><dt class="w-5 text-navy-400"><i class="fa-solid fa-flag"></i></dt><dd>Arbitres : {{ collect([$game->referee_1, $game->referee_2])->filter()->unique()->map(fn ($r) => \App\Support\Text::title($r))->implode(', ') }}</dd></div>
            @endif
            @if ($game->ffvb_code)
                <div class="flex gap-3"><dt class="w-5 text-navy-400"><i class="fa-solid fa-hashtag"></i></dt><dd>Match FFVolley {{ $game->ffvb_code }}</dd></div>
            @endif
        </dl>
        @if ($game->notes)<p class="mt-4 rounded-xl bg-sable-50 p-3 text-sm text-slate-700">{{ $game->notes }}</p>@endif
        @if ($game->date_time && $game->status !== 'finished')
            @php
                $start = $game->date_time->copy()->utc();
                $gcal = 'https://calendar.google.com/calendar/render?'.http_build_query(['action' => 'TEMPLATE', 'text' => $game->home_name.' - '.$game->away_name, 'dates' => $start->format('Ymd\THis\Z').'/'.$start->copy()->addHours(2)->format('Ymd\THis\Z'), 'location' => $game->venue_label.', Martinique', 'details' => $game->competition_label]);
            @endphp
            <a href="{{ $gcal }}" target="_blank" rel="noopener" class="btn-light mt-5 w-full"><i class="fa-regular fa-calendar-plus"></i> Ajouter à mon agenda</a>
        @endif
    </section>

    <section class="lg:col-span-2">
        <h2 class="h-display mb-4 text-2xl text-navy-900">Dernières confrontations</h2>
        @if ($headToHead->isNotEmpty())
            <div class="grid gap-4 sm:grid-cols-2">
                @foreach ($headToHead as $h2h)
                    <x-game-card :game="$h2h" />
                @endforeach
            </div>
        @else
            <x-empty-state icon="fa-people-arrows" title="Première confrontation">Aucun match précédent entre ces deux équipes dans nos archives.</x-empty-state>
        @endif
    </section>
</div>
@endsection
