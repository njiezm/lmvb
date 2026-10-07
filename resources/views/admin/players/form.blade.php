@extends('layouts.admin')

@section('title', $player->exists ? $player->full_name : 'Ajouter un joueur')

@section('content')
<x-admin.page :title="$player->exists ? $player->full_name : 'Ajouter un joueur'" :subtitle="$team->name" :back="route('admin.teams.edit', $team)" />

<form action="{{ $player->exists ? route('admin.players.update', $player) : route('admin.teams.players.store', $team) }}" method="POST" enctype="multipart/form-data" class="card max-w-3xl space-y-5 p-6">
    @csrf
    @if ($player->exists) @method('PUT') @endif
    <div class="grid gap-5 sm:grid-cols-2">
        <x-admin.input name="first_name" label="Prénom" :value="$player->first_name" required />
        <x-admin.input name="last_name" label="Nom" :value="$player->last_name" required />
        <x-admin.select name="position" label="Poste" :options="\App\Models\Player::POSITIONS" :value="$player->position" placeholder="Choisir…" required />
        <x-admin.input name="number" type="number" label="Numéro de maillot" :value="$player->number" min="0" max="99" />
        <x-admin.input name="height" type="number" label="Taille (cm)" :value="$player->height" min="120" max="240" />
        <x-admin.input name="birth_date" type="date" label="Date de naissance" :value="$player->birth_date?->format('Y-m-d')" />
        <x-admin.input name="club_name" label="Club" :value="$player->club_name" class="sm:col-span-2" />
    </div>
    <x-admin.image name="photo" label="Photo" :current="$player->photo" />
    <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.teams.edit', $team) }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </div>
</form>
@endsection
