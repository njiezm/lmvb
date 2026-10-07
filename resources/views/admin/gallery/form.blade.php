@extends('layouts.admin')

@section('title', $item->exists ? 'Modifier un élément' : 'Ajouter à la galerie')

@section('content')
<x-admin.page :title="$item->exists ? 'Modifier un élément' : 'Ajouter à la galerie'" :back="route('admin.gallery.index')" />

<form action="{{ $item->exists ? route('admin.gallery.update', $item) : route('admin.gallery.store') }}" method="POST" enctype="multipart/form-data" class="card max-w-3xl space-y-5 p-6">
    @csrf
    @if ($item->exists) @method('PUT') @endif

    @unless ($item->exists)
        <fieldset>
            <legend class="text-sm font-medium text-slate-700">Type d'ajout</legend>
            <div class="mt-2 grid grid-cols-2 gap-3">
                @foreach (['image' => ['fa-images', 'Photos (une ou plusieurs)'], 'video' => ['fa-brands fa-youtube', 'Vidéo YouTube']] as $type => [$icon, $label])
                    <label class="flex cursor-pointer items-center gap-3 rounded-xl p-4 ring-1 ring-slate-200 has-[:checked]:bg-navy-50 has-[:checked]:ring-2 has-[:checked]:ring-navy-600">
                        <input type="radio" name="type" value="{{ $type }}" class="text-navy-700" @checked(old('type', 'image') === $type) onchange="document.querySelectorAll('[data-type]').forEach(function(e){e.classList.toggle('hidden', e.dataset.type!=='{{ $type }}')})">
                        <i class="{{ str_starts_with($icon, 'fa-brands') ? $icon : 'fa-solid '.$icon }} text-lg text-navy-600"></i><span class="text-sm font-semibold">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>
    @endunless

    <x-admin.input name="title" label="Titre" :value="$item->title" required />

    @if ($item->exists)
        @if ($item->isVideo())
            <x-admin.input name="video_url" type="url" label="Lien YouTube" :value="$item->video_url" />
        @else
            <x-admin.image name="image" label="Remplacer la photo" :current="$item->image" />
        @endif
    @else
        <div data-type="image" @class(['hidden' => old('type', 'image') !== 'image'])>
            <x-admin.image name="images" label="Photos" multiple help="Jusqu'à 30 photos à la fois (8 Mo max chacune). Elles sont optimisées automatiquement." />
        </div>
        <div data-type="video" @class(['hidden' => old('type', 'image') !== 'video'])>
            <x-admin.input name="video_url" type="url" label="Lien YouTube" placeholder="https://www.youtube.com/watch?v=…" />
        </div>
    @endif

    <div class="grid gap-5 sm:grid-cols-2">
        <x-admin.select name="category" label="Catégorie" :options="\App\Models\Gallery::CATEGORIES" :value="$item->category" required />
        @if (auth()->user()->isSuperAdmin())
            <x-admin.select name="club_id" label="Club" :options="$clubs->pluck('name', 'id')" :value="$item->club_id" placeholder="Ligue (aucun club)" />
        @endif
    </div>
    <x-admin.textarea name="description" label="Description" :value="$item->description" rows="3" />
    <x-admin.input name="credit" label="Crédit (photographe, chaîne…)" :value="$item->credit" />
    <x-admin.toggle name="active" label="Visible sur le site" :checked="$item->exists ? $item->active : true" />

    <div class="flex justify-end gap-3 border-t border-slate-100 pt-5">
        <a href="{{ route('admin.gallery.index') }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </div>
</form>
@endsection
