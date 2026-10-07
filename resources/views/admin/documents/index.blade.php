@extends('layouts.admin')

@section('title', 'Documents')

@section('content')
<x-admin.page title="Documents officiels" subtitle="Règlements, formulaires, PV d'AG… affichés sur la page « La Ligue ».">
    <x-slot:actions><a href="{{ route('admin.documents.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Ajouter</a></x-slot:actions>
</x-admin.page>

<div class="card divide-y divide-slate-100">
    @forelse ($documents as $doc)
        <div class="flex flex-wrap items-center gap-3 px-4 py-3">
            <i class="fa-solid {{ $doc->file ? 'fa-file-pdf text-bordeaux-600' : 'fa-link text-navy-500' }} w-5 text-center"></i>
            <div class="min-w-0 flex-1"><p class="truncate font-medium text-slate-900">{{ $doc->title }}</p><p class="text-xs text-slate-500">{{ $doc->category_label }}@unless ($doc->active) · masqué @endunless</p></div>
            <a href="{{ $doc->link }}" target="_blank" class="inline-flex h-9 items-center rounded-lg px-2.5 text-slate-500 hover:bg-slate-100"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            <a href="{{ route('admin.documents.edit', $doc) }}" class="inline-flex h-9 items-center rounded-lg px-2.5 text-navy-600 hover:bg-navy-50"><i class="fa-regular fa-pen-to-square"></i></a>
            <x-admin.delete :action="route('admin.documents.destroy', $doc)" confirm="Supprimer ce document ?" />
        </div>
    @empty
        <p class="px-4 py-12 text-center text-slate-500">Aucun document.</p>
    @endforelse
</div>
@endsection
