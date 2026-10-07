@extends('layouts.app')

@section('title', $category->name)

@section('content')
<section class="py-20 px-6 lg:px-24">
    <div class="max-w-6xl mx-auto">
        <div class="breadcrumb flex items-center gap-2 mb-6 text-sm">
            <a href="{{ route('home') }}" class="text-blue-600 hover:underline">Accueil</a>
            <span>/</span>
            <a href="{{ route('news.index') }}" class="text-blue-600 hover:underline">Actualités</a>
            <span>/</span>
            <span>{{ $category->name }}</span>
        </div>
        
        <h1 class="text-5xl font-hand mb-12 text-center brush-underline">{{ $category->name }}</h1>
        
        <div class="grid md:grid-cols-3 gap-8">
            @foreach($news as $item)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-2">
                <div class="relative">
                    @if($item->image)
                    <img src="{{ asset($item->image) }}" class="w-full h-48 object-cover" alt="{{ $item->title }}">
                    @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-volleyball-ball text-4xl text-gray-400"></i>
                    </div>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-3">{{ $item->title }}</h3>
                    <p class="text-gray-600 mb-4">{{ Str::limit($item->excerpt, 100) }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-500">{{ $item->created_at->format('d/m/Y') }}</span>
                        <a href="{{ route('news.show', $item->slug) }}" class="text-red-500 hover:text-blue-600 font-hand flex items-center gap-2">
                            Lire la suite <i class="fas fa-chevron-right text-xs"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-12">
            {{ $news->links() }}
        </div>
    </div>
</section>
@endsection