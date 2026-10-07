@extends('layouts.admin')

@section('title', $document->exists ? $document->title : 'Nouveau document')

@section('content')
<x-admin.page :title="$document->exists ? $document->title : 'Nouveau document'" :back="route('admin.documents.index')" />

<form action="{{ $document->exists ? route('admin.documents.update', $document) : route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" class="card max-w-3xl space-y-5 p-6">
    @csrf
    @if ($document->exists) @method('PUT') @endif
    <div class="grid gap-5 sm:grid-cols-2">
        <x-admin.input name="title" label="Titre" :value="$document->title" required class="sm:col-span-2" />
        <x-admin.select name="category" label="Rubrique" :options="\App\Models\Document::CATEGORIES" :value="$document->category" required />
    </div>
    <x-admin.textarea name="description" label="Description" :value="$document->description" rows="2" />
    <div class="rounded-xl bg-slate-50 p-4 ring-1 ring-slate-200">
        <x-admin.input name="file" type="file" label="Fichier (PDF, Word, Excel… 20 Mo max)" accept=".pdf,.doc,.docx,.xls,.xlsx,.odt,.ods,.jpg,.png" />
        @if ($document->file)<p class="mt-2 text-xs text-slate-500">Fichier actuel : <a class="link" href="{{ asset($document->file) }}" target="_blank">{{ basename($document->file) }}</a></p>@endif
        <p class="my-3 text-center text-xs font-semibold uppercase text-slate-400">ou</p>
        <x-admin.input name="url" type="url" label="Lien externe" :value="$document->url" placeholder="https://" />
    </div>
    <x-admin.toggle name="active" label="Visible sur le site" :checked="$document->exists ? $document->active : true" />
    <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.documents.index') }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </div>
</form>
@endsection
