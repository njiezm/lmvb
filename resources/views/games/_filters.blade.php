{{-- Onglets + filtres communs aux pages Matchs --}}
<div class="sticky top-16 z-30 border-b border-slate-200 bg-white/95 backdrop-blur lg:top-20">
    <div class="container-x flex flex-col gap-3 py-3 lg:flex-row lg:items-center lg:justify-between">
        <nav class="flex gap-1.5" aria-label="Vues">
            @foreach (['games.index' => ['À venir', 'fa-calendar-plus'], 'games.results' => ['Résultats', 'fa-list-ol'], 'games.schedule' => ['Calendrier', 'fa-calendar-days']] as $route => [$label, $icon])
                <a href="{{ route($route, request()->only(['club', 'categorie', 'genre'])) }}"
                   @class(['inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold transition',
                           'bg-navy-800 text-white' => request()->routeIs($route), 'text-slate-600 hover:bg-navy-50' => ! request()->routeIs($route)])>
                    <i class="fa-solid {{ $icon }} hidden sm:inline"></i>{{ $label }}
                </a>
            @endforeach
        </nav>
        <form method="GET" class="grid grid-cols-3 gap-2 lg:flex">
            <select name="club" aria-label="Club" onchange="this.form.submit()" class="input !rounded-full !py-2 lg:w-56">
                <option value="">Tous les clubs</option>
                @foreach ($clubs as $club)
                    <option value="{{ $club->id }}" @selected($selected['club'] == $club->id)>{{ $club->name }}</option>
                @endforeach
            </select>
            <select name="categorie" aria-label="Catégorie" onchange="this.form.submit()" class="input !rounded-full !py-2 lg:w-40">
                <option value="">Catégories</option>
                @foreach ($categories as $key => $label)
                    <option value="{{ $key }}" @selected($selected['categorie'] === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="genre" aria-label="Genre" onchange="this.form.submit()" class="input !rounded-full !py-2 lg:w-36">
                <option value="">M & F</option>
                <option value="F" @selected($selected['genre'] === 'F')>Féminin</option>
                <option value="M" @selected($selected['genre'] === 'M')>Masculin</option>
            </select>
            <noscript><button class="btn-primary">Filtrer</button></noscript>
        </form>
    </div>
</div>
