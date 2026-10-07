@props(['full' => false])
{{-- « Le mot de la Présidente » : photo, message et signature (Admin > Réglages) --}}
@php
    $name = setting('president_name');
    $photo = setting('president_photo');
    $message = setting('president_message');
    $paragraphs = collect(preg_split('/\R{2,}/', trim((string) $message)))->filter();
    if (! $full) {
        $paragraphs = $paragraphs->take(2);
    }
@endphp
@if ($name)
<section {{ $attributes->class(['relative overflow-hidden']) }} aria-labelledby="mot-presidente">
    <div class="container-x grid items-center gap-10 lg:grid-cols-12">
        <div class="relative mx-auto w-full max-w-sm lg:col-span-5 lg:max-w-none">
            <div class="absolute -left-4 -top-4 h-full w-full rounded-[2rem] bg-gradient-to-br from-navy-500 to-bordeaux-600 opacity-90"></div>
            @if ($photo)
                <img src="{{ asset($photo) }}" alt="{{ $name }}, {{ setting('president_title') }}" loading="lazy"
                     class="relative aspect-[4/5] w-full rounded-[2rem] object-cover object-top shadow-xl">
            @else
                <div class="relative flex aspect-[4/5] w-full flex-col items-center justify-center rounded-[2rem] bg-navy-900 text-white shadow-xl">
                    <img src="{{ asset('images/brand/logo-lmvb-256.png') }}" alt="" class="h-40 w-40 rounded-full bg-white p-2">
                    <span class="mt-6 font-display text-3xl font-extrabold uppercase">{{ $name }}</span>
                </div>
            @endif
            <div class="absolute -bottom-5 right-4 rounded-2xl bg-white px-5 py-3 shadow-lg ring-1 ring-slate-200">
                <p class="font-display text-xl font-extrabold uppercase leading-none text-navy-900">{{ $name }}</p>
                <p class="mt-1 text-xs text-slate-500">{{ setting('president_title') }}</p>
            </div>
        </div>

        <div class="lg:col-span-7">
            <p class="eyebrow">Édito</p>
            <h2 id="mot-presidente" class="h-display mt-1 text-4xl text-navy-900 sm:text-5xl">Le mot de la Présidente</h2>
            <i class="fa-solid fa-quote-left mt-6 block text-4xl text-lavande-300" aria-hidden="true"></i>
            @if ($paragraphs->isNotEmpty())
                <div class="mt-3 space-y-4 text-lg leading-relaxed text-slate-700">
                    @foreach ($paragraphs as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            @else
                <p class="mt-3 text-lg text-slate-500">Message à venir.</p>
            @endif
            <p class="mt-6 font-display text-2xl font-bold italic text-bordeaux-600">{{ $name }}</p>
            @unless ($full)
                <a href="{{ route('league') }}#mot-presidente" class="btn-primary mt-6">Lire l'édito complet & découvrir la ligue</a>
            @endunless
        </div>
    </div>
</section>
@endif
