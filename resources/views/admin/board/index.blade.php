@extends('layouts.admin')

@section('title', 'Comité directeur')

@section('content')
<x-admin.page title="Comité directeur" subtitle="Affiché sur la page « La Ligue ». La photo et le mot de la présidente se gèrent dans Réglages.">
    <x-slot:actions><a href="{{ route('admin.board.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Ajouter un membre</a></x-slot:actions>
</x-admin.page>

<div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    @forelse ($members as $member)
        <div class="card p-5 text-center">
            <div class="mx-auto h-24 w-24 overflow-hidden rounded-full bg-navy-100">
                @if ($member->photo)<img src="{{ asset($member->photo) }}" alt="" class="h-full w-full object-cover">@else<span class="flex h-full items-center justify-center font-display text-2xl font-bold text-navy-400">{{ \App\Support\Text::initials($member->name) }}</span>@endif
            </div>
            <p class="mt-3 font-semibold text-slate-900">{{ $member->name }}</p>
            <p class="text-sm text-bordeaux-600">{{ $member->role }}</p>
            @unless ($member->active)<span class="chip mt-1 bg-slate-100 text-slate-500">Masqué</span>@endunless
            <div class="mt-3 flex justify-center gap-1">
                <a href="{{ route('admin.board.edit', $member) }}" class="inline-flex h-9 items-center rounded-lg px-2.5 text-navy-600 hover:bg-navy-50"><i class="fa-regular fa-pen-to-square"></i></a>
                <x-admin.delete :action="route('admin.board.destroy', $member)" confirm="Retirer ce membre ?" />
            </div>
        </div>
    @empty
        <x-empty-state icon="fa-user-tie" title="Aucun membre" class="sm:col-span-4" />
    @endforelse
</div>
@endsection
