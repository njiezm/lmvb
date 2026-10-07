<!DOCTYPE html>
<html lang="fr">
<head>
    @include('partials.head')
    <title>@yield('title', 'Connexion') · LMVB</title>
    <meta name="robots" content="noindex">
</head>
<body class="min-h-screen bg-navy-950 font-sans text-slate-800 antialiased">
    <div class="grid min-h-screen lg:grid-cols-2">
        {{-- Visuel --}}
        <div class="relative hidden overflow-hidden lg:block">
            <img src="{{ asset('images/photos/indoor-filet-silhouette.jpg') }}" alt="" class="absolute inset-0 h-full w-full object-cover opacity-50">
            <div class="absolute inset-0 bg-gradient-to-br from-navy-950 via-navy-900/80 to-bordeaux-800/60"></div>
            <div class="relative flex h-full flex-col justify-between p-12 text-white">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <span class="rounded-full bg-white p-1.5"><img src="{{ asset('images/brand/logo-lmvb-128.png') }}" alt="" class="h-12 w-12"></span>
                    <span class="font-display text-3xl font-extrabold uppercase">LMVB</span>
                </a>
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-lavande-300">Espace administration</p>
                    <h1 class="h-display mt-3 text-6xl">Gérez le volley<br>martiniquais</h1>
                    <p class="mt-4 max-w-md text-navy-100">Actualités, clubs, galerie, sélections, beach-volley et résultats synchronisés avec la FFVolley.</p>
                </div>
                <p class="text-xs text-navy-300">© {{ date('Y') }} Ligue Martiniquaise de Volley-Ball</p>
            </div>
        </div>

        {{-- Formulaire --}}
        <div class="flex items-center justify-center bg-slate-50 px-4 py-12 sm:px-8">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-8 flex items-center justify-center gap-3 lg:hidden">
                    <img src="{{ asset('images/brand/logo-lmvb-128.png') }}" alt="Logo LMVB" class="h-16 w-16">
                    <span class="font-display text-3xl font-extrabold uppercase text-navy-800">LMVB</span>
                </a>
                <div class="rounded-3xl bg-white p-8 shadow-xl ring-1 ring-slate-200 sm:p-10">
                    @if (session('status'))
                        <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm text-emerald-800 ring-1 ring-emerald-200" role="status">{{ session('status') }}</div>
                    @endif
                    @yield('content')
                </div>
                <p class="mt-6 text-center text-sm text-slate-500"><a href="{{ route('home') }}" class="link"><i class="fa-solid fa-arrow-left mr-1"></i>Retour au site</a></p>
            </div>
        </div>
    </div>
</body>
</html>
