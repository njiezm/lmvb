@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('content')
<x-admin.page :title="'Bonjour '.\Illuminate\Support\Str::before(auth()->user()->name, ' ').' 👋'"
              :subtitle="$club ? 'Espace de gestion : '.$club->name : 'Saison '.($season?->name ?? '').' · vue d\'ensemble du site'">
    <x-slot:actions>
        <a href="{{ route('admin.news.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Actualité</a>
        <a href="{{ route('admin.gallery.create') }}" class="btn-light"><i class="fa-solid fa-photo-film"></i> Photos / vidéo</a>
    </x-slot:actions>
</x-admin.page>

<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    @foreach ([
        ['Actualités', $stats['news_count'], $stats['published_news_count'].' publiée(s)', 'fa-newspaper', 'from-navy-600 to-navy-800', route('admin.news.index')],
        ['Matchs à venir (30 j)', $stats['upcoming_games_count'], $stats['finished_games_count'].' résultats en base', 'fa-calendar-days', 'from-sky-600 to-navy-700', route('admin.games.index')],
        ['Messages non lus', $stats['unread_contacts_count'], 'à traiter', 'fa-envelope', 'from-bordeaux-500 to-bordeaux-700', route('admin.contacts.index', ['status' => 0])],
        ['Clubs affiliés', $stats['clubs_count'], 'actifs', 'fa-shield-halved', 'from-emerald-600 to-teal-700', route('admin.clubs.index')],
    ] as [$label, $value, $hint, $icon, $gradient, $url])
        <a href="{{ $url }}" class="rounded-2xl bg-gradient-to-br {{ $gradient }} p-5 text-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
            <div class="flex items-start justify-between">
                <span class="font-display text-4xl font-extrabold">{{ $value }}</span>
                <i class="fa-solid {{ $icon }} text-2xl opacity-60"></i>
            </div>
            <p class="mt-2 text-sm font-semibold">{{ $label }}</p>
            <p class="text-xs opacity-75">{{ $hint }}</p>
        </a>
    @endforeach
</div>

