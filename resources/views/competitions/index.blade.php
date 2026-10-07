@extends('layouts.app')

@section('title', 'Compétitions '.($season?->name ?? ''))
@section('description', 'Championnats régionaux de volley-ball en Martinique : classements, calendriers et résultats de toutes les poules seniors et jeunes.')

@section('content')
<x-page-header eyebrow="Saison {{ $season?->name }}" title="Compétitions" image="images/photos/indoor-bloc.jpg"
               subtitle="Classements, calendriers et résultats de toutes les poules de la ligue, mis à jour automatiquement depuis la FFVolley."
               :breadcrumbs="['Compétitions' => null]">
    @if ($seasons->count() > 1)
        <form method="GET" class="mt-6 flex items-center gap-2">
            <label for="saison" class="text-sm text-navy-100">Saison</label>
            <select id="saison" name="saison" onchange="this.form.submit()" class="rounded-full border-0 bg-white/10 py-2 pl-4 pr-10 text-sm text-white ring-1 ring-white/30 focus:ring-lavande-300">
                @foreach ($seasons as $s)
                    <option value="{{ $s->slug }}" class="text-slate-900" @selected($season && $s->id === $season->id)>{{ $s->name }}</option>
                @endforeach
            </select>
            <noscript><button class="btn-light">OK</button></noscript>
        </form>
    @endif
</x-page-header>

<div class="container-x py-12">
    @forelse ($groups as $category => $competitions)
        <section class="mb-12" id="{{ $category }}">
            <h2 class="h-display mb-5 flex items-center gap-3 text-3xl text-navy-900">
                <span class="h-8 w-1.5 rounded-full bg-bordeaux-600"></span>{{ \App\Models\Competition::CATEGORIES[$category] ?? $category }}
            </h2>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($competitions as $competition)
                    <a href="{{ route('competitions.show', [$season, $competition]) }}" class="group card flex flex-col p-5 transition hover:-translate-y-0.5 hover:shadow-md hover:ring-navy-200">
                        <div class="flex items-start justify-between gap-3">
                            <span @class(['chip', 'bg-pink-100 text-pink-800' => $competition->gender === 'F', 'bg-sky-100 text-sky-800' => $competition->gender === 'M', 'bg-slate-100 text-slate-700' => ! $competition->gender])>
                                {{ $competition->gender_label ?: 'Mixte' }}
                            </span>
                            <span class="chip bg-navy-50 text-navy-700">{{ $competition->phase_label }}</span>
                        </div>
                        <h3 class="mt-3 font-display text-xl font-bold uppercase leading-tight text-navy-900 group-hover:text-bordeaux-600">{{ $competition->display_name }}</h3>
                        <div class="mt-auto pt-4">
                            @php $progress = $competition->games_count ? round($competition->finished_count / $competition->games_count * 100) : 0; @endphp
                            <div class="flex justify-between text-xs text-slate-500"><span>{{ $competition->finished_count }} / {{ $competition->games_count }} matchs joués</span><span>{{ $progress }} %</span></div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-gradient-to-r from-navy-500 to-bordeaux-600" style="width: {{ $progress }}%"></div></div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @empty
        <x-empty-state icon="fa-trophy" title="Aucune compétition publiée">Les poules de la saison apparaîtront ici dès leur publication sur la plateforme FFVolley.</x-empty-state>
    @endforelse
</div>
@endsection
