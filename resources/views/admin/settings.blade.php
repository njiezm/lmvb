@extends('layouts.admin')

@section('title', 'Réglages du site')

@section('content')
<x-admin.page title="Réglages du site" subtitle="Textes, coordonnées, réseaux sociaux et mot de la présidente." />

<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
    @csrf @method('PUT')

    <section class="card grid gap-5 p-6 lg:grid-cols-3">
        <div><h2 class="font-display text-lg font-bold uppercase text-navy-900">Le mot de la présidente</h2><p class="mt-1 text-sm text-slate-500">Section « Édito » de l'accueil et de la page « La Ligue ». Séparez les paragraphes par une ligne vide.</p></div>
        <div class="grid gap-5 sm:grid-cols-2 lg:col-span-2">
            <x-admin.input name="president_name" label="Nom" :value="$values['president_name']" />
            <x-admin.input name="president_title" label="Fonction" :value="$values['president_title']" />
            <x-admin.textarea name="president_message" label="Message" :value="$values['president_message']" rows="10" class="sm:col-span-2" />
            <x-admin.image name="president_photo_file" label="Photo (format portrait conseillé)" :current="$values['president_photo']" removable="remove_president_photo" class="sm:col-span-2" />
        </div>
    </section>

    <section class="card grid gap-5 p-6 lg:grid-cols-3">
        <div><h2 class="font-display text-lg font-bold uppercase text-navy-900">Identité & accueil</h2><p class="mt-1 text-sm text-slate-500">Nom, slogan et textes du bandeau principal.</p></div>
        <div class="grid gap-5 sm:grid-cols-2 lg:col-span-2">
            <x-admin.input name="site_name" label="Nom du site" :value="$values['site_name']" required class="sm:col-span-2" />
            <x-admin.textarea name="site_tagline" label="Description (moteurs de recherche, pied de page)" :value="$values['site_tagline']" rows="2" class="sm:col-span-2" />
            <x-admin.input name="hero_title" label="Titre de l'accueil" :value="$values['hero_title']" class="sm:col-span-2" />
            <x-admin.textarea name="hero_subtitle" label="Sous-titre de l'accueil" :value="$values['hero_subtitle']" rows="2" class="sm:col-span-2" />
        </div>
    </section>

    <section class="card grid gap-5 p-6 lg:grid-cols-3">
        <div><h2 class="font-display text-lg font-bold uppercase text-navy-900">Coordonnées</h2><p class="mt-1 text-sm text-slate-500">Les messages du formulaire de contact sont aussi envoyés à cet email.</p></div>
        <div class="grid gap-5 sm:grid-cols-2 lg:col-span-2">
            <x-admin.input name="address" label="Adresse du siège" :value="$values['address']" class="sm:col-span-2" />
            <x-admin.input name="phone" label="Téléphone" :value="$values['phone']" />
            <x-admin.input name="email" type="email" label="Email" :value="$values['email']" required />
        </div>
    </section>

    <section class="card grid gap-5 p-6 lg:grid-cols-3">
        <div><h2 class="font-display text-lg font-bold uppercase text-navy-900">Réseaux & liens</h2></div>
        <div class="grid gap-5 sm:grid-cols-2 lg:col-span-2">
            <x-admin.input name="facebook_url" type="url" label="Facebook" :value="$values['facebook_url']" />
            <x-admin.input name="instagram_url" type="url" label="Instagram" :value="$values['instagram_url']" />
            <x-admin.input name="youtube_url" type="url" label="YouTube" :value="$values['youtube_url']" />
            <x-admin.input name="license_url" type="url" label="Lien « Prendre sa licence »" :value="$values['license_url']" />
        </div>
    </section>

    <div class="flex justify-end"><button class="btn-primary px-8 py-3"><i class="fa-solid fa-floppy-disk"></i> Enregistrer les réglages</button></div>
</form>
@endsection
