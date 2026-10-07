@extends('layouts.admin')

@section('title', $contact->subject)

@section('content')
<x-admin.page :title="$contact->subject" :back="route('admin.contacts.index')">
    <x-slot:actions>
        <a href="mailto:{{ $contact->email }}?subject={{ rawurlencode('Re: '.$contact->subject) }}" class="btn-primary"><i class="fa-solid fa-reply"></i> Répondre par email</a>
        <x-admin.delete :action="route('admin.contacts.destroy', $contact)" confirm="Supprimer ce message ?" label="Supprimer" class="!h-10 ring-1 ring-red-200" />
    </x-slot:actions>
</x-admin.page>

<div class="grid gap-6 lg:grid-cols-3">
    <div class="card p-6 lg:col-span-2">
        <p class="whitespace-pre-line leading-relaxed text-slate-800">{{ $contact->message }}</p>
    </div>
    <aside class="card p-6">
        <dl class="space-y-4 text-sm">
            <div><dt class="text-xs uppercase text-slate-500">Expéditeur</dt><dd class="font-semibold">{{ $contact->name }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Email</dt><dd><a href="mailto:{{ $contact->email }}" class="link break-all">{{ $contact->email }}</a></dd></div>
            @if ($contact->phone)<div><dt class="text-xs uppercase text-slate-500">Téléphone</dt><dd><a href="tel:{{ $contact->phone }}" class="link">{{ $contact->phone }}</a></dd></div>@endif
            <div><dt class="text-xs uppercase text-slate-500">Destinataire</dt><dd>{{ $contact->club?->name ?? 'La ligue' }}</dd></div>
            <div><dt class="text-xs uppercase text-slate-500">Reçu le</dt><dd>{{ $contact->created_at->translatedFormat('l d F Y à H:i') }}</dd></div>
        </dl>
    </aside>
</div>
@endsection
