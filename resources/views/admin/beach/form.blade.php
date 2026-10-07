@extends('layouts.admin')

@section('title', $event->exists ? $event->title : 'Nouvel événement beach')

@section('content')
<x-admin.page :title="$event->exists ? $event->title : 'Nouvel événement beach'" :back="route('admin.beach.index')" />

<form action="{{ $event->exists ? route('admin.beach.update', $event) : route('admin.beach.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-3">
    @csrf
    @if ($event->exists) @method('PUT') @endif
    <div class="card space-y-5 p-6 lg:col-span-2">
        <x-admin.input name="title" label="Titre" :value="$event->title" required />
        <x-admin.textarea name="description" label="Description (programme, format, règlement, dotation…)" :value="$event->description" rows="10" required help="Les retours à la ligne et les balises simples (gras, listes, liens) sont acceptés." />
        <div class="grid gap-5 sm:grid-cols-2">
            <x-admin.input name="start_date" type="datetime-local" label="Début" :value="$event->start_date?->format('Y-m-d\TH:i')" required />
            <x-admin.input name="end_date" type="datetime-local" label="Fin" :value="$event->end_date?->format('Y-m-d\TH:i')" required />
            <x-admin.input name="location" label="Lieu (plage, commune)" :value="$event->location" required />
            <x-admin.input name="contact_email" type="email" label="Email de contact" :value="$event->contact_email" />
        </div>
    </div>
    <div class="space-y-6">
        <div class="card space-y-5 p-6">
            <x-admin.select name="type" label="Type" :options="\App\Models\BeachEvent::TYPES" :value="$event->type" required />
            <x-admin.select name="category" label="Catégorie" :options="['M' => 'Masculin', 'F' => 'Féminin', 'mixte' => 'Mixte', 'jeunes' => 'Jeunes', 'open' => 'Open']" :value="$event->category" placeholder="—" />
            <x-admin.select name="status" label="Statut" :options="\App\Models\BeachEvent::STATUSES" :value="$event->status" required />
            <x-admin.input name="max_teams" type="number" label="Nombre max. d'équipes" :value="$event->max_teams" min="2" />
            <x-admin.input name="prize_pool" type="number" label="Dotation (€)" :value="$event->prize_pool" min="0" step="1" />
            <x-admin.toggle name="registration_open" label="Inscriptions en ligne ouvertes" :checked="$event->registration_open" />
        </div>
        <div class="card p-6"><x-admin.image name="image" label="Visuel" :current="$event->image" /></div>
        <button class="btn-primary w-full py-3"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </div>
</form>
@endsection
