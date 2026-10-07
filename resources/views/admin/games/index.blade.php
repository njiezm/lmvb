@extends('layouts.admin')

@section('title', 'Matchs')

@section('content')
@php $super = auth()->user()->isSuperAdmin(); @endphp
<x-admin.page :title="$super ? 'Matchs' : 'Matchs du club'" :subtitle="$games->total().' match(s) · importés automatiquement depuis la FFVolley'">
    @if ($super)
        <x-slot:actions><a href="{{ route('admin.games.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Match hors FFVolley</a></x-slot:actions>
    @endif
</x-admin.page>

<form method="GET" class="card mb-4 grid gap-3 p-4 sm:grid-cols-2 lg:grid-cols-[9rem_1fr_1fr_10rem_auto]">
    <select name="saison" class="input" onchange="this.form.competition.value='';this.form.submit()">
        @foreach ($seasons as $s)<option value="{{ $s->slug }}" @selected($season && $s->id === $season->id)>{{ $s->name }}</option>@endforeach
    </select>
    <select name="competition" class="input">
        <option value="">Toutes les poules</option>
        @foreach ($competitions as $c)<option value="{{ $c->id }}" @selected(request('competition') == $c->id)>{{ $c->code }} · {{ $c->display_name }}</option>@endforeach
    </select>
    @if ($super)
        <select name="club" class="input">
            <option value="">Tous les clubs</option>
            @foreach ($clubs as $club)<option value="{{ $club->id }}" @selected(request('club') == $club->id)>{{ $club->name }}</option>@endforeach
        </select>
    @endif
    <select name="status" class="input">
        <option value="">Tous statuts</option>
        @foreach (\App\Models\Game::STATUSES as $k => $l)<option value="{{ $k }}" @selected(request('status') === $k)>{{ $l }}</option>@endforeach
    </select>
    <button class="btn-light"><i class="fa-solid fa-filter"></i> Filtrer</button>
</form>

<div class="card overflow-hidden">
    <div class="scrollbar-thin overflow-x-auto">
        <table class="w-full min-w-[52rem] text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                <tr><th class="px-4 py-3">Date</th><th class="px-4 py-3">Poule</th><th class="px-4 py-3">Rencontre</th><th class="px-4 py-3 text-center">Score</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($games as $game)
                    <tr class="hover:bg-slate-50">
                        <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $game->date_time?->format('d/m/Y H:i') ?? 'À définir' }}</td>
                        <td class="px-4 py-3"><span class="font-mono text-xs">{{ $game->competitionModel?->code ?? '—' }}</span>@if ($game->matchday) <span class="text-xs text-slate-400">J{{ $game->matchday }}</span>@endif</td>
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $game->home_name }}</span> <span class="text-slate-400">–</span> <span class="font-medium">{{ $game->away_name }}</span>
                            @if ($game->venue)<span class="block text-xs text-slate-400">{{ $game->venue_label }}</span>@endif
                        </td>
                        <td class="tabular whitespace-nowrap px-4 py-3 text-center font-bold text-navy-800">{{ $game->status === 'finished' ? $game->home_score.' - '.$game->away_score : '' }}</td>
                        <td class="px-4 py-3">
                            <x-admin.status :value="$game->status" :label="$game->status_label" />
                            @if ($game->locked)<i class="fa-solid fa-lock ml-1 text-xs text-amber-500" title="Modifié manuellement : non écrasé par l'import"></i>@endif
                            @if ($game->ffvb_code)<span class="ml-1 font-mono text-[0.65rem] text-slate-400">{{ $game->ffvb_code }}</span>@endif
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('games.show', $game) }}" target="_blank" class="inline-flex h-9 items-center rounded-lg px-2.5 text-slate-500 hover:bg-slate-100" title="Voir"><i class="fa-regular fa-eye"></i></a>
                            @if ($super)
                                <a href="{{ route('admin.games.edit', $game) }}" class="inline-flex h-9 items-center rounded-lg px-2.5 text-navy-600 hover:bg-navy-50" title="Corriger"><i class="fa-regular fa-pen-to-square"></i></a>
                                @unless ($game->ffvb_code)<x-admin.delete :action="route('admin.games.destroy', $game)" confirm="Supprimer ce match ?" />@endunless
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">Aucun match.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $games->links() }}</div>
@endsection
