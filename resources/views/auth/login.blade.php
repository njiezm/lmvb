@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<h2 class="h-display text-4xl text-navy-900">Connexion</h2>
<p class="mt-1 text-sm text-slate-500">Accès réservé aux administrateurs de la ligue et des clubs.</p>

<form method="POST" action="{{ route('login') }}" class="mt-8 space-y-5">
    @csrf
    <div>
        <label for="email" class="text-sm font-medium text-slate-700">Adresse email</label>
        <div class="relative mt-1">
            <i class="fa-regular fa-envelope pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                   class="input !py-3 pl-11 @error('email') !border-red-500 @enderror">
        </div>
        @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <div class="flex items-center justify-between">
            <label for="password" class="text-sm font-medium text-slate-700">Mot de passe</label>
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-navy-600 hover:text-bordeaux-600" href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            @endif
        </div>
        <div class="relative mt-1">
            <i class="fa-solid fa-lock pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="input !py-3 pl-11 pr-11 @error('password') !border-red-500 @enderror">
            <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-navy-700" aria-label="Afficher le mot de passe"
                    onclick="var i=document.getElementById('password');i.type=i.type==='password'?'text':'password';this.firstElementChild.classList.toggle('fa-eye-slash')">
                <i class="fa-regular fa-eye"></i>
            </button>
        </div>
        @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <label class="flex items-center gap-2 text-sm text-slate-600">
        <input type="checkbox" name="remember" class="rounded border-slate-300 text-navy-700 focus:ring-navy-500" {{ old('remember') ? 'checked' : '' }}>
        Rester connecté
    </label>

    <button type="submit" class="btn-primary w-full py-3 text-base">Se connecter <i class="fa-solid fa-arrow-right-to-bracket"></i></button>
</form>
@endsection
