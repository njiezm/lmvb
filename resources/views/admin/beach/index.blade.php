@extends('layouts.admin')

@section('title', 'Beach-volley')

@section('content')
<x-admin.page title="Beach-volley" subtitle="Tournois, stages et inscriptions en ligne.">
    <x-slot:actions><a href="{{ route('admin.beach.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Nouvel événement</a></x-slot:actions>
</x-admin.page>

<div class="card overflow-hidden">
    <div class="scrollbar-thin overflow-x-auto">
        <table class="w-full min-w-[44rem] text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                <tr><th class="px-4 py-3">Événement</th><th class="px-4 py-3">Dates</th><th class="px-4 py-3">Inscriptions</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($events as $event)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><p class="font-semibold text-slate-900">{{ $event->title }}</p><p class="text-xs text-slate-500">{{ $event->type_label }} · {{ $event->location }}</p></td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-600">{{ $event->start_date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.beach.registrations', $event) }}" class="link">{{ $event->registrations_count }} équipe(s)</a>@if ($event->max_teams) / {{ $event->max_teams }}@endif
                            @if ($event->registration_open)<span class="chip ml-1 bg-emerald-100 text-emerald-800">ouvertes</span>@endif
                        </td>
                        <td class="px-4 py-3"><x-admin.status :value="$event->status" :label="$event->status_label" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('beach.show', $event) }}" target="_blank" class="inline-flex h-9 items-center rounded-lg px-2.5 text-slate-500 hover:bg-slate-100"><i class="fa-regular fa-eye"></i></a>
                            <a href="{{ route('admin.beach.edit', $event) }}" class="inline-flex h-9 items-center rounded-lg px-2.5 text-navy-600 hover:bg-navy-50"><i class="fa-regular fa-pen-to-square"></i></a>
                            <x-admin.delete :action="route('admin.beach.destroy', $event)" confirm="Supprimer cet événement et ses inscriptions ?" />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-slate-500">Aucun événement. Créez le prochain tournoi pour ouvrir les inscriptions en ligne.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $events->links() }}</div>
@endsection
