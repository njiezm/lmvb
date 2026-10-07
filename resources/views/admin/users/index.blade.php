@extends('layouts.admin')

@section('title', 'Utilisateurs')

@section('content')
<x-admin.page title="Utilisateurs" subtitle="Super admins (tout le site) et administrateurs de club (leur club uniquement). L'inscription publique est désactivée.">
    <x-slot:actions><a href="{{ route('admin.users.create') }}" class="btn-primary"><i class="fa-solid fa-user-plus"></i> Nouveau compte</a></x-slot:actions>
</x-admin.page>

<form method="GET" class="card mb-4 flex gap-3 p-4">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Nom ou email…" class="input">
    <button class="btn-light"><i class="fa-solid fa-magnifying-glass"></i></button>
</form>

<div class="card overflow-hidden">
    <div class="scrollbar-thin overflow-x-auto">
        <table class="w-full min-w-[44rem] text-sm">
            <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                <tr><th class="px-4 py-3">Utilisateur</th><th class="px-4 py-3">Rôle</th><th class="px-4 py-3">Club</th><th class="px-4 py-3">Dernière connexion</th><th class="px-4 py-3">Statut</th><th class="px-4 py-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($users as $user)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><p class="font-semibold text-slate-900">{{ $user->name }}</p><p class="text-xs text-slate-500">{{ $user->email }}</p></td>
                        <td class="px-4 py-3">
                            @if ($user->isSuperAdmin())<span class="chip bg-bordeaux-50 text-bordeaux-700"><i class="fa-solid fa-crown"></i> Super admin</span>
                            @elseif ($user->role === 'club_admin')<span class="chip bg-navy-50 text-navy-700">Admin club</span>
                            @else<span class="chip bg-slate-100 text-slate-500">Aucun accès</span>@endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $user->club?->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->last_login_at?->diffForHumans() ?? 'Jamais' }}</td>
                        <td class="px-4 py-3"><x-admin.status :value="$user->active ? 'active' : 'inactive'" :label="$user->active ? 'Actif' : 'Désactivé'" /></td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex h-9 items-center rounded-lg px-2.5 text-navy-600 hover:bg-navy-50" title="Modifier"><i class="fa-regular fa-pen-to-square"></i></a>
                            @unless ($user->is(auth()->user()))<x-admin.delete :action="route('admin.users.destroy', $user)" confirm="Supprimer ce compte ?" />@endunless
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-4">{{ $users->links() }}</div>
@endsection
