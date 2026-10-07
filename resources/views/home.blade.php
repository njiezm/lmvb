@extends('layouts.app')

@section('description', setting('site_tagline'))

@section('content')
{{-- HERO --}}
<section class="relative isolate overflow-hidden bg-navy-950 text-white">
    <video class="absolute inset-0 -z-20 h-full w-full object-cover opacity-40" autoplay muted loop playsinline preload="none"
           poster="{{ asset('images/photos/beach-2.jpg') }}" aria-hidden="true">
        <source src="{{ asset('videos/hero-volley.mp4') }}" type="video/mp4">
    </video>
    <div class="absolute inset-0 -z-10 bg-gradient-to-br from-navy-950 via-navy-900/85 to-bordeaux-800/40"></div>
    <img src="{{ asset('images/brand/logo-lmvb-512.png') }}" alt="" class="pointer-events-none absolute -right-20 bottom-[-6rem] -z-10 hidden w-[34rem] opacity-[0.08] lg:block">

    <div class="container-x grid gap-10 py-16 sm:py-20 lg:grid-cols-12 lg:py-28">
        <div class="lg:col-span-7">
            <p class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-lavande-200 ring-1 ring-white/20">
                <span class="h-2 w-2 rounded-full bg-lagon-400"></span>
                Saison {{ $season?->name ?? \App\Models\Season::currentName() }}
            </p>
            <h1 class="h-display mt-5 text-5xl sm:text-6xl lg:text-7xl">
                {{ setting('hero_title') }}
            </h1>
            <p class="mt-5 max-w-xl text-lg text-navy-100">{{ setting('hero_subtitle') }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('competitions.index') }}" class="btn-accent px-6 py-3 text-base"><i class="fa-solid fa-trophy"></i> Classements</a>
                <a href="{{ route('games.results') }}" class="btn px-6 py-3 text-base bg-white text-navy-900 hover:bg-lavande-100"><i class="fa-solid fa-list-ol"></i> Résultats</a>
                <a href="{{ route('clubs.index') }}" class="btn px-6 py-3 text-base text-white ring-1 ring-white/40 hover:bg-white/10"><i class="fa-solid fa-location-dot"></i> Trouver un club</a>
            </div>
            @if ($lastSync)
                <p class="mt-6 text-xs text-navy-300"><i class="fa-solid fa-rotate mr-1"></i>Résultats synchronisés avec la FFVolley {{ $lastSync->diffForHumans() }}</p>
            @endif
        </div>

        {{-- Prochain match à l'affiche --}}
        <div class="lg:col-span-5">
            @php $next = $upcomingGames->first(); @endphp
            <div class="rounded-3xl bg-white/10 p-6 ring-1 ring-white/20 backdrop-blur">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-lavande-200">{{ $next ? 'Prochain match' : 'Dernier résultat' }}</p>
                @php $feature = $next ?? $latestResults->first(); @endphp
                @if ($feature)
                    <p class="mt-1 truncate text-sm text-navy-100">{{ $feature->competition_label }}</p>
                    <div class="mt-6 grid grid-cols-[1fr_auto_1fr] items-center gap-3 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <x-club-badge :club="$feature->homeTeam" :name="$feature->home_name" size="lg" />
                            <span class="line-clamp-2 text-sm font-semibold">{{ $feature->home_name }}</span>
                        </div>
                        <div class="font-display text-4xl font-extrabold">
                            @if ($feature->status === 'finished')
                                {{ $feature->home_score }}<span class="mx-1 text-navy-300">-</span>{{ $feature->away_score }}
                            @else
                                <span class="text-2xl text-lavande-200">VS</span>
                            @endif
                        </div>
                        <div class="flex flex-col items-center gap-2">
                            <x-club-badge :club="$feature->awayTeam" :name="$feature->away_name" size="lg" />
                            <span class="line-clamp-2 text-sm font-semibold">{{ $feature->away_name }}</span>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-sm text-navy-100">
                        @if ($feature->date_time)<span><i class="fa-regular fa-calendar mr-1"></i>{{ $feature->date_time->translatedFormat('l d F · H\hi') }}</span>@endif
                        @if ($feature->venue)<span><i class="fa-solid fa-location-dot mr-1"></i>{{ $feature->venue_label }}</span>@endif
                    </div>
                    <a href="{{ route('games.show', $feature) }}" class="btn mt-6 w-full bg-white text-navy-900 hover:bg-lavande-100">Fiche du match</a>
                @else
                    <p class="mt-4 text-navy-100">Le calendrier de la saison sera bientôt publié.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Chiffres clés --}}
    <div class="border-t border-white/10 bg-navy-950/60">
        <dl class="container-x grid grid-cols-2 divide-white/10 py-5 text-center sm:grid-cols-4 sm:divide-x">
            @foreach ([[$stats['clubs'], 'clubs affiliés'], [$stats['competitions'], 'poules cette saison'], [$stats['games'], 'matchs joués cette saison'], [$stats['previous_games'], 'résultats archivés']] as [$value, $label])
                <div class="px-2 py-2">
                    <dt class="order-2 text-xs uppercase tracking-wider text-navy-300">{{ $label }}</dt>
                    <dd class="font-display text-3xl font-extrabold text-white">{{ number_format($value, 0, ',', ' ') }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>

{{-- Bandeau des derniers résultats --}}
@if ($latestResults->isNotEmpty())
    <section class="border-b border-slate-200 bg-white" aria-label="Derniers résultats">
        <div class="container-x flex items-stretch gap-4 py-4">
            <a href="{{ route('games.results') }}" class="hidden shrink-0 flex-col justify-center rounded-xl bg-bordeaux-600 px-4 text-white sm:flex">
                <span class="font-display text-lg font-extrabold uppercase leading-none">Résultats</span>
                <span class="text-xs opacity-80">Tous <i class="fa-solid fa-arrow-right text-[0.6rem]"></i></span>
            </a>
            <div class="scrollbar-thin flex min-w-0 flex-1 snap-x gap-3 overflow-x-auto pb-1">
                @foreach ($latestResults as $game)
                    <x-game-card :game="$game" compact class="w-64 shrink-0 snap-start !p-3 !shadow-none" />
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- Matchs à venir + classements --}}
<section class="container-x py-16">
    <div class="grid gap-10 lg:grid-cols-12">
        <div class="lg:col-span-7">
            <x-section-heading eyebrow="Calendrier" title="Prochains matchs" :link="route('games.index')" />
            @if ($upcomingGames->isNotEmpty())
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach ($upcomingGames as $game)
                        <x-game-card :game="$game" />
                    @endforeach
                </div>
            @else
                <x-empty-state icon="fa-calendar-days" title="Pas de match programmé">Les prochaines rencontres apparaîtront dès leur publication par la FFVolley.</x-empty-state>
            @endif
        </div>

        <div class="lg:col-span-5">
            <x-section-heading eyebrow="Seniors" title="Classements" :link="route('competitions.index')" />
            @if ($standings->isNotEmpty())
                <div class="card p-4 sm:p-5" data-tabs>
                    <div class="mb-4 flex gap-2" role="tablist">
                        @foreach ($standings as $i => $competition)
                            <button type="button" role="tab" data-tab="std-{{ $competition->id }}" aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                                    class="rounded-full px-4 py-1.5 text-sm font-semibold text-slate-600 ring-1 ring-slate-200 aria-selected:bg-navy-800 aria-selected:text-white aria-selected:ring-navy-800">
                                {{ $competition->gender === 'F' ? 'Féminin' : 'Masculin' }}{{ $standings->where('gender', $competition->gender)->count() > 1 ? ' '.$competition->code : '' }}
                            </button>
                        @endforeach
                    </div>
                    @foreach ($standings as $i => $competition)
                        <div data-panel="std-{{ $competition->id }}" @class(['hidden' => $i > 0])>
                            <x-standings-table :standings="$competition->standings->take(8)" compact />
                            <a href="{{ route('competitions.show', [$competition->season, $competition]) }}" class="link mt-4 inline-block text-sm">Classement complet et calendrier →</a>
                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state icon="fa-trophy" title="Classements à venir" />
            @endif
        </div>
    </div>
