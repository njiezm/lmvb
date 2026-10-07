@extends('layouts.app')

@section('title', 'Calendrier beach-volley')

@section('content')
<x-page-header eyebrow="Beach-volley" title="Calendrier beach" image="images/photos/beach-5-coucher-soleil.jpg"
               :breadcrumbs="['Beach' => route('beach.index'), 'Calendrier' => null]" />

<div class="container-x max-w-4xl py-12">
    @forelse ($events as $month => $monthEvents)
        <h2 class="h-display mb-4 mt-10 text-3xl text-navy-900 first:mt-0">{{ ucfirst($month) }}</h2>
        <div class="card divide-y divide-slate-100">
            @foreach ($monthEvents as $event)
                <a href="{{ route('beach.show', $event) }}" class="flex items-center gap-4 p-4 transition hover:bg-sable-50">
                    <span class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-xl bg-sable-100 leading-none">
                        <span class="font-display text-2xl font-extrabold text-navy-900">{{ $event->start_date->format('d') }}</span>
                        <span class="text-[0.6rem] font-bold uppercase text-bordeaux-600">{{ $event->start_date->translatedFormat('D') }}</span>
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate font-semibold text-slate-900">{{ $event->title }}</span>
                        <span class="text-sm text-slate-500">{{ $event->location }} · {{ $event->type_label }}</span>
                    </span>
                    <span class="chip hidden bg-slate-100 text-slate-600 sm:inline-flex">{{ $event->status_label }}</span>
                </a>
            @endforeach
        </div>
    @empty
        <x-empty-state icon="fa-umbrella-beach" title="Aucun événement au calendrier" />
    @endforelse
</div>
@endsection
