@extends('layouts.admin')

@section('title', 'Galerie')

@section('content')
<x-admin.page title="Galerie" :subtitle="$items->total().' élément(s) : photos et vidéos YouTube'">
    <x-slot:actions><a href="{{ route('admin.gallery.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Ajouter</a></x-slot:actions>
</x-admin.page>

<form method="GET" class="card mb-4 grid gap-3 p-4 sm:grid-cols-[1fr_11rem_11rem_auto]">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher…" class="input">
    <select name="category" class="input">
        <option value="">Toutes catégories</option>
        @foreach (\App\Models\Gallery::CATEGORIES as $key => $label)<option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>@endforeach
    </select>
    <select name="type" class="input">
        <option value="">Photos & vidéos</option>
        <option value="image" @selected(request('type') === 'image')>Photos</option>
        <option value="video" @selected(request('type') === 'video')>Vidéos</option>
    </select>
    <button class="btn-light"><i class="fa-solid fa-filter"></i> Filtrer</button>
</form>

@if ($items->isNotEmpty())
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
        @foreach ($items as $item)
            <div class="card group overflow-hidden">
                <div class="relative aspect-[4/3] bg-slate-100">
                    @if ($item->image_url)<img src="{{ $item->image_url }}" alt="" loading="lazy" class="h-full w-full object-cover">@endif
                    @if ($item->isVideo())<span class="absolute left-2 top-2 chip bg-red-600 text-white"><i class="fa-brands fa-youtube"></i> Vidéo</span>@endif
                    @unless ($item->active)<span class="absolute right-2 top-2 chip bg-slate-800 text-white">Masqué</span>@endunless
                </div>
                <div class="p-3">
                    <p class="line-clamp-2 text-sm font-semibold text-slate-900">{{ $item->title }}</p>
                    <p class="mt-0.5 truncate text-xs text-slate-500">{{ $item->category_name }}@if ($item->club) · {{ $item->club->short_name ?? $item->club->name }}@endif</p>
                    <div class="mt-2 flex items-center justify-end gap-1">
                        <a href="{{ route('admin.gallery.edit', $item) }}" class="inline-flex h-9 items-center rounded-lg px-2.5 text-navy-600 hover:bg-navy-50" title="Modifier"><i class="fa-regular fa-pen-to-square"></i></a>
                        <x-admin.delete :action="route('admin.gallery.destroy', $item)" confirm="Supprimer cet élément de la galerie ?" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
@else
    <x-empty-state icon="fa-photo-film" title="Galerie vide"><a class="link" href="{{ route('admin.gallery.create') }}">Ajouter des photos ou une vidéo</a></x-empty-state>
@endif
@endsection
