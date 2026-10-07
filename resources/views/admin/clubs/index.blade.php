@extends('layouts.admin')

@section('title', 'Clubs')

@section('content')
<x-admin.page title="Clubs" :subtitle="$clubs->total().' club(s) · créés automatiquement par l\'import FFVolley, complétez leurs fiches'">
    <x-slot:actions><a href="{{ route('admin.clubs.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Nouveau club</a></x-slot:actions>
</x-admin.page>

<form method="GET" class="card mb-4 grid gap-3 p-4 sm:grid-cols-[1fr_10rem_12rem_auto]">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Nom, commune ou n° FFVolley…" class="input">
    <select name="status" class="input">
        <option value="">Tous</option>
        <option value="1" @selected(request('status') === '1')>Actifs</option>
        <option value="0" @selected(request('status') === '0')>Inactifs</option>
    </select>
    <select name="sort" class="input">
        <option value="name">Nom (A-Z)</option>
        <option value="members" @selected(request('sort') === 'members')>Nombre de licenciés</option>
    </select>
    <button class="btn-light"><i class="fa-solid fa-filter"></i> Filtrer</button>
</form>

<div class="card overflow-hidden">
    <div class="scrollbar-thin overflow-x-auto">
        <table class="w-full min-w-[48rem] text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                <tr><th class="px-4 py-3">Club</th><th class="px-4 py-3">Commune</th><th class="px-4 py-3">N° FFVolley</th><th class="px-4 py-3 text-center">Matchs</th><th class="px-4 py-3 text-center">Admins</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($clubs as $club)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <x-club-badge :club="$club" size="md" />
                                <div class="min-w-0">
                                    <a href="{{ route('admin.clubs.edit', $club) }}" class="font-semibold text-slate-900 hover:text-navy-700">{{ $club->name }}</a>
                                    <p class="text-xs text-slate-500">{{ $club->short_name }}@if (! $club->description && $club->active) · <span class="text-amber-600">fiche à compléter</span>@endif</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $club->city ?: '—' }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $club->ffvb_number ?: '—' }}</td>
                        <td class="px-4 py-3 text-center">{{ $club->home_games_count + $club->away_games_count }}</td>
                        <td class="px-4 py-3 text-center">{{ $club->admins_count ?: '—' }}</td>
                        <td class="px-4 py-3"><x-admin.status :value="$club->active ? 'active' : 'inactive'" :label="$club->active ? 'Actif' : 'Inactif'" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('clubs.show', $club) }}" target="_blank" class="inline-flex h-9 items-center rounded-lg px-2.5 text-slate-500 hover:bg-slate-100" title="Voir"><i class="fa-regular fa-eye"></i></a>
                            <a href="{{ route('admin.clubs.edit', $club) }}" class="inline-flex h-9 items-center rounded-lg px-2.5 text-navy-600 hover:bg-navy-50" title="Modifier"><i class="fa-regular fa-pen-to-square"></i></a>
                            <x-admin.delete :action="route('admin.clubs.destroy', $club)" confirm="Archiver ce club ? Ses matchs resteront visibles." />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">Aucun club.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $clubs->links() }}</div>
@endsection
