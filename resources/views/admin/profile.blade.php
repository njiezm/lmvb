@extends('layouts.admin')

@section('title', 'Mon profil')

@section('content')
<x-admin.page title="Mon profil" :subtitle="$user->role_label.($user->club ? ' · '.$user->club->name : '')" />

<form action="{{ route('admin.profile.update') }}" method="POST" class="grid max-w-4xl gap-6 lg:grid-cols-2">
    @csrf @method('PUT')
    <section class="card space-y-5 p-6">
        <h2 class="font-display text-lg font-bold uppercase text-navy-900">Informations</h2>
        <x-admin.input name="name" label="Nom" :value="$user->name" required />
        <x-admin.input name="email" type="email" label="Email de connexion" :value="$user->email" required />
    </section>
    <section class="card space-y-5 p-6">
        <h2 class="font-display text-lg font-bold uppercase text-navy-900">Changer le mot de passe</h2>
        <x-admin.input name="current_password" type="password" label="Mot de passe actuel" autocomplete="current-password" />
        <x-admin.input name="password" type="password" label="Nouveau mot de passe" autocomplete="new-password" help="10 caractères minimum, avec lettres et chiffres." />
        <x-admin.input name="password_confirmation" type="password" label="Confirmation" autocomplete="new-password" />
    </section>
    <div class="lg:col-span-2"><button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button></div>
</form>
@endsection
