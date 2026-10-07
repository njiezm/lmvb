@extends('layouts.admin')

@section('title', 'Sélections')

@section('content')
<x-admin.page title="Sélections de Martinique" subtitle="Équipes régionales et effectifs convoqués.">
    <x-slot:actions><a href="{{ route('admin.teams.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Nouvelle sélection</a></x-slot:actions>
</x-admin.page>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    @forelse ($teams as $team)
        <div class="card overflow-hidden">
            <div class="relative aspect-[16/9] bg-navy-900">
                @if ($team->photo)<img src="{{ asset($team->photo) }}" alt="" class="h-full w-full object-cover opacity-90">@endif
                <span class="chip absolute left-3 top-3 bg-white/90 text-navy-800">{{ $team->category_name }}</span>
                @unless ($team->active)<span class="chip absolute right-3 top-3 bg-slate-800 text-white">Masquée</span>@endunless
            </div>
            <div class="p-4">
                <p class="font-semibold text-slate-900">{{ $team->name }}</p>
                <p class="text-xs text-slate-500">{{ $team->players_count }} joueur·se(s)@if ($team->coach) · {{ $team->coach }}@endif</p>
                <div class="mt-3 flex justify-end gap-1">
                    <a href="{{ route('admin.teams.edit', $team) }}" class="btn-light !py-1.5 text-sm"><i class="fa-regular fa-pen-to-square"></i> Gérer</a>
                    <x-admin.delete :action="route('admin.teams.destroy', $team)" confirm="Supprimer cette sélection et son effectif ?" />
                </div>
            </div>
        </div>
    @empty
        <x-empty-state icon="fa-flag" title="Aucune sélection" class="sm:col-span-3" />
    @endforelse
</div>
@endsection
