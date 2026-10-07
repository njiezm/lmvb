@extends('layouts.app')

@section('title', 'Contact')
@section('description', 'Contactez la Ligue Martiniquaise de Volley-Ball ou un club : licences, compétitions, beach-volley, arbitrage, partenariats.')

@section('content')
<x-page-header eyebrow="Une question ?" title="Contact" image="images/photos/martinique-fort-de-france.jpg"
               subtitle="Écrivez à la ligue ou directement à un club : nous vous répondons rapidement." :breadcrumbs="['Contact' => null]" />

<div class="container-x grid gap-10 py-12 lg:grid-cols-3">
    <form action="{{ route('contact.store') }}" method="POST" class="card space-y-5 p-6 sm:p-8 lg:col-span-2" novalidate>
        @csrf
        <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
        @if ($errors->any())
            <div class="rounded-xl bg-red-50 px-4 py-3 text-sm text-red-800 ring-1 ring-red-200" role="alert">Merci de corriger les champs indiqués.</div>
        @endif

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="name" class="text-sm font-medium text-slate-700">Nom et prénom *</label>
                <input id="name" name="name" value="{{ old('name') }}" required autocomplete="name" class="input mt-1 @error('name') !border-red-500 @enderror">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="text-sm font-medium text-slate-700">Email *</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="input mt-1 @error('email') !border-red-500 @enderror">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="phone" class="text-sm font-medium text-slate-700">Téléphone</label>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel" class="input mt-1 @error('phone') !border-red-500 @enderror">
                @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="club_id" class="text-sm font-medium text-slate-700">Destinataire</label>
                <select id="club_id" name="club_id" class="input mt-1">
                    <option value="">La ligue (secrétariat)</option>
                    @foreach ($clubs as $club)
                        <option value="{{ $club->id }}" @selected(old('club_id', $selectedClub) == $club->id)>{{ $club->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label for="subject" class="text-sm font-medium text-slate-700">Sujet *</label>
            <select id="subject" name="subject" required class="input mt-1 @error('subject') !border-red-500 @enderror">
                <option value="">Choisir…</option>
                @foreach ($subjects as $subject)
                    <option @selected(old('subject') === $subject)>{{ $subject }}</option>
                @endforeach
            </select>
            @error('subject')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="message" class="text-sm font-medium text-slate-700">Message *</label>
            <textarea id="message" name="message" rows="7" required class="input mt-1 @error('message') !border-red-500 @enderror">{{ old('message') }}</textarea>
            @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="flex flex-col-reverse items-start justify-between gap-4 sm:flex-row sm:items-center">
            <p class="text-xs text-slate-500">Vos données servent uniquement à vous répondre. <a href="{{ route('legal') }}" class="link">En savoir plus</a></p>
            <button class="btn-accent px-8 py-3"><i class="fa-regular fa-paper-plane"></i> Envoyer</button>
        </div>
    </form>

    <aside class="space-y-5">
        <div class="card p-6">
            <h2 class="font-display text-xl font-bold uppercase text-navy-900">Secrétariat de la ligue</h2>
            <ul class="mt-4 space-y-3 text-sm text-slate-700">
                @if (setting('address'))<li class="flex gap-3"><i class="fa-solid fa-location-dot mt-0.5 w-4 text-navy-400"></i>{{ setting('address') }}</li>@endif
                @if (setting('phone'))<li class="flex gap-3"><i class="fa-solid fa-phone mt-0.5 w-4 text-navy-400"></i><a class="link" href="tel:{{ preg_replace('/\s+/', '', setting('phone')) }}">{{ setting('phone') }}</a></li>@endif
                <li class="flex gap-3"><i class="fa-regular fa-envelope mt-0.5 w-4 text-navy-400"></i><a class="link break-all" href="mailto:{{ setting('email') }}">{{ setting('email') }}</a></li>
            </ul>
        </div>
        <div class="card p-6">
            <h2 class="font-display text-xl font-bold uppercase text-navy-900">Questions fréquentes</h2>
            <dl class="mt-3 divide-y divide-slate-100 text-sm">
                <div class="py-3"><dt class="font-semibold text-slate-800">Comment prendre une licence ?</dt><dd class="mt-1 text-slate-600">La licence se prend dans un club. <a class="link" href="{{ route('clubs.index') }}">Trouvez le club le plus proche</a>.</dd></div>
                <div class="py-3"><dt class="font-semibold text-slate-800">Où voir les résultats officiels ?</dt><dd class="mt-1 text-slate-600">Rubrique <a class="link" href="{{ route('competitions.index') }}">Compétitions</a>, mise à jour automatiquement depuis la FFVolley.</dd></div>
                <div class="py-3"><dt class="font-semibold text-slate-800">Devenir arbitre ?</dt><dd class="mt-1 text-slate-600">Écrivez-nous avec le sujet « Arbitrage » : la ligue organise des formations.</dd></div>
            </dl>
        </div>
    </aside>
</div>
@endsection
