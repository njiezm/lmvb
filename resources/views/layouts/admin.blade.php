<!DOCTYPE html>
<html lang="fr">
<head>
    @include('partials.head')
    <title>@yield('title', 'Administration') · Admin LMVB</title>
    <meta name="robots" content="noindex, nofollow">
    @stack('head')
</head>
<body class="min-h-screen bg-slate-100 font-sans text-slate-800 antialiased">
    {{-- Fond du tiroir mobile --}}
    <div id="sidebar-backdrop" class="fixed inset-0 z-40 hidden bg-navy-950/60 lg:hidden" onclick="toggleSidebar(false)"></div>

    @include('admin.partials.sidebar')

    <div class="lg:pl-72">
        <header class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-slate-200 bg-white/95 px-4 backdrop-blur sm:px-6">
            <button type="button" class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 lg:hidden" onclick="toggleSidebar(true)" aria-label="Ouvrir le menu">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
            <p class="min-w-0 flex-1 truncate text-sm text-slate-500">
                @if (auth()->user()->isSuperAdmin())
                    <span class="chip bg-bordeaux-50 text-bordeaux-700"><i class="fa-solid fa-crown"></i> Super admin</span>
                @else
                    <span class="chip bg-navy-50 text-navy-700"><i class="fa-solid fa-people-group"></i> {{ auth()->user()->club?->name }}</span>
                @endif
            </p>
            @php $unread = \App\Models\Contact::visibleTo(auth()->user())->where('read', false)->count(); @endphp
            <a href="{{ route('admin.contacts.index', ['status' => 0]) }}" class="relative inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100" aria-label="{{ $unread }} message(s) non lu(s)">
                <i class="fa-regular fa-bell text-lg"></i>
                @if ($unread)<span class="absolute right-1 top-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-bordeaux-600 px-1 text-[0.6rem] font-bold text-white">{{ $unread > 9 ? '9+' : $unread }}</span>@endif
            </a>
            <a href="{{ route('home') }}" target="_blank" class="hidden h-10 items-center gap-2 rounded-lg px-3 text-sm text-slate-600 hover:bg-slate-100 sm:inline-flex"><i class="fa-solid fa-arrow-up-right-from-square"></i> Voir le site</a>
            <a href="{{ route('admin.profile.edit') }}" class="flex items-center gap-2 rounded-lg px-2 py-1 hover:bg-slate-100" title="Mon profil">
                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-navy-800 text-sm font-bold text-white">{{ \App\Support\Text::initials(auth()->user()->name) }}</span>
                <span class="hidden text-sm font-medium md:block">{{ auth()->user()->name }}</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100" title="Déconnexion" aria-label="Déconnexion"><i class="fa-solid fa-arrow-right-from-bracket"></i></button>
            </form>
        </header>

        <main class="mx-auto max-w-7xl p-4 sm:p-6 lg:p-8">
            @include('admin.partials.notifications')
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar(open) {
            document.getElementById('admin-sidebar').classList.toggle('-translate-x-full', !open);
            document.getElementById('sidebar-backdrop').classList.toggle('hidden', !open);
            document.body.classList.toggle('overflow-hidden', open);
        }
        // Cocher / décocher toutes les cases d'un tableau
        document.querySelectorAll('[data-check-all]').forEach(function (master) {
            master.addEventListener('change', function () {
                document.querySelectorAll(master.dataset.checkAll).forEach(function (cb) { cb.checked = master.checked; });
            });
        });
        // Disparition automatique des notifications
        setTimeout(function () { document.querySelectorAll('[data-flash]').forEach(function (el) { el.remove(); }); }, 6000);
    </script>
    @stack('scripts')
</body>
</html>
