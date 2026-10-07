@props(['title', 'eyebrow' => null, 'subtitle' => null, 'image' => 'images/photos/indoor-terrain-vue-aerienne.jpg', 'breadcrumbs' => []])
<section class="relative isolate overflow-hidden bg-navy-900 text-white">
    <img src="{{ asset($image) }}" alt="" class="absolute inset-0 -z-10 h-full w-full object-cover opacity-30" loading="eager">
    <div class="absolute inset-0 -z-10 bg-gradient-to-r from-navy-950 via-navy-900/90 to-navy-800/40"></div>
    <div class="container-x py-12 sm:py-16">
        @if ($breadcrumbs)
            <nav aria-label="Fil d'Ariane" class="mb-4 text-xs text-navy-200">
                <ol class="flex flex-wrap items-center gap-1.5">
                    <li><a href="{{ route('home') }}" class="hover:text-white">Accueil</a></li>
                    @foreach ($breadcrumbs as $label => $url)
                        <li class="flex items-center gap-1.5"><i class="fa-solid fa-chevron-right text-[0.6rem] opacity-60"></i>
                            @if ($url)<a href="{{ $url }}" class="hover:text-white">{{ $label }}</a>@else<span class="text-white">{{ $label }}</span>@endif
                        </li>
                    @endforeach
                </ol>
            </nav>
        @endif
        @if ($eyebrow)<p class="text-xs font-bold uppercase tracking-[0.2em] text-lavande-300">{{ $eyebrow }}</p>@endif
        <h1 class="h-display mt-2 max-w-4xl text-4xl sm:text-5xl lg:text-6xl">{{ $title }}</h1>
        @if ($subtitle)<p class="mt-4 max-w-2xl text-base text-navy-100 sm:text-lg">{{ $subtitle }}</p>@endif
        {{ $slot }}
    </div>
</section>
