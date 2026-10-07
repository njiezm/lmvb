<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    @include('partials.head')
    @php
        $pageTitle = trim($__env->yieldContent('title'));
        $fullTitle = $pageTitle ? $pageTitle.' · LMVB' : setting('site_name').' (LMVB)';
        $description = trim($__env->yieldContent('description')) ?: setting('site_tagline');
        $ogImage = trim($__env->yieldContent('og_image')) ?: asset('images/brand/og-default.jpg');
    @endphp
    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 160) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:site_name" content="LMVB · Ligue Martiniquaise de Volley-Ball">
    <meta property="og:locale" content="fr_FR">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($description), 200) }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'SportsOrganization',
            'name' => setting('site_name'),
            'alternateName' => 'LMVB',
            'sport' => 'Volleyball',
            'url' => url('/'),
            'logo' => asset('images/brand/logo-lmvb-512.png'),
            'email' => setting('email'),
            'telephone' => setting('phone'),
            'address' => setting('address'),
            'sameAs' => array_values(array_filter([setting('facebook_url'), setting('instagram_url'), setting('youtube_url')])),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @stack('head')
</head>
<body class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-800 antialiased">
    <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow">Aller au contenu</a>

    @include('partials.navigation')

    <main id="contenu" class="flex-1">
        @if (session('success') || session('error') || session('newsletter'))
            <div class="container-x pt-6">
                @foreach (['success' => 'bg-emerald-50 text-emerald-800 ring-emerald-200', 'newsletter' => 'bg-emerald-50 text-emerald-800 ring-emerald-200', 'error' => 'bg-red-50 text-red-800 ring-red-200'] as $key => $classes)
                    @if (session($key))
                        <div class="flex items-start gap-3 rounded-xl px-4 py-3 text-sm ring-1 {{ $classes }}" role="status">
                            <i class="fa-solid {{ $key === 'error' ? 'fa-circle-exclamation' : 'fa-circle-check' }} mt-0.5"></i>
                            <span>{{ session($key) }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>

    @include('partials.footer')

    <script>
        // Menu mobile + sous-menus
        document.querySelectorAll('[data-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = document.getElementById(btn.dataset.toggle);
                var open = target.classList.toggle('hidden') === false;
                btn.setAttribute('aria-expanded', open);
                if (btn.dataset.lock) document.body.classList.toggle('overflow-hidden', open);
            });
        });
        // Onglets génériques : [data-tabs] > [data-tab="id"] / [data-panel="id"]
        document.querySelectorAll('[data-tabs]').forEach(function (group) {
            group.querySelectorAll('[data-tab]').forEach(function (tab) {
                tab.addEventListener('click', function () {
                    group.querySelectorAll('[data-tab]').forEach(function (t) { t.setAttribute('aria-selected', t === tab); });
                    group.querySelectorAll('[data-panel]').forEach(function (p) { p.classList.toggle('hidden', p.dataset.panel !== tab.dataset.tab); });
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
