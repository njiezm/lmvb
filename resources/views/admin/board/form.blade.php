@extends('layouts.admin')

@section('title', $member->exists ? $member->name : 'Nouveau membre')

@section('content')
<x-admin.page :title="$member->exists ? $member->name : 'Nouveau membre du comité'" :back="route('admin.board.index')" />

<form action="{{ $member->exists ? route('admin.board.update', $member) : route('admin.board.store') }}" method="POST" enctype="multipart/form-data" class="card max-w-3xl space-y-5 p-6">
    @csrf
    @if ($member->exists) @method('PUT') @endif
    <div class="grid gap-5 sm:grid-cols-2">
        <x-admin.input name="name" label="Nom" :value="$member->name" required />
        <x-admin.input name="role" label="Fonction" :value="$member->role" required placeholder="Ex. Trésorier, Secrétaire générale" />
        <x-admin.input name="sort_order" type="number" label="Ordre d'affichage" :value="$member->sort_order" required help="1 = en premier." />
    </div>
    <x-admin.textarea name="bio" label="Présentation" :value="$member->bio" rows="4" />
    <x-admin.image name="photo" label="Photo" :current="$member->photo" />
    <x-admin.toggle name="active" label="Visible sur le site" :checked="$member->exists ? $member->active : true" />
    <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.board.index') }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </div>
</form>
@endsection
