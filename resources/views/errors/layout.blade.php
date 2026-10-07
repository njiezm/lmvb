<!DOCTYPE html>
<html lang="fr">
<head>
    @include('partials.head')
    <title>@yield('code') · @yield('title') · LMVB</title>
    <meta name="robots" content="noindex">
</head>
<body class="flex min-h-screen items-center justify-center bg-navy-950 px-4 font-sans text-white antialiased">
    <img src="{{ asset('images/photos/indoor-gymnase-ballon.jpg') }}" alt="" class="fixed inset-0 -z-10 h-full w-full object-cover opacity-20">
    <main class="max-w-lg text-center">
        <a href="{{ url('/') }}" class="inline-block rounded-full bg-white p-2"><img src="{{ asset('images/brand/logo-lmvb-128.png') }}" alt="LMVB" class="h-20 w-20"></a>
        <p class="mt-8 font-display text-8xl font-extrabold text-lavande-300">@yield('code')</p>
        <h1 class="h-display mt-2 text-4xl">@yield('title')</h1>
        <p class="mt-4 text-navy-100">@yield('message')</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ url('/') }}" class="btn bg-white text-navy-900 hover:bg-lavande-100"><i class="fa-solid fa-house"></i> Accueil</a>
            <a href="{{ url('/matchs/resultats') }}" class="btn text-white ring-1 ring-white/40 hover:bg-white/10">Résultats</a>
        </div>
    </main>
</body>
</html>