</section>

{{-- Actualités --}}
<section class="bg-white py-16">
    <div class="container-x">
        <x-section-heading eyebrow="La vie de la ligue" title="Actualités" :link="route('news.index')" />
        @php $lead = $featuredNews->first() ?? $latestNews->first(); $others = $latestNews->reject(fn ($n) => $lead && $n->id === $lead->id)->take(3); @endphp
        @if ($lead)
            <div class="grid gap-6 lg:grid-cols-12">
                <x-news-card :news="$lead" large class="lg:col-span-6" />
                <div class="grid gap-6 sm:grid-cols-2 lg:col-span-6 lg:grid-cols-1">
                    @foreach ($others as $news)
                        <article class="group relative flex gap-4 rounded-2xl p-2 transition hover:bg-slate-50">
                            <div class="aspect-[4/3] w-32 shrink-0 overflow-hidden rounded-xl bg-navy-100 sm:w-40">
                                @if ($news->image_url)<img src="{{ $news->image_url }}" alt="" loading="lazy" class="h-full w-full object-cover">@endif
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-semibold uppercase tracking-wide" style="color: {{ $news->category?->color }}">{{ $news->category?->name }}</p>
                                <h3 class="mt-1 font-display text-lg font-bold uppercase leading-tight text-navy-900 group-hover:text-bordeaux-600">
                                    <a href="{{ route('news.show', $news->slug) }}" class="after:absolute after:inset-0">{{ $news->title }}</a>
                                </h3>
                                <p class="mt-1 text-xs text-slate-500">{{ $news->published_at?->translatedFormat('d F Y') }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @else
            <x-empty-state icon="fa-newspaper" title="Aucune actualité publiée" />
        @endif
    </div>
</section>

{{-- Le mot de la Présidente --}}
<x-president-word class="py-20" />

{{-- La ligue --}}
<section class="relative overflow-hidden bg-gradient-to-br from-lavande-100 via-white to-sable-50 py-16">
    <div class="container-x grid items-center gap-10 lg:grid-cols-2">
        <div class="relative">
            <img src="{{ asset('images/photos/indoor-celebration.jpg') }}" alt="Équipe de volley célébrant un point" loading="lazy" class="aspect-[4/3] w-full rounded-3xl object-cover shadow-xl">
            <div class="absolute -bottom-6 -right-2 hidden rounded-2xl bg-white p-4 shadow-lg sm:block">
                <img src="{{ asset('images/brand/logo-lmvb-128.png') }}" alt="" class="h-20 w-20">
            </div>
        </div>
        <div>
            <p class="eyebrow">La Ligue</p>
            <h2 class="h-display mt-1 text-4xl text-navy-900 sm:text-5xl">Le volley-ball martiniquais, ensemble</h2>
            <p class="mt-4 text-slate-600">
                La Ligue Martiniquaise de Volley-Ball organise les championnats régionaux seniors et jeunes, la Coupe de Martinique,
                le beach-volley et les sélections régionales. Elle fédère {{ $stats['clubs'] }} clubs, de Schœlcher aux Anses-d'Arlet et de Sainte-Luce à La Trinité.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('league') }}" class="btn-primary">Découvrir la ligue</a>
                <a href="{{ setting('license_url') }}" target="_blank" rel="noopener" class="btn-light">Prendre sa licence</a>
            </div>
        </div>
    </div>
</section>

{{-- Clubs --}}
@if ($clubs->isNotEmpty())
    <section class="container-x py-16">
        <x-section-heading eyebrow="{{ $clubs->count() }} clubs" title="Les clubs de la ligue" :link="route('clubs.index')" link-label="Tous les clubs" />
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            @foreach ($clubs->take(12) as $club)
                <a href="{{ route('clubs.show', $club) }}" class="group flex flex-col items-center gap-3 rounded-2xl bg-white p-4 text-center ring-1 ring-slate-200/70 transition hover:-translate-y-0.5 hover:shadow-md">
                    <x-club-badge :club="$club" size="lg" />
                    <span class="line-clamp-2 text-sm font-semibold text-slate-800 group-hover:text-navy-700">{{ $club->name }}</span>
                    @if ($club->city)<span class="-mt-2 text-xs text-slate-500">{{ $club->city }}</span>@endif
                </a>
            @endforeach
        </div>
    </section>
@endif

{{-- Vidéos --}}
@if ($videos->isNotEmpty())
    <section class="bg-navy-950 py-16 text-white">
        <div class="container-x">
            <div class="mb-6 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-lavande-300">LMVB TV</p>
                    <h2 class="h-display mt-1 text-3xl sm:text-4xl">Les matchs en vidéo</h2>
                </div>
                @if (setting('youtube_url'))
                    <a href="{{ setting('youtube_url') }}" target="_blank" rel="noopener" class="btn bg-red-600 text-white hover:bg-red-700"><i class="fa-brands fa-youtube"></i> Chaîne YouTube</a>
                @endif
            </div>
            <div class="grid gap-6 md:grid-cols-3">
                @foreach ($videos as $video)
                    <x-video-embed :item="$video" />
                @endforeach
            </div>
        </div>
    </section>
@endif

{{-- Beach --}}
<section class="relative isolate overflow-hidden">
    <img src="{{ asset('images/photos/beach-6-caraibes.jpg') }}" alt="" loading="lazy" class="absolute inset-0 -z-10 h-full w-full object-cover">
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-navy-950/90 via-navy-900/70 to-transparent"></div>
    <div class="container-x py-20 text-white">
        <div class="max-w-xl">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-sable-200">Beach-volley</p>
            <h2 class="h-display mt-2 text-4xl sm:text-5xl">Du sable, du soleil et du jeu</h2>
            <p class="mt-4 text-navy-100">Championnat de beach-volley, tournois d'été et paires martiniquaises sur la scène caribéenne.</p>
            @if ($nextBeach)
                <div class="mt-6 rounded-2xl bg-white/10 p-4 ring-1 ring-white/20 backdrop-blur">
                    <p class="text-xs uppercase tracking-wider text-sable-200">Prochain rendez-vous</p>
                    <p class="mt-1 font-display text-2xl font-bold uppercase">{{ $nextBeach->title }}</p>
                    <p class="text-sm text-navy-100">{{ $nextBeach->start_date->translatedFormat('d F Y') }} · {{ $nextBeach->location }}</p>
                </div>
            @endif
            <a href="{{ route('beach.index') }}" class="btn mt-6 bg-sable-100 text-navy-900 hover:bg-white">Le beach-volley en Martinique</a>
        </div>
    </div>
</section>

{{-- Partenaires --}}
@if ($partners->isNotEmpty())
    <section class="container-x py-14">
        <p class="eyebrow text-center">Ils soutiennent le volley martiniquais</p>
        <div class="mt-6 flex flex-wrap items-center justify-center gap-x-10 gap-y-6">
            @foreach ($partners as $partner)
                <a href="{{ $partner->url ?: '#' }}" target="_blank" rel="noopener" class="opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0" title="{{ $partner->name }}">
                    @if ($partner->logo)<img src="{{ asset($partner->logo) }}" alt="{{ $partner->name }}" class="h-12 w-auto" loading="lazy">@else<span class="font-display text-xl font-bold uppercase text-navy-800">{{ $partner->name }}</span>@endif
                </a>
            @endforeach
        </div>
    </section>
@endif
@endsection
