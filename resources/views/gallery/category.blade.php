@extends('layouts.app')

@section('title', 'Galerie - ' . ucfirst($category))

@section('content')
<section class="py-20 px-6 lg:px-24 bg-gray-100">
    <div class="max-w-6xl mx-auto">
        <div class="breadcrumb flex items-center gap-2 mb-6 text-sm">
            <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Accueil</a>
            <span>/</span>
            <a href="{{ route('gallery.index') }}" class="text-blue-600 hover:underline">Galerie</a>
            <span>/</span>
            <span>{{ ucfirst($category) }}</span>
        </div>
        
        <h1 class="text-5xl font-hand mb-12 text-center brush-underline">Galerie - {{ ucfirst($category) }}</h1>
        
        <!-- Filtres -->
        <div class="flex justify-center mb-10">
            <div class="bg-white rounded-full p-1 shadow-lg">
                <a href="{{ route('gallery.index') }}" class="filter-btn px-6 py-2 rounded-full font-bold text-sm uppercase hover:bg-gray-100" data-filter="all">Toutes</a>
                @foreach($categories as $cat)
                <a href="{{ route('gallery.category', $cat) }}" class="filter-btn px-6 py-2 rounded-full font-bold text-sm uppercase {{ $cat === $category ? 'bg-blue-600 text-white' : 'hover:bg-gray-100' }}" data-filter="{{ $cat }}">
                    {{ ucfirst($cat) }}
                </a>
                @endforeach
            </div>
        </div>
        
        <!-- Galerie -->
        @if($images->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                @foreach($images as $image)
                <div class="gallery-item relative group overflow-hidden rounded-xl shadow-lg hover:shadow-xl transition">
                    <a href="{{ route('gallery.show', $image->id) }}" class="block">
                        <img src="{{ asset($image->image) }}" 
                             alt="{{ $image->title }}" 
                             class="w-full h-48 object-cover group-hover:scale-110 transition duration-300">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition duration-300">
                            <div class="absolute bottom-0 left-0 right-0 p-3 text-white">
                                <p class="text-sm font-semibold truncate">{{ $image->title }}</p>
                            </div>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
            
            <div class="mt-12">
                {{ $images->links() }}
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-500 text-lg">Aucune photo dans la catégorie "{{ ucfirst($category) }}" pour le moment</p>
            </div>
        @endif
    </div>
</section>

@push('styles')
<style>
.gallery-item {
    aspect-ratio: 1;
}
</style>
@endpush
@endsection