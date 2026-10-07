@extends('layouts.admin')

@section('title', 'Actualités')

@section('content')
<x-admin.page title="Actualités" :subtitle="$news->total().' article(s)'">
    <x-slot:actions><a href="{{ route('admin.news.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i> Nouvelle actualité</a></x-slot:actions>
</x-admin.page>

<form method="GET" class="card mb-4 grid gap-3 p-4 sm:grid-cols-[1fr_12rem_auto]">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Rechercher un titre…" class="input">
    <select name="status" class="input">
        <option value="">Tous les statuts</option>
        @foreach (\App\Models\News::STATUSES as $key => $label)<option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>@endforeach
    </select>
    <button class="btn-light"><i class="fa-solid fa-filter"></i> Filtrer</button>
</form>

<div class="card overflow-hidden">
    <div class="scrollbar-thin overflow-x-auto">
        <table class="w-full min-w-[44rem] text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                <tr><th class="px-4 py-3">Article</th><th class="px-4 py-3">Catégorie</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3">Publication</th><th class="px-4 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($news as $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="h-12 w-16 shrink-0 overflow-hidden rounded-lg bg-slate-100">@if ($item->image_url)<img src="{{ $item->image_url }}" alt="" class="h-full w-full object-cover">@endif</div>
                                <div class="min-w-0">
                                    <a href="{{ route('admin.news.edit', $item) }}" class="line-clamp-2 font-semibold text-slate-900 hover:text-navy-700">{{ $item->title }}</a>
                                    <p class="text-xs text-slate-500">
                                        @if ($item->featured)<i class="fa-solid fa-star text-amber-500" title="À la une"></i>@endif
                                        {{ $item->club?->name ?? 'Ligue' }} · {{ $item->user?->name ?? '—' }} · {{ $item->views }} vue(s)
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3"><span class="chip text-white" style="background: {{ $item->category?->color }}">{{ $item->category?->name }}</span></td>
                        <td class="px-4 py-3"><x-admin.status :value="$item->status" :label="$item->status_label" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-slate-500">{{ $item->published_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            @if ($item->status === 'published')<a href="{{ route('news.show', $item->slug) }}" target="_blank" class="inline-flex h-9 items-center rounded-lg px-2.5 text-slate-500 hover:bg-slate-100" title="Voir"><i class="fa-regular fa-eye"></i></a>@endif
                            <a href="{{ route('admin.news.edit', $item) }}" class="inline-flex h-9 items-center rounded-lg px-2.5 text-navy-600 hover:bg-navy-50" title="Modifier"><i class="fa-regular fa-pen-to-square"></i></a>
                            <x-admin.delete :action="route('admin.news.destroy', $item)" confirm="Supprimer cette actualité ?" />
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-12 text-center text-slate-500">Aucune actualité.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $news->links() }}</div>
@endsection
