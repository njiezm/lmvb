@extends('layouts.app')

@section('title', $team->name)
@section('description', \Illuminate\Support\Str::limit($team->description ?: $team->name.' : sélection régionale de volley-ball de Martinique.', 160))

@section('content')
<x-page-header :eyebrow="$team->category_name" :title="$team->name" :image="$team->photo ?: ($team->gender === 'F' ? 'images/photos/indoor-smash-2.jpg' : 'images/photos/indoor-bloc.jpg')"
               :breadcrumbs="['Sélections' => route('selections.index'), $team->name => null]">
    @if ($team->coach)<p class="mt-4 text-navy-100"><i class="fa-solid fa-clipboard-user mr-2"></i>Entraîneur·e : <strong class="text-white">{{ $team->coach }}</strong></p>@endif
</x-page-header>

<div class="container-x py-12">
    @if ($team->description)
        <div class="prose prose-slate mb-12 max-w-3xl">{!! nl2br(e($team->description)) !!}</div>
    @endif

    <h2 class="h-display mb-6 text-3xl text-navy-900">Effectif</h2>
    @if ($team->players->isNotEmpty())
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($team->players as $player)
                <div class="card overflow-hidden text-center">
                    <div class="relative aspect-[4/5] bg-gradient-to-b from-navy-100 to-navy-200">
                        @if ($player->photo)
                            <img src="{{ asset($player->photo) }}" alt="{{ $player->full_name }}" loading="lazy" class="h-full w-full object-cover">
                        @else
                            <i class="fa-solid fa-user absolute inset-0 m-auto h-fit text-6xl text-white/80"></i>
                        @endif
                        @if ($player->number !== null)<span class="absolute left-3 top-3 font-display text-4xl font-extrabold text-white drop-shadow">{{ $player->number }}</span>@endif
                    </div>
                    <div class="p-3">
                        <p class="font-display text-lg font-bold uppercase leading-tight text-navy-900">{{ $player->first_name }}<br>{{ $player->last_name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $player->position }}@if ($player->height) · {{ $player->height }} cm @endif</p>
                        @if ($player->club_name)<p class="text-xs text-navy-600">{{ $player->club_name }}</p>@endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <x-empty-state icon="fa-users" title="Effectif bientôt publié">La liste des joueurs et joueuses sera communiquée lors de la prochaine convocation.</x-empty-state>
    @endif
</div>
@endsection
