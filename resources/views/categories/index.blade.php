@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
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

    <!-- Static options at the bottom matching the image (New Category / Removed Categories) -->
    <div class="pt-4">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <a href="{{ route('categories.create') }}" class="flex items-center justify-between p-4 border-b border-gray-100 hover:bg-gray-50 transition cursor-pointer">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white bg-gray-600">
                        <i class="fa-solid fa-plus text-lg"></i>
                    </div>
                    <span class="ml-4 text-[16px] text-gray-800 font-medium">New Category</span>
                </div>
                <div class="text-gray-400">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </div>
            </a>
            
            <div class="flex items-center justify-between p-4 hover:bg-gray-50 transition cursor-pointer">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white bg-gray-500">
                        <i class="fa-solid fa-trash text-lg"></i>
                    </div>
                    <span class="ml-4 text-[16px] text-gray-800 font-medium">Removed Categories</span>
                </div>
                <div class="text-gray-400">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection