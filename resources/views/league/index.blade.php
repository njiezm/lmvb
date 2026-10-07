@extends('layouts.app')

@section('title', 'La Ligue')
@section('description', 'La Ligue Martiniquaise de Volley-Ball : présentation, mot de la présidente, comité directeur, documents officiels et partenaires.')

@section('content')
<x-page-header eyebrow="Qui sommes-nous ?" title="La Ligue Martiniquaise de Volley-Ball" image="images/photos/martinique-anses-arlet.jpg"
               subtitle="Organe régional de la Fédération Française de Volley en Martinique : championnats, beach-volley, sélections, formation et arbitrage."
               :breadcrumbs="['La Ligue' => null]" />

<x-president-word full class="bg-white py-20" />

{{-- Missions --}}
<section class="container-x py-16">
    <x-section-heading eyebrow="Nos missions" title="Ce que fait la ligue" />
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @foreach ([
            ['fa-trophy', 'Compétitions', 'Organisation des championnats régionaux seniors et jeunes (M13 à M21), des play-offs et de la Coupe de Martinique.'],
            ['fa-umbrella-beach', 'Beach-volley', 'Championnat et tournois de beach-volley sur les plages de l\'île, du loisir à la haute performance.'],
            ['fa-flag', 'Sélections', 'Préparation et engagement des sélections de Martinique en Coupe Antilles et dans les compétitions CAZOVA / NORCECA.'],
            ['fa-whistle', 'Arbitrage & formation', 'Formation des arbitres, des entraîneurs et des dirigeants, accompagnement des clubs.'],
        ] as [$icon, $title, $text])
            <div class="card p-6">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-navy-800 text-xl text-white"><i class="fa-solid {{ $icon === 'fa-whistle' ? 'fa-flag-checkered' : $icon }}"></i></span>
                <h3 class="mt-4 font-display text-xl font-bold uppercase text-navy-900">{{ $title }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $text }}</p>
            </div>
        @endforeach
    </div>
    <dl class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-4">
        @foreach ([[$clubsCount, 'clubs affiliés'], ['LIMART', 'code FFVolley'], ['1 400 km²', 'de terrain de jeu'], ['972', 'Martinique']] as [$v, $l])
            <div class="rounded-2xl bg-lavande-100/70 p-5 text-center">
                <dd class="font-display text-3xl font-extrabold text-navy-900">{{ $v }}</dd>
                <dt class="text-xs uppercase tracking-wider text-slate-600">{{ $l }}</dt>
            </div>
        @endforeach
    </dl>
</section>

{{-- Comité directeur --}}
@if ($board->isNotEmpty())
    <section class="bg-white py-16">
        <div class="container-x">
            <x-section-heading eyebrow="Gouvernance" title="Le comité directeur" />
            <div class="grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-4">
                @foreach ($board as $member)
                    <div class="text-center">
                        <div class="mx-auto aspect-square w-32 overflow-hidden rounded-full bg-navy-100 ring-4 ring-white shadow sm:w-40">
                            @if ($member->photo)
                                <img src="{{ asset($member->photo) }}" alt="{{ $member->name }}" loading="lazy" class="h-full w-full object-cover">
                            @else
                                <span class="flex h-full items-center justify-center font-display text-4xl font-extrabold text-navy-400">{{ \App\Support\Text::initials($member->name) }}</span>
                            @endif
                        </div>
                        <p class="mt-3 font-semibold text-navy-900">{{ $member->name }}</p>
                        <p class="text-sm text-bordeaux-600">{{ $member->role }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- Documents --}}
<section class="container-x py-16" id="documents">
    <x-section-heading eyebrow="Ressources" title="Documents officiels" />
    @if ($documents->isNotEmpty())
        <div class="grid gap-6 md:grid-cols-2">
            @foreach ($documents as $category => $docs)
                <div class="card p-6">
                    <h3 class="font-display text-xl font-bold uppercase text-navy-900">{{ \App\Models\Document::CATEGORIES[$category] ?? $category }}</h3>
                    <ul class="mt-4 divide-y divide-slate-100">
                        @foreach ($docs as $doc)
                            <li>
                                <a href="{{ $doc->link }}" target="_blank" rel="noopener" class="group flex items-start gap-3 py-3">
                                    <i class="fa-solid {{ $doc->file ? 'fa-file-arrow-down' : 'fa-arrow-up-right-from-square' }} mt-1 text-bordeaux-600"></i>
                                    <span><span class="font-semibold text-slate-800 group-hover:text-navy-700">{{ $doc->title }}</span>
                                        @if ($doc->description)<span class="block text-sm text-slate-500">{{ $doc->description }}</span>@endif</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @else
        <x-empty-state icon="fa-folder-open" title="Documents bientôt disponibles" />
    @endif
</section>

{{-- Contact --}}
<section class="container-x pb-4">
    <div class="grid overflow-hidden rounded-3xl bg-navy-900 text-white lg:grid-cols-2">
        <div class="p-8 sm:p-12">
            <h2 class="h-display text-4xl">Nous contacter</h2>
            <address class="mt-6 space-y-3 not-italic text-navy-100">
                @if (setting('address'))<p><i class="fa-solid fa-location-dot mr-3 w-4 text-lavande-300"></i>{{ setting('address') }}</p>@endif
                @if (setting('phone'))<p><i class="fa-solid fa-phone mr-3 w-4 text-lavande-300"></i>{{ setting('phone') }}</p>@endif
                <p><i class="fa-regular fa-envelope mr-3 w-4 text-lavande-300"></i>{{ setting('email') }}</p>
            </address>
            <a href="{{ route('contact.create') }}" class="btn-accent mt-8">Écrire à la ligue</a>
        </div>
        <iframe title="Carte : Maison des Sports, Fort-de-France" class="h-72 w-full lg:h-full" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                src="https://www.google.com/maps?q={{ urlencode('Maison des Sports, Pointe de la Vierge, Fort-de-France, Martinique') }}&output=embed"></iframe>
    </div>
</section>

@if ($partners->isNotEmpty())
    <section class="container-x py-16">
        <x-section-heading eyebrow="Merci" title="Nos partenaires" />
        <div class="flex flex-wrap items-center gap-8">
            @foreach ($partners as $partner)
                <a href="{{ $partner->url ?: '#' }}" target="_blank" rel="noopener" title="{{ $partner->name }}">
                    @if ($partner->logo)<img src="{{ asset($partner->logo) }}" alt="{{ $partner->name }}" class="h-14 w-auto">@else<span class="font-display text-xl font-bold uppercase text-navy-800">{{ $partner->name }}</span>@endif
                </a>
            @endforeach
        </div>
    </section>
@endif
@endsection
