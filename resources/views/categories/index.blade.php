@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
    <a href="{{ route('categories.create') }}" class="w-full sm:w-auto text-center bg-primary hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-sm shadow-emerald-200 transition flex items-center justify-center gap-2">
        <i class="fas fa-plus"></i> New Category
    </a>
</div>

<div class="space-y-8 pb-20"> <!-- pb-20 for space in case there is a bottom bar, but we don't have one -->
    @forelse($groupedCategories as $groupName => $categories)
        <div>
            <h2 class="text-lg font-medium text-gray-800 mb-3 ml-2">{{ $groupName }}</h2>
            
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                @foreach($categories as $category)
                    <div class="flex items-center justify-between p-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }} hover:bg-gray-50 transition cursor-pointer">
                        <div class="flex items-center">
                            <!-- Category Icon -->
                            @php
                                // color might be like "#f43f5e" or a tailwind class like "bg-pink-500". 
                                // Since we seeded hex codes, we can use style="background-color: {{ $category->color }}"
                                $isHex = str_starts_with($category->color, '#');
                            @endphp
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white {{ $isHex ? '' : ($category->color ?? 'bg-gray-400') }}"
                                 style="{{ $isHex ? 'background-color: ' . $category->color : '' }}">
                                <i class="{{ $category->icon ?? 'fa-solid fa-tag' }} text-lg"></i>
                            </div>
                            
                            <!-- Category Name -->
                            <span class="ml-4 text-[16px] text-gray-800 font-medium">{{ $category->name }}</span>
                        </div>
                        
                        <!-- Action Buttons / Chevron -->
                        <div class="flex items-center gap-3">
                            @if(!$category->is_default)
                                <a href="{{ route('categories.edit', $category) }}" class="text-gray-400 hover:text-blue-500 transition-colors p-2" title="Edit Custom Category">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                            @endif
                            <div class="text-gray-300">
                                <i class="fa-solid fa-chevron-right text-sm"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
            <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-tags"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No categories set</h3>
            <p class="text-gray-500 mb-4">Start organizing by creating a category.</p>
        </div>
    @endforelse
</div>
@endsection