@extends('layouts.admin')

@section('title', $game->exists ? 'Corriger un match' : 'Nouveau match')

@section('content')
<x-admin.page :title="$game->exists ? 'Corriger un match' : 'Nouveau match (hors FFVolley)'" :back="route('admin.games.index')"
              :subtitle="$game->ffvb_code ? 'Match FFVolley '.$game->ffvb_code.' · '.$game->competition_label : 'Match amical, tournoi ou rencontre non publiée sur la plateforme FFVolley.'" />

@if ($game->ffvb_code)
    <div class="mb-6 flex gap-3 rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-900 ring-1 ring-amber-200">
        <i class="fa-solid fa-circle-info mt-0.5"></i>
        <p>Ce match est importé de la FFVolley. Vos corrections le <strong>verrouillent</strong> : il ne sera plus mis à jour automatiquement, sauf si vous décochez « Verrouiller » ci-dessous.</p>
    </div>
@endif

<form action="{{ $game->exists ? route('admin.games.update', $game) : route('admin.games.store') }}" method="POST" class="space-y-6">
    @csrf
    @if ($game->exists) @method('PUT') @endif

    <section class="card grid gap-5 p-6 sm:grid-cols-2 lg:grid-cols-3">
        <x-admin.select name="competition_id" label="Compétition" placeholder="Aucune (match libre)" :value="$game->competition_id"
                        :options="$competitions->mapWithKeys(fn ($c) => [$c->id => $c->season->name.' · '.$c->code.' · '.$c->display_name])" class="sm:col-span-2" />
        <x-admin.input name="competition" label="Libellé (si match libre)" :value="$game->competition" placeholder="Ex. Match amical, Tournoi de Noël" />
        <x-admin.input name="date_time" type="datetime-local" label="Date et heure" :value="$game->date_time?->format('Y-m-d\TH:i')" />
        <x-admin.input name="matchday" type="number" label="Journée" :value="$game->matchday" min="0" max="99" />
        <x-admin.input name="venue" label="Salle" :value="$game->venue" />
        <x-admin.select name="home_team_id" label="Équipe à domicile" :options="$clubs->pluck('name', 'id')" :value="$game->home_team_id" placeholder="Choisir…" required />
        <x-admin.select name="away_team_id" label="Équipe à l'extérieur" :options="$clubs->pluck('name', 'id')" :value="$game->away_team_id" placeholder="Choisir…" required />
        <x-admin.select name="status" label="Statut" :options="\App\Models\Game::STATUSES" :value="$game->status" required />
    </section>

    <section class="card p-6">
        <h2 class="font-display text-lg font-bold uppercase text-navy-900">Score</h2>
        <div class="mt-4 grid max-w-xs grid-cols-2 gap-4">
            <x-admin.input name="home_score" type="number" label="Sets domicile" :value="$game->home_score" min="0" max="3" />
            <x-admin.input name="away_score" type="number" label="Sets extérieur" :value="$game->away_score" min="0" max="3" />
        </div>
        <p class="mt-5 text-sm font-medium text-slate-700">Détail des sets (facultatif)</p>
        <div class="mt-2 grid grid-cols-2 gap-3 sm:grid-cols-5">
            @for ($i = 0; $i < 5; $i++)
                <div class="rounded-xl bg-slate-50 p-3 ring-1 ring-slate-200">
                    <p class="mb-2 text-xs font-semibold uppercase text-slate-500">Set {{ $i + 1 }}</p>
                    <div class="flex items-center gap-1.5">
                        <input type="number" name="sets[{{ $i }}][home]" min="0" max="99" aria-label="Set {{ $i + 1 }} domicile" value="{{ old("sets.$i.home", $game->set_scores[$i][0] ?? '') }}" class="input !px-2 text-center">
                        <span class="text-slate-400">-</span>
                        <input type="number" name="sets[{{ $i }}][away]" min="0" max="99" aria-label="Set {{ $i + 1 }} extérieur" value="{{ old("sets.$i.away", $game->set_scores[$i][1] ?? '') }}" class="input !px-2 text-center">
                    </div>
                </div>
            @endfor
        </div>
    </section>

    <section class="card grid gap-5 p-6 sm:grid-cols-2">
        <x-admin.input name="referee_1" label="1er arbitre" :value="$game->referee_1" />
        <x-admin.input name="referee_2" label="2e arbitre" :value="$game->referee_2" />
        <x-admin.textarea name="notes" label="Note publique (report, huis clos…)" :value="$game->notes" rows="2" class="sm:col-span-2" />
        @if ($game->ffvb_code)
            <x-admin.toggle name="locked" label="Verrouiller (ne plus écraser par l'import FFVolley)" :checked="true" class="sm:col-span-2" />
        @endif
    </section>

    <div class="flex justify-end gap-3">
        <a href="{{ route('admin.games.index') }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </div>
</form>
@endsection
