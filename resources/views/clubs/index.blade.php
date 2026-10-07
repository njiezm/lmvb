@extends('layouts.app')

@section('title', 'Les clubs')
@section('description', 'Trouvez un club de volley-ball en Martinique : coordonnées, salles, résultats et classements des '.$clubs->count().' clubs de la ligue.')

@section('content')
<x-page-header eyebrow="{{ $clubs->count() }} clubs affiliés" title="Trouver un club" image="images/photos/jeunes-1.jpg"
               subtitle="Débutant, compétiteur, jeune ou adulte : il y a un club de volley près de chez vous en Martinique."
               :breadcrumbs="['Clubs' => null]" />

<div class="container-x py-10">
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <label class="relative block w-full sm:max-w-sm">
            <span class="sr-only">Rechercher un club ou une commune</span>
            <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="search" id="club-search" placeholder="Club ou commune…" class="input !rounded-full !py-3 pl-11" autocomplete="off">
        </label>
        <p class="text-sm text-slate-500"><span id="club-count">{{ $clubs->count() }}</span> club(s)</p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3" id="clubs-grid">
        @foreach ($clubs as $club)
            <a href="{{ route('clubs.show', $club) }}" data-search="{{ \Illuminate\Support\Str::lower(\Illuminate\Support\Str::ascii($club->name.' '.$club->short_name.' '.$club->city)) }}"
               class="club-card group card relative flex flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-lg">
                <div class="h-2" style="background: {{ $club->display_color }}"></div>
                <div class="flex items-center gap-4 p-5">
                    <x-club-badge :club="$club" size="lg" />
                    <div class="min-w-0">
                        <h2 class="font-display text-xl font-bold uppercase leading-tight text-navy-900 group-hover:text-bordeaux-600">{{ $club->name }}</h2>
                        @if ($club->city)<p class="mt-0.5 text-sm text-slate-500"><i class="fa-solid fa-location-dot mr-1 text-xs"></i>{{ $club->city }}</p>@endif
                    </div>
                </div>
                <div class="mt-auto flex items-center justify-between border-t border-slate-100 px-5 py-3 text-xs text-slate-500">
                    <span>@if ($club->founded_year)Fondé en {{ $club->founded_year }}@elseif ($club->ffvb_number)N° FFVolley {{ $club->ffvb_number }}@endif</span>
                    <span class="font-semibold text-navy-600">Voir le club <i class="fa-solid fa-arrow-right ml-0.5"></i></span>
                </div>
            </a>
        @endforeach
    </div>
    <x-empty-state id="clubs-empty" class="mt-6 hidden" icon="fa-magnifying-glass" title="Aucun club trouvé">Essayez un autre nom ou une autre commune.</x-empty-state>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        var input = document.getElementById('club-search');
        var cards = document.querySelectorAll('.club-card');
        var normalize = function (s) { return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, ''); };
        input.addEventListener('input', function () {
            var q = normalize(input.value.trim()), visible = 0;
            cards.forEach(function (card) {
                var show = !q || card.dataset.search.indexOf(q) !== -1;
                card.classList.toggle('hidden', !show);
                if (show) visible++;
            });
            document.getElementById('club-count').textContent = visible;
            document.getElementById('clubs-empty').classList.toggle('hidden', visible > 0);
        });
    })();
</script>
@endpush
