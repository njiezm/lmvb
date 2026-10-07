@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

@section('content')
<h2 class="h-display text-4xl text-navy-900">Mot de passe oublié</h2>
<p class="mt-1 text-sm text-slate-500">Indiquez votre email : vous recevrez un lien pour choisir un nouveau mot de passe.</p>

<form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-5">
    @csrf
    <div>
        <label for="email" class="text-sm font-medium text-slate-700">Adresse email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus class="input mt-1 !py-3 @error('email') !border-red-500 @enderror">
        @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="btn-primary w-full py-3">Envoyer le lien</button>
    <p class="text-center text-sm"><a href="{{ route('login') }}" class="link">Retour à la connexion</a></p>
</form>
@endsection
