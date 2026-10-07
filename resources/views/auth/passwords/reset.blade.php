@extends('layouts.auth')

@section('title', 'Nouveau mot de passe')

@section('content')
<h2 class="h-display text-4xl text-navy-900">Nouveau mot de passe</h2>

<form method="POST" action="{{ route('password.update') }}" class="mt-8 space-y-5">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div>
        <label for="email" class="text-sm font-medium text-slate-700">Adresse email</label>
        <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" class="input mt-1 !py-3 @error('email') !border-red-500 @enderror">
        @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password" class="text-sm font-medium text-slate-700">Nouveau mot de passe</label>
        <input id="password" type="password" name="password" required autocomplete="new-password" class="input mt-1 !py-3 @error('password') !border-red-500 @enderror">
        @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="password-confirm" class="text-sm font-medium text-slate-700">Confirmation</label>
        <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password" class="input mt-1 !py-3">
    </div>
    <button type="submit" class="btn-primary w-full py-3">Enregistrer le mot de passe</button>
</form>
@endsection
