<a href="{{ route('beach.show', $event) }}" class="group card flex flex-col overflow-hidden transition hover:-translate-y-0.5 hover:shadow-lg">
    <div class="relative aspect-[16/9] overflow-hidden bg-sable-100">
        <img src="{{ $event->image ? asset($event->image) : asset('images/photos/beach-1.jpg') }}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        <div class="absolute left-3 top-3 flex flex-col items-center rounded-xl bg-white px-3 py-1.5 leading-none shadow">
            <span class="font-display text-2xl font-extrabold text-navy-900">{{ $event->start_date->format('d') }}</span>
            <span class="text-[0.65rem] font-bold uppercase text-bordeaux-600">{{ $event->start_date->translatedFormat('M') }}</span>
        </div>
        <span class="chip absolute right-3 top-3 bg-navy-900/80 text-white">{{ $event->type_label }}</span>
    </div>
    <div class="flex flex-1 flex-col p-5">
        <h3 class="font-display text-xl font-bold uppercase leading-tight text-navy-900 group-hover:text-bordeaux-600">{{ $event->title }}</h3>
        <p class="mt-1 text-sm text-slate-500"><i class="fa-solid fa-location-dot mr-1"></i>{{ $event->location }}</p>
        <div class="mt-auto flex items-center justify-between pt-4 text-xs">
            @if ($event->canRegister())
                <span class="chip bg-emerald-100 text-emerald-800">Inscriptions ouvertes{{ $event->spots_left !== null ? ' · '.$event->spots_left.' places' : '' }}</span>
            @else
                <span class="chip bg-slate-100 text-slate-600">{{ $event->status_label }}</span>
            @endif
            <span class="font-semibold text-navy-600">Détails →</span>
        </div>
    </div>
</a>
