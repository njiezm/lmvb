@extends('layouts.admin')

@section('title', 'Partenaires')

@section('content')
<x-admin.page title="Partenaires" subtitle="Logos affichés en bas de la page d'accueil et sur la page « La Ligue ».">
    <x-slot:actions><a href="{{ route('admin.partners.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Ajouter</a></x-slot:actions>
</x-admin.page>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @forelse ($partners as $partner)
        <div class="card flex flex-col items-center p-5 text-center">
            <div class="flex h-20 items-center">@if ($partner->logo)<img src="{{ asset($partner->logo) }}" alt="" class="max-h-20 max-w-full">@else<i class="fa-solid fa-handshake text-3xl text-slate-300"></i>@endif</div>
            <p class="mt-3 font-semibold text-slate-900">{{ $partner->name }}</p>
            @unless ($partner->active)<span class="chip mt-1 bg-slate-100 text-slate-500">Masqué</span>@endunless
            <div class="mt-3 flex gap-1">
                <a href="{{ route('admin.partners.edit', $partner) }}" class="inline-flex h-9 items-center rounded-lg px-2.5 text-navy-600 hover:bg-navy-50"><i class="fa-regular fa-pen-to-square"></i></a>
                <x-admin.delete :action="route('admin.partners.destroy', $partner)" confirm="Supprimer ce partenaire ?" />
            </div>
        </div>
    @empty
        <x-empty-state icon="fa-handshake" title="Aucun partenaire" class="sm:col-span-4">Ajoutez la CTM, les sponsors et les institutions qui soutiennent la ligue.</x-empty-state>
    @endforelse
</div>
@endsection
