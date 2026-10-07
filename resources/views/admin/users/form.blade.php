@extends('layouts.admin')

@section('title', $user->exists ? 'Modifier un compte' : 'Nouveau compte')

@section('content')
<x-admin.page :title="$user->exists ? 'Modifier : '.$user->name : 'Nouveau compte administrateur'" :back="route('admin.users.index')" />

<form action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" class="card max-w-3xl space-y-5 p-6">
    @csrf
    @if ($user->exists) @method('PUT') @endif
    <div class="grid gap-5 sm:grid-cols-2">
        <x-admin.input name="name" label="Nom complet" :value="$user->name" required />
        <x-admin.input name="email" type="email" label="Email de connexion" :value="$user->email" required />
        <x-admin.select name="role" label="Rôle" :options="\App\Models\User::ROLES" :value="in_array($user->role, array_keys(\App\Models\User::ROLES)) ? $user->role : 'club_admin'" required
                        onchange="document.getElementById('club-field').classList.toggle('hidden', this.value !== 'club_admin')" />
        <div id="club-field" @class(['hidden' => old('role', $user->role) === 'super_admin'])>
            <x-admin.select name="club_id" label="Club géré" :options="$clubs->pluck('name', 'id')" :value="$user->club_id" placeholder="Choisir un club…" />
        </div>
        <x-admin.input name="password" type="password" :label="$user->exists ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe'" :required="! $user->exists" autocomplete="new-password" help="10 caractères minimum, avec lettres et chiffres." />
        <x-admin.input name="password_confirmation" type="password" label="Confirmation" autocomplete="new-password" />
    </div>
    <x-admin.toggle name="active" label="Compte actif" :checked="$user->exists ? $user->active : true" help="Un compte désactivé ne peut plus se connecter." />
    <div class="rounded-xl bg-navy-50 p-4 text-sm text-navy-900">
        <p class="font-semibold">Droits d'un administrateur de club</p>
        <p class="mt-1 text-navy-800">Modifier la fiche de son club, publier des actualités et des photos/vidéos au nom du club, consulter les messages adressés au club et les matchs du club. Les résultats officiels restent gérés par l'import FFVolley.</p>
    </div>
    <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.users.index') }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </div>
</form>
@endsection