@if (auth()->user()->isSuperAdmin())
    <div class="card mt-6 p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <span @class(['flex h-12 w-12 items-center justify-center rounded-2xl text-xl', 'bg-emerald-100 text-emerald-700' => $lastSync, 'bg-amber-100 text-amber-700' => ! $lastSync])><i class="fa-solid fa-rotate"></i></span>
                <div>
                    <p class="font-semibold text-slate-900">Synchronisation automatique FFVolley</p>
                    <p class="text-sm text-slate-500">
                        {{ $lastSync ? 'Dernière réussite '.$lastSync->finished_at->diffForHumans().' ('.$lastSync->finished_at->format('d/m à H:i').')' : 'Aucune synchronisation réussie' }}
                        · cron toutes les 30 min + déclenchement automatique à la visite
                    </p>
                </div>
            </div>
            <form action="{{ route('admin.sync') }}" method="POST" class="flex gap-2">
                @csrf
                <button class="btn-accent" onclick="this.disabled=true;this.innerHTML='<i class=\'fa-solid fa-rotate fa-spin\'></i> En cours…';this.form.submit()"><i class="fa-solid fa-rotate"></i> Synchroniser maintenant</button>
            </form>
        </div>
        @if ($syncLogs->isNotEmpty())
            <details class="mt-4">
                <summary class="cursor-pointer text-sm font-medium text-navy-600">Historique des synchronisations</summary>
                <div class="scrollbar-thin mt-3 overflow-x-auto">
                    <table class="w-full min-w-[36rem] text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500"><tr><th class="py-2">Date</th><th>Origine</th><th>Saison</th><th>Statut</th><th>Poules</th><th>Créés</th><th>MAJ</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($syncLogs as $log)
                                <tr title="{{ $log->message }}">
                                    <td class="py-2">{{ $log->started_at?->format('d/m H:i') }}</td>
                                    <td>{{ ['cron' => 'Cron', 'visit' => 'Visite', 'manual' => 'Manuel'][$log->trigger] ?? $log->trigger }}</td>
                                    <td>{{ $log->season }}</td>
                                    <td><x-admin.status :value="$log->status" /></td>
                                    <td>{{ $log->competitions_count }}</td><td>{{ $log->games_created }}</td><td>{{ $log->games_updated }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        @endif
    </div>
@endif

<div class="mt-6 grid gap-6 lg:grid-cols-2">
    <section class="card p-5">
        <div class="mb-4 flex items-center justify-between"><h2 class="font-display text-xl font-bold uppercase text-navy-900">Prochains matchs</h2><a href="{{ route('games.index') }}" target="_blank" class="link text-sm">Voir sur le site</a></div>
        <ul class="divide-y divide-slate-100">
            @forelse ($upcomingGames as $game)
                <li class="flex items-center gap-3 py-2.5 text-sm">
                    <span class="w-20 shrink-0 text-xs text-slate-500">{{ $game->date_time->translatedFormat('D d/m') }}<br>{{ $game->date_time->format('H\hi') }}</span>
                    <span class="min-w-0 flex-1 truncate font-medium">{{ $game->home_name }} <span class="text-slate-400">vs</span> {{ $game->away_name }}</span>
                    <span class="hidden shrink-0 text-xs text-slate-400 sm:block">{{ $game->competitionModel?->code }}</span>
                </li>
            @empty
                <li class="py-6 text-center text-sm text-slate-500">Aucun match programmé.</li>
            @endforelse
        </ul>
    </section>

    <section class="card p-5">
        <h2 class="mb-4 font-display text-xl font-bold uppercase text-navy-900">Derniers résultats</h2>
        <ul class="divide-y divide-slate-100">
            @forelse ($recentResults as $game)
                <li class="flex items-center gap-3 py-2.5 text-sm">
                    <span class="w-14 shrink-0 text-xs text-slate-500">{{ $game->date_time->format('d/m') }}</span>
                    <span class="min-w-0 flex-1 truncate">{{ $game->home_name }} <strong class="tabular mx-1 text-navy-800">{{ $game->home_score }}-{{ $game->away_score }}</strong> {{ $game->away_name }}</span>
                    @if ($game->locked)<i class="fa-solid fa-lock text-xs text-amber-500" title="Corrigé manuellement"></i>@endif
                </li>
            @empty
                <li class="py-6 text-center text-sm text-slate-500">Aucun résultat.</li>
            @endforelse
        </ul>
    </section>

    <section class="card p-5">
        <div class="mb-4 flex items-center justify-between"><h2 class="font-display text-xl font-bold uppercase text-navy-900">Dernières actualités</h2><a href="{{ route('admin.news.index') }}" class="link text-sm">Gérer</a></div>
        <ul class="divide-y divide-slate-100">
            @forelse ($latestNews as $news)
                <li class="flex items-center gap-3 py-2.5">
                    <div class="h-11 w-14 shrink-0 overflow-hidden rounded-lg bg-slate-100">@if ($news->image_url)<img src="{{ $news->image_url }}" alt="" class="h-full w-full object-cover">@endif</div>
                    <div class="min-w-0 flex-1"><a href="{{ route('admin.news.edit', $news) }}" class="block truncate text-sm font-medium hover:text-navy-700">{{ $news->title }}</a><p class="text-xs text-slate-500">{{ $news->created_at->format('d/m/Y') }}</p></div>
                    <x-admin.status :value="$news->status" :label="$news->status_label" />
                </li>
            @empty
                <li class="py-6 text-center text-sm text-slate-500">Aucune actualité. <a class="link" href="{{ route('admin.news.create') }}">En créer une</a></li>
            @endforelse
        </ul>
    </section>

    <section class="card p-5">
        <div class="mb-4 flex items-center justify-between"><h2 class="font-display text-xl font-bold uppercase text-navy-900">Messages non lus</h2><a href="{{ route('admin.contacts.index') }}" class="link text-sm">Tous les messages</a></div>
        <ul class="divide-y divide-slate-100">
            @forelse ($latestContacts as $contact)
                <li class="py-2.5">
                    <a href="{{ route('admin.contacts.show', $contact) }}" class="flex items-center gap-3 hover:text-navy-700">
                        <span class="h-2 w-2 shrink-0 rounded-full bg-bordeaux-600"></span>
                        <span class="min-w-0 flex-1"><span class="block truncate text-sm font-medium">{{ $contact->name }} · {{ $contact->subject }}</span><span class="text-xs text-slate-500">{{ $contact->created_at->diffForHumans() }}</span></span>
                    </a>
                </li>
            @empty
                <li class="py-6 text-center text-sm text-slate-500"><i class="fa-regular fa-face-smile mr-1"></i>Boîte de réception à jour.</li>
            @endforelse
        </ul>
    </section>
</div>
@endsection
