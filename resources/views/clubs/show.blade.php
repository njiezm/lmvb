@extends('layouts.app')

@section('title', $club->name)
@section('description', \Illuminate\Support\Str::limit($club->description ?: $club->name.' : club de volley-ball'.($club->city ? ' à '.$club->city : '').' affilié à la Ligue Martiniquaise de Volley-Ball. Résultats, calendrier et classements.', 160))
@if ($club->logo_url) @section('og_image', $club->logo_url) @endif

@section('content')
<section class="relative isolate overflow-hidden bg-navy-950 text-white">
    <div class="absolute inset-0 -z-10 opacity-90" style="background: radial-gradient(circle at 15% 20%, {{ $club->display_color }}aa, transparent 55%), linear-gradient(135deg, #10122f, #242868)"></div>
    <div class="container-x py-12 sm:py-16">
        <nav class="mb-6 text-xs text-navy-200" aria-label="Fil d'Ariane"><a href="{{ route('home') }}" class="hover:text-white">Accueil</a> / <a href="{{ route('clubs.index') }}" class="hover:text-white">Clubs</a></nav>
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
            <x-club-badge :club="$club" size="xl" class="ring-4 ring-white/30" />
            <div>
                @if ($club->city)<p class="text-xs font-bold uppercase tracking-[0.2em] text-lavande-300">{{ $club->city }}</p>@endif
                <h1 class="h-display mt-1 text-4xl sm:text-6xl">{{ $club->name }}</h1>
                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    @if ($club->founded_year)<span class="chip bg-white/10 py-1 ring-1 ring-white/20">Fondé en {{ $club->founded_year }}</span>@endif
                    @if ($club->ffvb_number)<span class="chip bg-white/10 py-1 ring-1 ring-white/20">N° FFVolley {{ $club->ffvb_number }}</span>@endif
                    @if ($stats['played'])<span class="chip bg-white/10 py-1 ring-1 ring-white/20">{{ $stats['wins'] }} victoires / {{ $stats['played'] }} matchs archivés</span>@endif
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container-x grid gap-10 py-12 lg:grid-cols-12">
    <div class="space-y-12 lg:col-span-8">
        @if ($club->description)
            <section>
                <h2 class="h-display mb-3 text-2xl text-navy-900">Le club</h2>
                <div class="prose prose-slate max-w-none">{!! nl2br(e($club->description)) !!}</div>
            </section>
        @endif

        <section>
            <h2 class="h-display mb-4 text-2xl text-navy-900">Engagements {{ $season?->name }}</h2>
            @if ($standings->isNotEmpty())
                <div class="grid gap-3 sm:grid-cols-2">
                    @foreach ($standings as $standing)
                        <a href="{{ route('competitions.show', [$standing->competition->season, $standing->competition]) }}" class="card flex items-center gap-4 p-4 transition hover:shadow-md">
                            <span class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-navy-800 leading-none text-white">
                                <span class="font-display text-2xl font-extrabold">{{ $standing->position }}<sup class="text-xs">{{ $standing->position === 1 ? 'er' : 'e' }}</sup></span>
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate font-semibold text-slate-900">{{ $standing->competition->display_name }}</span>
                                <span class="text-sm text-slate-500">{{ $standing->points }} pts · {{ $standing->won }} V / {{ $standing->lost }} D</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-slate-500">Aucun engagement en championnat publié pour cette saison.</p>
            @endif
        </section>

        <section>
            <h2 class="h-display mb-4 text-2xl text-navy-900">Prochains matchs</h2>
            @if ($upcoming->isNotEmpty())
                <div class="grid gap-4 sm:grid-cols-2">@foreach ($upcoming as $game)<x-game-card :game="$game" />@endforeach</div>
            @else
                <p class="text-slate-500">Pas de match programmé pour le moment.</p>
            @endif
        </section>

        <section>
            <div class="mb-4 flex items-end justify-between">
                <h2 class="h-display text-2xl text-navy-900">Derniers résultats</h2>
                <a href="{{ route('games.results', ['club' => $club->id]) }}" class="link text-sm">Tous les résultats</a>
            </div>
            @if ($results->isNotEmpty())
                <div class="grid gap-4 sm:grid-cols-2">@foreach ($results as $game)<x-game-card :game="$game" />@endforeach</div>
            @else
                <p class="text-slate-500">Aucun résultat enregistré.</p>
            @endif
        </section>

        @if ($news->isNotEmpty())
            <section>
                <h2 class="h-display mb-4 text-2xl text-navy-900">Actualités du club</h2>
                <div class="grid gap-5 sm:grid-cols-3">@foreach ($news as $item)<x-news-card :news="$item" />@endforeach</div>
            </section>
        @endif

        @if ($photos->isNotEmpty())
            <section>
                <h2 class="h-display mb-4 text-2xl text-navy-900">En images</h2>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ($photos as $photo)
                        <a href="{{ route('gallery.show', $photo) }}" class="aspect-square overflow-hidden rounded-xl bg-navy-100"><img src="{{ $photo->image_url }}" alt="{{ $photo->title }}" loading="lazy" class="h-full w-full object-cover transition hover:scale-105"></a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <aside class="lg:col-span-4">
        <div class="card sticky top-28 space-y-5 p-6">
            <h2 class="font-display text-xl font-bold uppercase text-navy-900">Contact & accès</h2>
            <ul class="space-y-3 text-sm">
                @if ($club->venue)<li class="flex gap-3"><i class="fa-solid fa-building mt-0.5 w-4 text-navy-400"></i><span>{{ $club->venue }}</span></li>@endif
                @if ($club->address)<li class="flex gap-3"><i class="fa-solid fa-location-dot mt-0.5 w-4 text-navy-400"></i><span>{{ $club->address }}</span></li>@endif
                @if ($club->phone)<li class="flex gap-3"><i class="fa-solid fa-phone mt-0.5 w-4 text-navy-400"></i><a class="link" href="tel:{{ preg_replace('/\s+/', '', $club->phone) }}">{{ $club->phone }}</a></li>@endif
                @if ($club->email)<li class="flex gap-3"><i class="fa-regular fa-envelope mt-0.5 w-4 text-navy-400"></i><a class="link break-all" href="mailto:{{ $club->email }}">{{ $club->email }}</a></li>@endif
                @if ($club->website)<li class="flex gap-3"><i class="fa-solid fa-globe mt-0.5 w-4 text-navy-400"></i><a class="link break-all" href="{{ $club->website }}" target="_blank" rel="noopener">{{ preg_replace('~^https?://(www\.)?~', '', rtrim($club->website, '/')) }}</a></li>@endif
            </ul>
            @if ($club->facebook || $club->instagram)
                <div class="flex gap-2">
                    @if ($club->facebook)<a href="{{ $club->facebook }}" target="_blank" rel="noopener" class="btn-light flex-1"><i class="fa-brands fa-facebook-f"></i> Facebook</a>@endif
                    @if ($club->instagram)<a href="{{ $club->instagram }}" target="_blank" rel="noopener" class="btn-light flex-1"><i class="fa-brands fa-instagram"></i> Instagram</a>@endif
                </div>
            @endif
            @if ($club->address || $club->venue || $club->city)
                <a target="_blank" rel="noopener" class="btn-primary w-full" href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($club->address ?: $club->venue.' '.$club->city).' Martinique') }}"><i class="fa-solid fa-route"></i> Itinéraire</a>
            @endif
            <a href="{{ route('contact.create', ['club' => $club->id]) }}" class="btn-accent w-full"><i class="fa-regular fa-paper-plane"></i> Contacter le club</a>
        </div>
    </aside>
</div>
@endsection
