@extends('layouts.admin')

@section('title', $team->exists ? $team->name : 'Nouvelle sélection')

@section('content')
<x-admin.page :title="$team->exists ? $team->name : 'Nouvelle sélection'" :back="route('admin.teams.index')">
    @if ($team->exists)
        <x-slot:actions>
            <a href="{{ route('selections.show', $team) }}" target="_blank" class="btn-light"><i class="fa-regular fa-eye"></i> Page publique</a>
        </x-slot:actions>
    @endif
</x-admin.page>

<div class="grid gap-6 lg:grid-cols-5">
    <form action="{{ $team->exists ? route('admin.teams.update', $team) : route('admin.teams.store') }}" method="POST" enctype="multipart/form-data" class="card space-y-5 p-6 lg:col-span-2">
        @csrf
        @if ($team->exists) @method('PUT') @endif
        <x-admin.input name="name" label="Nom" :value="$team->name" required />
        <x-admin.select name="category" label="Catégorie" :options="\App\Models\Team::CATEGORIES" :value="$team->category" placeholder="Choisir…" required />
        <x-admin.input name="coach" label="Entraîneur·e" :value="$team->coach" />
        <x-admin.textarea name="description" label="Présentation, palmarès, prochaines échéances" :value="$team->description" rows="6" />
        <x-admin.image name="photo" label="Photo d'équipe" :current="$team->photo" />
        <x-admin.toggle name="active" label="Visible sur le site" :checked="$team->exists ? $team->active : true" />
        <button class="btn-primary w-full"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </form>

    @if ($team->exists)
        <section class="card p-6 lg:col-span-3">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-display text-lg font-bold uppercase text-navy-900">Effectif ({{ $team->players->count() }})</h2>
                <a href="{{ route('admin.teams.players.create', $team) }}" class="btn-primary !py-2 text-sm"><i class="fa-solid fa-user-plus"></i> Ajouter</a>
            </div>
            <ul class="divide-y divide-slate-100">
                @forelse ($team->players as $player)
                    <li class="flex items-center gap-3 py-2.5">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-navy-100 font-display font-bold text-navy-700">
                            @if ($player->photo)<img src="{{ asset($player->photo) }}" alt="" class="h-full w-full object-cover">@else{{ $player->number ?? '–' }}@endif
                        </span>
                        <span class="min-w-0 flex-1"><span class="block truncate font-medium">{{ $player->full_name }}</span><span class="text-xs text-slate-500">{{ $player->position }}@if ($player->club_name) · {{ $player->club_name }}@endif</span></span>
                        <a href="{{ route('admin.players.edit', $player) }}" class="inline-flex h-9 items-center rounded-lg px-2.5 text-navy-600 hover:bg-navy-50"><i class="fa-regular fa-pen-to-square"></i></a>
                        <x-admin.delete :action="route('admin.players.destroy', $player)" confirm="Retirer ce joueur de la sélection ?" />
                    </li>
                @empty
                    <li class="py-8 text-center text-sm text-slate-500">Aucun joueur pour l'instant.</li>
                @endforelse
            </ul>
        </section>
    @endif
</div>
@endsection
