@extends('layouts.admin')

@section('title', 'Newsletter')

@section('content')
<x-admin.page title="Newsletter" :subtitle="$activeCount.' inscrit(s) actif(s) · formulaire en pied de page du site'">
    <x-slot:actions><a href="{{ route('admin.newsletter.export') }}" class="btn-primary"><i class="fa-solid fa-file-csv"></i> Exporter (CSV)</a></x-slot:actions>
</x-admin.page>

<p class="mb-4 rounded-xl bg-navy-50 px-4 py-3 text-sm text-navy-900">L'export CSV s'importe directement dans Brevo, Mailchimp ou Gmail. Chaque inscrit dispose d'un lien de désinscription personnel.</p>

<div class="card divide-y divide-slate-100">
    @forelse ($subscribers as $s)
        <div class="flex items-center gap-3 px-4 py-3 text-sm">
            <i class="fa-regular fa-envelope text-slate-400"></i>
            <span class="min-w-0 flex-1 truncate">{{ $s->email }}</span>
            <span class="text-xs text-slate-500">{{ $s->created_at->format('d/m/Y') }}</span>
            @if ($s->unsubscribed_at)<span class="chip bg-slate-100 text-slate-500">Désinscrit</span>@endif
        </div>
    @empty
        <p class="px-4 py-12 text-center text-slate-500">Aucun inscrit pour le moment.</p>
    @endforelse
</div>
<div class="mt-4">{{ $subscribers->links() }}</div>
@endsection
