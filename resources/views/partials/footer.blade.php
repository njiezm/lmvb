<footer class="relative mt-20 overflow-hidden bg-navy-950 text-navy-100">
    <div class="absolute inset-x-0 top-0 h-1.5 bg-gradient-to-r from-navy-500 via-lavande-300 to-bordeaux-600"></div>
    <svg class="pointer-events-none absolute -right-24 -top-24 h-96 w-96 text-white/[0.03]" viewBox="0 0 100 100" aria-hidden="true"><circle cx="50" cy="50" r="48" fill="none" stroke="currentColor" stroke-width="4"/><path d="M50 2c-10 20-10 76 0 96M2 50c20-10 76-10 96 0M14 18c22 8 50 40 58 76M86 18C64 26 36 58 28 94" fill="none" stroke="currentColor" stroke-width="3"/></svg>

    <div class="container-x relative grid gap-12 py-16 md:grid-cols-2 lg:grid-cols-12">
        <div class="lg:col-span-4">
            <div class="flex items-center gap-3">
                <span class="rounded-full bg-white p-1.5"><img src="{{ asset('images/brand/logo-lmvb-128.png') }}" alt="" class="h-14 w-14"></span>
                <div>
                    <p class="font-display text-3xl font-extrabold uppercase text-white">LMVB</p>
                    <p class="text-xs uppercase tracking-wider text-navy-300">Ligue Martiniquaise de Volley-Ball</p>
                </div>
            </div>
            <p class="mt-5 max-w-sm text-sm leading-relaxed text-navy-200">{{ setting('site_tagline') }}</p>
            <div class="mt-6 flex gap-3">
                @foreach (['facebook_url' => ['fa-facebook-f', 'Facebook'], 'instagram_url' => ['fa-instagram', 'Instagram'], 'youtube_url' => ['fa-youtube', 'YouTube']] as $key => [$icon, $label])
                    @if (setting($key))
                        <a href="{{ setting($key) }}" target="_blank" rel="noopener" aria-label="{{ $label }}"
                           class="flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-bordeaux-600"><i class="fa-brands {{ $icon }}"></i></a>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-2">
            <h2 class="font-display text-lg font-bold uppercase tracking-wide text-white">Compétitions</h2>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a class="hover:text-white" href="{{ route('competitions.index') }}">Championnats & classements</a></li>
                <li><a class="hover:text-white" href="{{ route('games.index') }}">Prochains matchs</a></li>
                <li><a class="hover:text-white" href="{{ route('games.results') }}">Résultats</a></li>
                <li><a class="hover:text-white" href="{{ route('beach.index') }}">Beach-volley</a></li>
                <li><a class="hover:text-white" href="{{ route('selections.index') }}">Sélections</a></li>
            </ul>
        </div>

        <div class="lg:col-span-2">
            <h2 class="font-display text-lg font-bold uppercase tracking-wide text-white">La ligue</h2>
            <ul class="mt-4 space-y-2.5 text-sm">
                <li><a class="hover:text-white" href="{{ route('league') }}">Présentation & comité</a></li>
                <li><a class="hover:text-white" href="{{ route('league') }}#documents">Documents officiels</a></li>
                <li><a class="hover:text-white" href="{{ route('clubs.index') }}">Trouver un club</a></li>
                <li><a class="hover:text-white" href="{{ setting('license_url') }}" target="_blank" rel="noopener">Prendre sa licence</a></li>
                <li><a class="hover:text-white" href="{{ route('legal') }}">Mentions légales</a></li>
            </ul>
        </div>

        <div class="md:col-span-2 lg:col-span-4">
            <h2 class="font-display text-lg font-bold uppercase tracking-wide text-white">Restez informé</h2>
            <p class="mt-2 text-sm text-navy-200">Résultats, convocations des sélections, tournois beach : recevez les infos de la ligue.</p>
            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mt-4 flex flex-col gap-2 sm:flex-row">
                @csrf
                <label for="newsletter_email" class="sr-only">Votre email</label>
                <input id="newsletter_email" type="email" name="newsletter_email" required placeholder="votre@email.fr" value="{{ old('newsletter_email') }}"
                       class="min-w-0 flex-1 rounded-full border-0 bg-white/10 px-5 py-3 text-sm text-white placeholder:text-navy-300 focus:ring-2 focus:ring-lavande-300">
                <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                <button class="btn-accent py-3">S'inscrire</button>
            </form>
            @error('newsletter_email') <p class="mt-2 text-sm text-red-300">{{ $message }}</p> @enderror

            <address class="mt-6 space-y-1.5 text-sm not-italic text-navy-200">
                @if (setting('address'))<p><i class="fa-solid fa-location-dot mr-2 w-4 text-lavande-300"></i>{{ setting('address') }}</p>@endif
                @if (setting('phone'))<p><i class="fa-solid fa-phone mr-2 w-4 text-lavande-300"></i><a href="tel:{{ preg_replace('/\s+/', '', setting('phone')) }}" class="hover:text-white">{{ setting('phone') }}</a></p>@endif
                <p><i class="fa-regular fa-envelope mr-2 w-4 text-lavande-300"></i><a href="mailto:{{ setting('email') }}" class="hover:text-white">{{ setting('email') }}</a></p>
            </address>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="container-x flex flex-col items-center justify-between gap-3 py-6 text-xs text-navy-300 sm:flex-row">
            <p>© {{ date('Y') }} Ligue Martiniquaise de Volley-Ball · Fait en Martinique 🇲🇶</p>
            <p class="flex items-center gap-2">
                Résultats officiels :
                <a href="https://www.ffvbbeach.org/ffvbapp/resu/vbspo_home.php?codent=LIMART" target="_blank" rel="noopener" class="font-semibold text-white hover:underline">FFVolley</a>
                <img src="{{ asset('images/brand/ffvolley.png') }}" alt="FFVolley" class="h-5 rounded bg-white px-1">
            </p>
        </div>
    </div>
</footer>
