@extends('layouts.admin')

@section('title', $partner->exists ? $partner->name : 'Nouveau partenaire')

@section('content')
<x-admin.page :title="$partner->exists ? $partner->name : 'Nouveau partenaire'" :back="route('admin.partners.index')" />

<form action="{{ $partner->exists ? route('admin.partners.update', $partner) : route('admin.partners.store') }}" method="POST" enctype="multipart/form-data" class="card max-w-2xl space-y-5 p-6">
    @csrf
    @if ($partner->exists) @method('PUT') @endif
    <x-admin.input name="name" label="Nom" :value="$partner->name" required />
    <x-admin.input name="url" type="url" label="Site web" :value="$partner->url" placeholder="https://" />
    <x-admin.input name="sort_order" type="number" label="Ordre d'affichage" :value="$partner->sort_order" required />
    <x-admin.image name="logo" label="Logo" :current="$partner->logo" help="PNG transparent idéalement." />
    <x-admin.toggle name="active" label="Visible sur le site" :checked="$partner->exists ? $partner->active : true" />
    <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.partners.index') }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </div>
</form>
@endsection
