@extends('layouts.app')

@section('title', $event->title)
@section('description', \Illuminate\Support\Str::limit(strip_tags($event->description), 160))
@if ($event->image) @section('og_image', asset($event->image)) @endif

@section('content')
<x-page-header :eyebrow="$event->type_label.' · '.$event->start_date->translatedFormat('d F Y')" :title="$event->title"
               :image="$event->image ?: 'images/photos/beach-2.jpg'" :breadcrumbs="['Beach' => route('beach.index'), $event->title => null]" />

<div class="container-x grid gap-10 py-12 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <dl class="grid gap-4 sm:grid-cols-3">
            <div class="card p-4"><dt class="text-xs uppercase tracking-wider text-slate-500">Dates</dt><dd class="mt-1 font-semibold text-navy-900">{{ $event->start_date->translatedFormat('d M') }}@if (! $event->end_date->isSameDay($event->start_date)) – {{ $event->end_date->translatedFormat('d M Y') }}@else {{ $event->start_date->format('Y') }}@endif</dd></div>
            <div class="card p-4"><dt class="text-xs uppercase tracking-wider text-slate-500">Lieu</dt><dd class="mt-1 font-semibold text-navy-900">{{ $event->location }}</dd></div>
            <div class="card p-4"><dt class="text-xs uppercase tracking-wider text-slate-500">Équipes</dt><dd class="mt-1 font-semibold text-navy-900">{{ $event->registered_teams }}{{ $event->max_teams ? ' / '.$event->max_teams : '' }} inscrites</dd></div>
        </dl>
        <div class="prose prose-slate mt-8 max-w-none">{!! safe_html($event->description) !!}</div>
        @if ($event->prize_pool)<p class="mt-6 font-semibold text-navy-900"><i class="fa-solid fa-trophy mr-2 text-amber-500"></i>Dotation : {{ number_format($event->prize_pool, 0, ',', ' ') }} €</p>@endif
    </div>

    <aside>
        <div class="card sticky top-28 p-6" id="inscription">
            <h2 class="font-display text-2xl font-bold uppercase text-navy-900">Inscription</h2>
            @if ($event->canRegister())
                <form action="{{ route('beach.register', $event) }}" method="POST" class="mt-4 space-y-3">
                    @csrf
                    <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                    @foreach ([['team_name', "Nom de l'équipe", 'text'], ['player1_name', 'Joueur·se 1', 'text'], ['player1_email', 'Email joueur·se 1', 'email'], ['player2_name', 'Joueur·se 2', 'text'], ['player2_email', 'Email joueur·se 2', 'email'], ['phone', 'Téléphone', 'tel']] as [$field, $label, $type])
                        <div>
                            <label for="{{ $field }}" class="text-sm font-medium text-slate-700">{{ $label }}</label>
                            <input id="{{ $field }}" type="{{ $type }}" name="{{ $field }}" value="{{ old($field) }}" required class="input mt-1 @error($field) !border-red-500 @enderror">
                            @error($field)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                    <button class="btn-accent w-full py-3">Inscrire mon équipe</button>
                    <p class="text-xs text-slate-500">Vos données servent uniquement à l'organisation du tournoi par la ligue.</p>
                </form>
            @else
                <p class="mt-3 text-slate-600">Les inscriptions sont fermées pour cet événement.</p>
            @endif
            @if ($event->contact_email)<p class="mt-4 text-sm"><a class="link" href="mailto:{{ $event->contact_email }}"><i class="fa-regular fa-envelope mr-1"></i>{{ $event->contact_email }}</a></p>@endif
        </div>
    </aside>
</div>
@endsection
