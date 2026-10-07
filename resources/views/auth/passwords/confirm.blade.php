@extends('layouts.auth')

@section('title', 'Confirmation')

@section('content')
<h2 class="h-display text-4xl text-navy-900">Confirmez votre mot de passe</h2>
<p class="mt-1 text-sm text-slate-500">Par sécurité, saisissez à nouveau votre mot de passe pour continuer.</p>

<form method="POST" action="{{ route('password.confirm') }}" class="mt-8 space-y-5">
    @csrf
    <div>
        <label for="password" class="text-sm font-medium text-slate-700">Mot de passe</label>
        <input id="password" type="password" name="password" required autocomplete="current-password" class="input mt-1 !py-3 @error('password') !border-red-500 @enderror">
        @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <button type="submit" class="btn-primary w-full py-3">Confirmer</button>
</form>
@endsection
