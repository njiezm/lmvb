@extends('layouts.admin')

@section('title', 'Inscriptions : '.$event->title)

@section('content')
<x-admin.page :title="'Inscriptions : '.$event->title" :subtitle="$event->registered_teams.' équipe(s) confirmée(s) ou en attente'.($event->max_teams ? ' sur '.$event->max_teams : '')" :back="route('admin.beach.index')" />

<div class="card overflow-hidden">
    <div class="scrollbar-thin overflow-x-auto">
        <table class="w-full min-w-[52rem] text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                <tr><th class="px-4 py-3">Équipe</th><th class="px-4 py-3">Joueur·se 1</th><th class="px-4 py-3">Joueur·se 2</th><th class="px-4 py-3">Téléphone</th><th class="px-4 py-3">Reçue</th><th class="px-4 py-3">Statut</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($event->registrations as $r)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $r->team_name }}</td>
                        <td class="px-4 py-3">{{ $r->player1_name }}<br><a class="link text-xs" href="mailto:{{ $r->player1_email }}">{{ $r->player1_email }}</a></td>
                        <td class="px-4 py-3">{{ $r->player2_name }}<br><a class="link text-xs" href="mailto:{{ $r->player2_email }}">{{ $r->player2_email }}</a></td>
                        <td class="px-4 py-3"><a class="link" href="tel:{{ $r->phone }}">{{ $r->phone }}</a></td>
                        <td class="px-4 py-3 text-slate-500">{{ $r->created_at->format('d/m H:i') }}</td>
                        <td class="px-4 py-3">
                            <form action="{{ route('admin.beach.registrations.update', $r) }}" method="POST">
                                @csrf @method('PUT')
                                <select name="status" class="input !py-1.5 text-xs" onchange="this.form.submit()" aria-label="Statut">
                                    @foreach (\App\Models\BeachRegistration::STATUSES as $k => $l)<option value="{{ $k }}" @selected($r->status === $k)>{{ $l }}</option>@endforeach
                                </select>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">Aucune inscription pour le moment.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
