@props(['game', 'compact' => false, 'showCompetition' => true])
@php
    $finished = $game->status === 'finished';
    $winner = $game->winner;
@endphp
<a href="{{ route('games.show', $game) }}" {{ $attributes->class(['group block rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200/70 transition hover:-translate-y-0.5 hover:shadow-md hover:ring-navy-200']) }}>
    <div class="mb-3 flex items-center justify-between gap-2 text-xs">
        @if ($showCompetition)
            <span class="truncate font-semibold uppercase tracking-wide text-navy-600">{{ $game->competition_label }}</span>
        @endif
        @if ($game->status === 'live')
            <span class="chip shrink-0 bg-red-100 text-red-700"><span class="h-1.5 w-1.5 animate-pulse rounded-full bg-red-600"></span>En direct</span>
        @elseif ($game->status === 'cancelled')
            <span class="chip shrink-0 bg-slate-100 text-slate-600">Annulé</span>
        @elseif ($game->isForfeit())
            <span class="chip shrink-0 bg-amber-100 text-amber-800">Forfait</span>
        @elseif ($game->matchday)
            <span class="shrink-0 text-slate-400">J{{ $game->matchday }}</span>
        @endif
    </div>

    <div class="space-y-2">
        @foreach (['home' => [$game->homeTeam, $game->home_name, $game->home_score], 'away' => [$game->awayTeam, $game->away_name, $game->away_score]] as $side => [$club, $name, $score])
            <div class="flex items-center gap-3">
                <x-club-badge :club="$club" :name="$name" size="sm" />
                <span @class(['min-w-0 flex-1 truncate text-sm', 'font-bold text-slate-900' => $winner === $side, 'font-medium text-slate-700' => $winner !== $side])>{{ $name }}</span>
                @if ($finished)
                    <span @class(['tabular w-7 rounded-md py-0.5 text-center font-display text-lg font-extrabold',
                                  'bg-navy-800 text-white' => $winner === $side, 'bg-slate-100 text-slate-500' => $winner !== $side])>{{ $score }}</span>
                @endif
            </div>
        @endforeach
    </div>

    <div class="mt-3 flex items-center justify-between gap-2 border-t border-slate-100 pt-3 text-xs text-slate-500">
        @if ($game->date_time)
            <span class="flex shrink-0 items-center gap-1.5"><i class="fa-regular fa-calendar"></i>{{ $game->date_time->translatedFormat($compact ? 'd M' : 'D d M') }}@unless ($finished) · {{ $game->date_time->format('H\hi') }}@endunless</span>
        @else
            <span>Date à définir</span>
        @endif
        @if ($finished && $game->set_scores && ! $compact)
            <span class="tabular min-w-0 truncate text-slate-400">{{ collect($game->set_scores)->map(fn ($s) => $s[0].'-'.$s[1])->implode(' · ') }}</span>
        @elseif ($game->venue)
            <span class="min-w-0 truncate"><i class="fa-solid fa-location-dot mr-1"></i>{{ $game->venue_label }}</span>
        @endif
    </div>
</a>
