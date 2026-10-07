{{-- En-tête HTML commun (site public et administration) --}}
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/png" href="{{ asset('images/brand/favicon-32.png') }}">
<link rel="apple-touch-icon" href="{{ asset('images/brand/apple-touch-icon.png') }}">
<meta name="theme-color" content="#242868">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,600;0,700;0,800;1,700;1,800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" referrerpolicy="no-referrer">

<script src="https://cdn.tailwindcss.com?plugins=forms,typography,line-clamp"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    // Couleurs relevées sur le logo officiel LMVB
                    navy: { 50: '#eef0fa', 100: '#d9ddf2', 200: '#b3bbe4', 300: '#8792cf', 400: '#5d68b5', 500: '#3F63AF', 600: '#34509a', 700: '#2c3f80', 800: '#242868', 900: '#1a1d4d', 950: '#10122f' },
                    bordeaux: { 50: '#fcf1f2', 100: '#f8dfe1', 200: '#f0bcc1', 400: '#d0505d', 500: '#b8303d', 600: '#9A1A26', 700: '#801520', 800: '#66111a' },
                    lavande: { 100: '#ecebf7', 200: '#dcd9f0', 300: '#C3BFE4' },
                    sable: { 50: '#fdf9f1', 100: '#f8efdc', 200: '#f0dfb7' },
                    lagon: { 400: '#2dd4bf', 500: '#14b8a6', 600: '#0d9488' },
                },
                fontFamily: {
                    sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    display: ['"Barlow Condensed"', 'Inter', 'sans-serif'],
                },
            },
        },
    };
</script>
<style type="text/tailwindcss">
    @layer components {
        .container-x { @apply mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8; }
        .btn { @apply inline-flex items-center justify-center gap-2 rounded-full px-5 py-2.5 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50; }
        .btn-primary { @apply btn bg-navy-800 text-white hover:bg-navy-700 focus-visible:ring-navy-500; }
        .btn-accent { @apply btn bg-bordeaux-600 text-white hover:bg-bordeaux-700 focus-visible:ring-bordeaux-500; }
        .btn-light { @apply btn bg-white text-navy-800 ring-1 ring-navy-100 hover:bg-navy-50; }
        .btn-ghost { @apply btn text-navy-700 hover:bg-navy-50; }
        .card { @apply rounded-2xl bg-white shadow-sm ring-1 ring-slate-200/70; }
        .chip { @apply inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold; }
        .eyebrow { @apply text-xs font-bold uppercase tracking-[0.18em] text-bordeaux-600; }
        .h-display { @apply font-display font-extrabold uppercase leading-[0.95] tracking-tight; }
        .link { @apply font-semibold text-navy-600 hover:text-bordeaux-600 underline-offset-4 hover:underline; }
        .input { @apply block w-full rounded-xl border-slate-300 text-sm shadow-sm focus:border-navy-500 focus:ring-navy-500; }
    }
</style>
<style>
    [x-cloak] { display: none !important; }
    .scrollbar-thin::-webkit-scrollbar { height: 6px; width: 6px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #c7cbe3; border-radius: 9999px; }
    .tabular { font-variant-numeric: tabular-nums; }
    /* Empêche un contenu défilant (onglets, tableaux) d'élargir une grille au-delà de l'écran sur mobile */
    .grid > * { min-width: 0; }
    /* Barre de défilement du menu d'administration (fond sombre) */
    .sidebar-scroll { scrollbar-width: thin; scrollbar-color: rgba(195, 191, 228, .35) transparent; }
    .sidebar-scroll::-webkit-scrollbar { width: 8px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; margin: 8px 0; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(195, 191, 228, .25); border-radius: 9999px; border: 2px solid #10122f; }
    .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(195, 191, 228, .55); }
    .sidebar-scroll { mask-image: linear-gradient(to bottom, transparent 0, #000 12px, #000 calc(100% - 16px), transparent 100%); }
    @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation: none !important; transition: none !important; } video[autoplay] { display: none; } }
</style>
