@php
    $links = [
        ['route' => 'home', 'label' => 'Accueil', 'active' => 'home'],
        ['route' => 'competitions.index', 'label' => 'Compétitions', 'active' => 'competitions.*'],
        ['route' => 'games.index', 'label' => 'Matchs', 'active' => 'games.*'],
        ['route' => 'clubs.index', 'label' => 'Clubs', 'active' => 'clubs.*'],
        ['route' => 'news.index', 'label' => 'Actualités', 'active' => 'news.*'],
        ['route' => 'beach.index', 'label' => 'Beach', 'active' => 'beach.*'],
        ['route' => 'selections.index', 'label' => 'Sélections', 'active' => 'selections.*'],
        ['route' => 'gallery.index', 'label' => 'Galerie', 'active' => 'gallery.*'],
        ['route' => 'league', 'label' => 'La Ligue', 'active' => 'league'],
    ];
@endphp
<header class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/85">
    {{-- Bandeau supérieur --}}
    <div class="hidden bg-navy-900 text-xs text-navy-100 md:block">
        <div class="container-x flex h-9 items-center justify-between gap-4">
            <p class="truncate"><i class="fa-solid fa-volleyball mr-1.5 text-lavande-300"></i>Affiliée à la Fédération Française de Volley</p>
            <div class="flex items-center gap-4">
                @if (setting('phone'))
                    <a href="tel:{{ preg_replace('/\s+/', '', setting('phone')) }}" class="hover:text-white"><i class="fa-solid fa-phone mr-1"></i>{{ setting('phone') }}</a>
                @endif
                <a href="mailto:{{ setting('email') }}" class="hover:text-white"><i class="fa-regular fa-envelope mr-1"></i>Contact</a>
                @foreach (['facebook_url' => 'fa-facebook-f', 'instagram_url' => 'fa-instagram', 'youtube_url' => 'fa-youtube'] as $key => $icon)
                    @if (setting($key))
                        <a href="{{ setting($key) }}" target="_blank" rel="noopener" class="hover:text-white" aria-label="{{ ucfirst(str_replace('_url', '', $key)) }}"><i class="fa-brands {{ $icon }}"></i></a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <nav class="container-x flex h-16 items-center justify-between gap-4 lg:h-20" aria-label="Navigation principale">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-3" aria-label="Accueil LMVB">
            <img src="{{ asset('images/brand/logo-lmvb-128.png') }}" alt="Logo LMVB" width="56" height="56" class="h-11 w-11 lg:h-14 lg:w-14">
            <span class="leading-tight">
                <span class="block font-display text-2xl font-extrabold uppercase tracking-tight text-navy-800 lg:text-[1.7rem]">LMVB</span>
                <span class="hidden text-[0.68rem] font-semibold uppercase tracking-wider text-slate-500 sm:block">Ligue Martiniquaise de Volley-Ball</span>
            </span>
        </a>

        <div class="hidden items-center gap-1 xl:flex">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   @class(['rounded-full px-3 py-2 text-sm font-semibold transition',
                           'bg-navy-800 text-white' => request()->routeIs($link['active']),
                           'text-slate-700 hover:bg-navy-50 hover:text-navy-800' => ! request()->routeIs($link['active'])])
                   @if (request()->routeIs($link['active'])) aria-current="page" @endif>{{ $link['label'] }}</a>
            @endforeach
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('contact.create') }}" class="btn-accent hidden sm:inline-flex"><i class="fa-regular fa-paper-plane"></i> Contact</a>
            @auth
                <a href="{{ route('admin.dashboard') }}" class="btn-ghost hidden sm:inline-flex" title="Administration"><i class="fa-solid fa-gauge"></i></a>
            @endauth
            <button type="button" class="inline-flex h-11 w-11 items-center justify-center rounded-full text-navy-800 hover:bg-navy-50 xl:hidden"
                    data-toggle="mobile-menu" data-lock="1" aria-controls="mobile-menu" aria-expanded="false" aria-label="Ouvrir le menu">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
        </div>
    </nav>

    {{-- Menu mobile plein écran --}}
    <div id="mobile-menu" class="hidden xl:hidden">
        <div class="fixed inset-x-0 bottom-0 top-16 overflow-y-auto bg-white px-4 pb-10 pt-4 md:top-[6.25rem] lg:top-[7.25rem]">
            <ul class="divide-y divide-slate-100">
                @foreach ($links as $link)
                    <li>
                        <a href="{{ route($link['route']) }}" @class(['flex items-center justify-between py-3.5 font-display text-2xl font-bold uppercase',
                            'text-bordeaux-600' => request()->routeIs($link['active']), 'text-navy-800' => ! request()->routeIs($link['active'])])>
                            {{ $link['label'] }} <i class="fa-solid fa-chevron-right text-sm text-slate-300"></i>
                        </a>
                    </li>
                @endforeach
            </ul>
            <div class="mt-6 grid gap-3">
                <a href="{{ route('contact.create') }}" class="btn-accent w-full py-3"><i class="fa-regular fa-paper-plane"></i> Contacter la ligue</a>
                <a href="{{ route('games.results') }}" class="btn-light w-full py-3"><i class="fa-solid fa-list-ol"></i> Derniers résultats</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn-ghost w-full"><i class="fa-solid fa-gauge"></i> Administration</a>
                @endauth
            </div>
        </div>
    </div>
</header>
