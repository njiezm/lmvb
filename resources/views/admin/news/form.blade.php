@extends('layouts.admin')

@section('title', $news->exists ? 'Modifier l\'actualité' : 'Nouvelle actualité')

@push('head')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <style>.ql-toolbar.ql-snow{border-radius:.75rem .75rem 0 0;border-color:#cbd5e1}.ql-container.ql-snow{border-radius:0 0 .75rem .75rem;border-color:#cbd5e1;font-family:Inter,sans-serif;font-size:1rem}.ql-editor{min-height:320px}</style>
@endpush

@section('content')
<x-admin.page :title="$news->exists ? 'Modifier l\'actualité' : 'Nouvelle actualité'" :back="route('admin.news.index')">
    @if ($news->exists && $news->status === 'published')
        <x-slot:actions><a href="{{ route('news.show', $news->slug) }}" target="_blank" class="btn-light"><i class="fa-regular fa-eye"></i> Voir l'article</a></x-slot:actions>
    @endif
</x-admin.page>

<form action="{{ $news->exists ? route('admin.news.update', $news) : route('admin.news.store') }}" method="POST" enctype="multipart/form-data" id="news-form" class="grid gap-6 lg:grid-cols-3">
    @csrf
    @if ($news->exists) @method('PUT') @endif

    <div class="card space-y-5 p-6 lg:col-span-2">
        <x-admin.input name="title" label="Titre" :value="$news->title" required maxlength="200" />
        <x-admin.textarea name="excerpt" label="Chapô (résumé affiché dans les listes)" :value="$news->excerpt" rows="3" required maxlength="400" />
        <div>
            <label class="block text-sm font-medium text-slate-700">Contenu <span class="text-bordeaux-600">*</span></label>
            <div id="editor" class="mt-1 bg-white">{!! safe_html(old('content', $news->content)) !!}</div>
            <input type="hidden" name="content" id="content" value="{{ old('content', $news->content) }}">
            <p class="mt-1 text-xs text-slate-500">Astuce : pour intégrer une vidéo, utilisez le bouton vidéo et collez un lien YouTube.</p>
            @error('content')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="space-y-6">
        <div class="card space-y-5 p-6">
            <x-admin.select name="status" label="Statut" :options="\App\Models\News::STATUSES" :value="$news->status" required />
            <x-admin.input name="published_at" type="datetime-local" label="Date de publication" :value="$news->published_at?->format('Y-m-d\TH:i')" help="Laisser vide pour publier immédiatement. Une date future programme la publication." />
            <x-admin.select name="category_id" label="Catégorie" :options="$categories->pluck('name', 'id')" :value="$news->category_id" placeholder="Choisir…" required />
            @if (auth()->user()->isSuperAdmin())
                <x-admin.select name="club_id" label="Club concerné" :options="$clubs->pluck('name', 'id')" :value="$news->club_id" placeholder="Ligue (aucun club)" />
                <x-admin.toggle name="featured" label="Mettre à la une" :checked="$news->featured" />
            @endif
        </div>
        <div class="card space-y-5 p-6">
            <x-admin.image name="image" label="Image principale" :current="$news->image" removable="remove_image" />
            <x-admin.input name="image_credit" label="Crédit photo" :value="$news->image_credit" />
            <x-admin.input name="source_url" type="url" label="Source (lien)" :value="$news->source_url" placeholder="https://" />
        </div>
        <button class="btn-primary w-full py-3"><i class="fa-solid fa-floppy-disk"></i> Enregistrer</button>
    </div>
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    var quill = new Quill('#editor', {
        theme: 'snow',
        placeholder: 'Rédigez votre article…',
        modules: { toolbar: [[{ header: [2, 3, false] }], ['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }], ['blockquote', 'link', 'image', 'video'], ['clean']] },
    });
    document.getElementById('news-form').addEventListener('submit', function () {
        document.getElementById('content').value = quill.root.innerHTML;
    });
</script>
@endpush
