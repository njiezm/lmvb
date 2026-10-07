@props(['item'])
{{-- Vidéo YouTube chargée au clic (pas de cookies ni de script tant que le visiteur ne lance pas la vidéo) --}}
<div {{ $attributes->class(['group relative aspect-video overflow-hidden rounded-2xl bg-navy-950 shadow-sm']) }} data-youtube="{{ $item->youtube_id }}">
    <img src="{{ $item->image_url }}" alt="" loading="lazy" class="h-full w-full object-cover opacity-80 transition group-hover:opacity-100">
    <button type="button" class="absolute inset-0 flex flex-col items-center justify-center gap-3 p-4 text-white" aria-label="Lire la vidéo : {{ $item->title }}"
            onclick="var w=this.parentElement;w.innerHTML='<iframe class=&quot;absolute inset-0 h-full w-full&quot; src=&quot;https://www.youtube-nocookie.com/embed/'+w.dataset.youtube+'?autoplay=1&amp;rel=0&quot; title=&quot;Vidéo YouTube&quot; allow=&quot;autoplay; encrypted-media; picture-in-picture&quot; allowfullscreen></iframe>'">
        <span class="flex h-16 w-16 items-center justify-center rounded-full bg-bordeaux-600 text-2xl shadow-lg transition group-hover:scale-110"><i class="fa-solid fa-play ml-1"></i></span>
        <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 to-transparent p-4 text-left text-sm font-semibold">{{ $item->title }}</span>
    </button>
</div>
