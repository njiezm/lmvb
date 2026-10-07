@extends('layouts.admin')

@section('title', 'Messages')

@section('content')
<x-admin.page title="Messages reçus" :subtitle="$contacts->total().' message(s)'">
    <x-slot:actions>
        <form action="{{ route('admin.contacts.destroyRead') }}" method="POST" onsubmit="return confirm('Supprimer définitivement tous les messages lus ?')">
            @csrf @method('DELETE')
            <button class="btn-light text-red-600"><i class="fa-regular fa-trash-can"></i> Supprimer les messages lus</button>
        </form>
    </x-slot:actions>
</x-admin.page>

<form method="GET" class="card mb-4 grid gap-3 p-4 sm:grid-cols-[1fr_12rem_auto]">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Nom, email ou sujet…" class="input">
    <select name="status" class="input">
        <option value="">Tous</option>
        <option value="0" @selected(request('status') === '0')>Non lus</option>
        <option value="1" @selected(request('status') === '1')>Lus</option>
    </select>
    <button class="btn-light"><i class="fa-solid fa-filter"></i> Filtrer</button>
</form>

<form action="{{ route('admin.contacts.markRead') }}" method="POST">
    @csrf
    <div class="card overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-4 py-2">
            <label class="flex items-center gap-2 text-sm text-slate-600"><input type="checkbox" data-check-all=".contact-checkbox" class="rounded border-slate-300"> Tout sélectionner</label>
            <button class="btn-ghost !py-1.5 text-sm"><i class="fa-regular fa-envelope-open"></i> Marquer comme lu</button>
        </div>
        <ul class="divide-y divide-slate-100">
            @forelse ($contacts as $contact)
                <li @class(['flex items-start gap-3 px-4 py-3 hover:bg-slate-50', 'bg-navy-50/50' => ! $contact->read])>
                    <input type="checkbox" name="ids[]" value="{{ $contact->id }}" class="contact-checkbox mt-1.5 rounded border-slate-300" aria-label="Sélectionner">
                    <a href="{{ route('admin.contacts.show', $contact) }}" class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            @unless ($contact->read)<span class="h-2 w-2 shrink-0 rounded-full bg-bordeaux-600"></span>@endunless
                            <span @class(['truncate', 'font-bold text-slate-900' => ! $contact->read, 'text-slate-700' => $contact->read])>{{ $contact->name }}</span>
                            <span class="ml-auto shrink-0 text-xs text-slate-400">{{ $contact->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <p class="truncate text-sm text-slate-600"><strong>{{ $contact->subject }}</strong>@if ($contact->club) · <span class="text-navy-600">{{ $contact->club->name }}</span>@endif · {{ \Illuminate\Support\Str::limit($contact->message, 90) }}</p>
                    </a>
                </li>
            @empty
                <li class="px-4 py-12 text-center text-slate-500">Aucun message.</li>
            @endforelse
        </ul>
    </div>
</form>
<div class="mt-4">{{ $contacts->links() }}</div>
@endsection
