@extends('layouts.admin')

@section('title', 'Compétitions')

@section('content')
<x-admin.page title="Compétitions & synchronisation" subtitle="Poules publiées par la FFVolley pour la ligue (code LIMART). Elles sont créées et mises à jour automatiquement.">
    <x-slot:actions>
        <form method="GET">
            <select name="saison" class="input" onchange="this.form.submit()" aria-label="Saison">
                @foreach ($seasons as $s)<option value="{{ $s->slug }}" @selected($season && $s->id === $season->id)>Saison {{ $s->name }}</option>@endforeach
            </select>
        </form>
        <form action="{{ route('admin.sync') }}" method="POST">
            @csrf
            <input type="hidden" name="season" value="{{ $season?->name }}">
            <button class="btn-accent" onclick="this.disabled=true;this.innerHTML='<i class=\'fa-solid fa-rotate fa-spin\'></i> En cours…';this.form.submit()"><i class="fa-solid fa-rotate"></i> Synchroniser {{ $season?->name }}</button>
        </form>
    </x-slot:actions>
</x-admin.page>

<div class="mb-6 grid gap-4 md:grid-cols-3">
    <div class="card p-5 text-sm"><p class="font-semibold text-slate-900"><i class="fa-solid fa-clock mr-2 text-navy-500"></i>Cron o2switch</p><p class="mt-1 text-slate-600">Import toutes les 30 min (7h-minuit) et chaque nuit à 3h30.</p></div>
    <div class="card p-5 text-sm"><p class="font-semibold text-slate-900"><i class="fa-solid fa-person-walking mr-2 text-navy-500"></i>À la visite</p><p class="mt-1 text-slate-600">Si les données ont plus de {{ config('lmvb.ffvb.interval_minutes') }} min, une visite relance l'import en arrière-plan.</p></div>
    <div class="card p-5 text-sm"><p class="font-semibold text-slate-900"><i class="fa-solid fa-lock mr-2 text-navy-500"></i>Corrections manuelles</p><p class="mt-1 text-slate-600">Un match corrigé dans l'admin est verrouillé et n'est plus écrasé.</p></div>
</div>

<div class="space-y-3">
    @forelse ($competitions as $competition)
        <details class="card group">
            <summary class="flex cursor-pointer list-none flex-wrap items-center gap-3 p-4">
                <span class="w-12 font-mono text-sm font-bold text-navy-700">{{ $competition->code }}</span>
                <span class="min-w-0 flex-1">
                    <span class="block font-semibold text-slate-900">{{ $competition->display_name }}</span>
                    <span class="text-xs text-slate-500">{{ $competition->category_label }} · {{ $competition->gender_label ?: 'Mixte' }} · {{ $competition->phase_label }} · {{ $competition->games_count }} matchs · {{ $competition->standings_count }} équipes classées @if ($competition->locked_count)· <span class="text-amber-600">{{ $competition->locked_count }} verrouillé(s)</span>@endif</span>
                </span>
                <span class="text-xs text-slate-500">{{ $competition->synced_at ? 'Synchro '.$competition->synced_at->diffForHumans() : '' }}</span>
                <x-admin.status :value="$competition->active ? 'active' : 'inactive'" :label="$competition->active ? 'Visible' : 'Masquée'" />
                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition group-open:rotate-180"></i>
            </summary>
            <form action="{{ route('admin.competitions.update', $competition) }}" method="POST" class="grid gap-4 border-t border-slate-100 p-4 sm:grid-cols-2 lg:grid-cols-6">
                @csrf @method('PUT')
                <x-admin.input name="name" label="Nom" :value="$competition->name" class="lg:col-span-2" />
                <x-admin.select name="category" label="Catégorie" :options="\App\Models\Competition::CATEGORIES" :value="$competition->category" />
                <x-admin.select name="gender" label="Genre" :options="['M' => 'Masculin', 'F' => 'Féminin', 'X' => 'Mixte']" :value="$competition->gender" placeholder="—" />
                <x-admin.select name="phase" label="Phase" :options="\App\Models\Competition::PHASES" :value="$competition->phase" />
                <x-admin.input name="sort_order" type="number" label="Ordre" :value="$competition->sort_order" />
                <div class="flex flex-wrap items-center justify-between gap-3 sm:col-span-2 lg:col-span-6">
                    <x-admin.toggle name="active" label="Afficher sur le site" :checked="$competition->active" />
                    <div class="flex gap-2">
                        <a href="{{ $competition->ffvbUrl() }}" target="_blank" class="btn-ghost text-sm">FFVolley <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i></a>
                        <a href="{{ route('admin.games.index', ['saison' => $season->slug, 'competition' => $competition->id]) }}" class="btn-light text-sm">Matchs</a>
                        <button class="btn-primary text-sm">Enregistrer</button>
                    </div>
                </div>
            </form>
        </details>
    @empty
        <x-empty-state icon="fa-trophy" title="Aucune compétition">Lancez une synchronisation pour importer les poules de la saison.</x-empty-state>
    @endforelse
</div>
@endsection
