@extends('layouts.admin')

@section('title', $club->exists ? $club->name : 'Nouveau club')

@section('content')
@php $super = auth()->user()->isSuperAdmin(); @endphp
<x-admin.page :title="$club->exists ? $club->name : 'Nouveau club'" :subtitle="$club->exists ? 'Les informations ci-dessous sont affichées sur la page publique du club.' : null"
              :back="$super ? route('admin.clubs.index') : null">
    @if ($club->exists)
        <x-slot:actions><a href="{{ route('clubs.show', $club) }}" target="_blank" class="btn-light"><i class="fa-regular fa-eye"></i> Page publique</a></x-slot:actions>
    @endif
</x-admin.page>

<form action="{{ $club->exists ? route('admin.clubs.update', $club) : route('admin.clubs.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3">
    @csrf
    @if ($club->exists) @method('PUT') @endif

    <div class="space-y-6 lg:col-span-2">
        <section class="card space-y-5 p-6">
            <h2 class="font-display text-lg font-bold uppercase text-navy-900">Identité</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.input name="name" label="Nom du club" :value="$club->name" required class="sm:col-span-2" />
                <x-admin.input name="short_name" label="Nom court / sigle" :value="$club->short_name" help="Affiché sur les pastilles (ex. MUC)." />
                @if ($super)
                    <x-admin.input name="ffvb_number" label="N° d'affiliation FFVolley" :value="$club->ffvb_number" help="Sert à rattacher automatiquement les résultats." />
                @endif
                <x-admin.input name="founded_year" type="number" label="Année de création" :value="$club->founded_year" min="1900" :max="date('Y')" />
                <x-admin.input name="members_count" type="number" label="Nombre de licenciés" :value="$club->members_count" min="0" />
            </div>
            <x-admin.textarea name="description" label="Présentation du club" :value="$club->description" rows="6" help="Histoire, palmarès, catégories accueillies, horaires d'entraînement…" />
        </section>

        <section class="card space-y-5 p-6">
            <h2 class="font-display text-lg font-bold uppercase text-navy-900">Coordonnées & accès</h2>
            <div class="grid gap-5 sm:grid-cols-2">
                <x-admin.input name="city" label="Commune" :value="$club->city" />
                <x-admin.input name="venue" label="Salle / gymnase" :value="$club->venue" />
                <x-admin.input name="address" label="Adresse" :value="$club->address" class="sm:col-span-2" />
                <x-admin.input name="phone" type="tel" label="Téléphone (officiel du club)" :value="$club->phone" />
                <x-admin.input name="email" type="email" label="Email (officiel du club)" :value="$club->email" />
                <x-admin.input name="website" type="url" label="Site web" :value="$club->website" placeholder="https://" class="sm:col-span-2" />
                <x-admin.input name="facebook" type="url" label="Page Facebook" :value="$club->facebook" placeholder="https://www.facebook.com/…" />
                <x-admin.input name="instagram" type="url" label="Compte Instagram" :value="$club->instagram" placeholder="https://www.instagram.com/…" />
            </div>
        </section>
    </div>

    <div class="space-y-6">
        <section class="card space-y-5 p-6">
            <h2 class="font-display text-lg font-bold uppercase text-navy-900">Visuel</h2>
            <x-admin.image name="logo" label="Logo" :current="$club->logo" removable="remove_logo" help="Idéalement carré, fond transparent ou blanc." />
            <div>
                <label for="color" class="block text-sm font-medium text-slate-700">Couleur du club</label>
                <div class="mt-1 flex items-center gap-3">
                    <input type="color" id="color" name="color" value="{{ old('color', $club->color ?: '#242868') }}" class="h-11 w-16 cursor-pointer rounded-lg border border-slate-300">
                    <span class="text-xs text-slate-500">Utilisée pour la pastille et l'en-tête de la page du club.</span>
                </div>
            </div>
            @if ($super)
                <x-admin.toggle name="active" label="Club actif (visible sur le site)" :checked="$club->exists ? $club->active : true" />
            @endif
        </section>
        <button class="btn-primary w-full py-3"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </div>
</form>
@endsection
