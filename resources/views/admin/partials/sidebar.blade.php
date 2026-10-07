@php
    $user = auth()->user();
    $sections = [
        'Pilotage' => [
            ['admin.dashboard', 'admin.dashboard', 'fa-gauge-high', 'Tableau de bord', false],
            ['admin.competitions.index', 'admin.competitions.*', 'fa-trophy', 'Compétitions & synchro', true],
            ['admin.games.index', 'admin.games.*', 'fa-volleyball', $user->isSuperAdmin() ? 'Matchs' : 'Matchs du club', false],
        ],
        'Contenus' => [
            ['admin.news.index', 'admin.news.*', 'fa-newspaper', 'Actualités', false],
            ['admin.gallery.index', 'admin.gallery.*', 'fa-photo-film', 'Galerie', false],
            ['admin.clubs.index', 'admin.clubs.*', 'fa-shield-halved', $user->isSuperAdmin() ? 'Clubs' : 'Fiche du club', false],
            ['admin.contacts.index', 'admin.contacts.*', 'fa-envelope', 'Messages', false],
        ],
        'Ligue' => [
            ['admin.teams.index', ['admin.teams.*', 'admin.players.*'], 'fa-flag', 'Sélections', true],
            ['admin.beach.index', 'admin.beach.*', 'fa-umbrella-beach', 'Beach-volley', true],
            ['admin.board.index', 'admin.board.*', 'fa-user-tie', 'Comité directeur', true],
            ['admin.documents.index', 'admin.documents.*', 'fa-folder-open', 'Documents', true],
            ['admin.partners.index', 'admin.partners.*', 'fa-handshake', 'Partenaires', true],
            ['admin.newsletter.index', 'admin.newsletter.*', 'fa-paper-plane', 'Newsletter', true],
        ],
        'Système' => [
            ['admin.users.index', 'admin.users.*', 'fa-users-gear', 'Utilisateurs', true],
            ['admin.settings.edit', 'admin.settings.*', 'fa-sliders', 'Réglages du site', true],
        ],
    ];
@endphp
<aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-navy-950 text-navy-100 transition-transform duration-200 lg:translate-x-0" aria-label="Menu d'administration">
    <div class="flex h-16 shrink-0 items-center justify-between border-b border-white/10 px-5">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
            <span class="rounded-full bg-white p-1"><img src="{{ asset('images/brand/logo-lmvb-128.png') }}" alt="" class="h-8 w-8"></span>
            <span class="leading-tight"><span class="block font-display text-xl font-extrabold uppercase text-white">LMVB</span><span class="text-[0.65rem] uppercase tracking-wider text-navy-300">Administration</span></span>
        </a>
        <button type="button" class="rounded-lg p-2 text-navy-200 hover:bg-white/10 lg:hidden" onclick="toggleSidebar(false)" aria-label="Fermer le menu"><i class="fa-solid fa-xmark text-lg"></i></button>
    </div>

    <nav class="sidebar-scroll flex-1 overflow-y-auto overscroll-contain px-3 py-4">
        @foreach ($sections as $title => $items)
            @php $visible = collect($items)->filter(fn ($i) => ! $i[4] || $user->isSuperAdmin()); @endphp
            @if ($visible->isNotEmpty())
                <p class="mb-1 mt-5 px-3 text-[0.65rem] font-bold uppercase tracking-[0.18em] text-navy-400 first:mt-0">{{ $title }}</p>
                @foreach ($visible as [$route, $pattern, $icon, $label])
                    @php $active = request()->routeIs(...(array) $pattern); @endphp
                    <a href="{{ route($route) }}" @class(['group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition',
                        'bg-white text-navy-900 shadow' => $active, 'hover:bg-white/10 hover:text-white' => ! $active])>
                        <i class="fa-solid {{ $icon }} w-5 text-center {{ $active ? 'text-bordeaux-600' : 'text-navy-300 group-hover:text-white' }}"></i>{{ $label }}
                    </a>
                @endforeach
            @endif
        @endforeach
    </nav>

    @if ($user->isSuperAdmin())
        @php $last = \App\Models\SyncLog::lastSuccess(); @endphp
        <form action="{{ route('admin.sync') }}" method="POST" class="border-t border-white/10 p-4">
            @csrf
            <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-bordeaux-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-bordeaux-700" onclick="this.disabled=true;this.innerHTML='<i class=\'fa-solid fa-rotate fa-spin\'></i> Synchronisation…';this.form.submit()">
                <i class="fa-solid fa-rotate"></i> Synchroniser la FFVolley
            </button>
            <p class="mt-2 text-center text-[0.7rem] text-navy-300">{{ $last ? 'Dernière mise à jour '.$last->finished_at->diffForHumans() : 'Jamais synchronisé' }}</p>
        </form>
    @endif
</aside>
