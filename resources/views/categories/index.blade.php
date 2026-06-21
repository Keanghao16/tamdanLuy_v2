<!-- Categories index view -->

@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
        <p class="text-sm text-gray-500 mt-1">Manage and organize asset flows into descriptive system pools.</p>
    </div>
    <a href="{{ route('categories.create') }}" class="w-full sm:w-auto text-center bg-primary hover:bg-emerald-600 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-sm shadow-emerald-200 transition flex items-center justify-center gap-2">
        <i class="fas fa-plus text-xs"></i> New Category
    </a>
</div>

<div class="space-y-6 pb-20">
    @forelse($groupedCategories as $groupName => $categories)
        <div>
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2.5 ml-2">{{ $groupName }}</h2>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden divide-y divide-gray-100">
                @foreach($categories as $category)
                    <div class="flex items-center justify-between p-4 hover:bg-gray-50/50 transition group">
                        <div class="flex items-center min-w-0">
                            <!-- Category Icon -->
                            @php
                                $isHex = str_starts_with($category->color, '#');
                            @endphp
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0 shadow-2xs {{ $isHex ? '' : ($category->color ?? 'bg-gray-400') }}"
                                 style="{{ $isHex ? 'background-color: ' . $category->color : '' }}">
                                <i class="{{ $category->icon ?? 'fa-solid fa-tag' }} text-sm"></i>
                            </div>
                            
                            <!-- Category Name & Type Tag -->
                            <div class="ml-4 min-w-0">
                                <span class="text-sm text-gray-800 font-bold block truncate group-hover:text-primary transition">{{ $category->name }}</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider mt-0.5 inline-block {{ $category->type === 'income' ? 'text-green-600' : ($category->type === 'saving' ? 'text-blue-600' : 'text-red-500') }}">
                                    {{ $category->type }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Action Buttons / Chevron -->
                        <div class="flex items-center gap-2 shrink-0">
                            @if(!$category->is_default)
                                <a href="{{ route('categories.edit', $category) }}" class="text-gray-400 hover:text-primary transition-colors p-2" title="Edit Custom Category">
                                    <i class="fa-regular fa-pen-to-square text-xs"></i>
                                </a>
                            @endif
                            <div class="text-gray-300 p-2">
                                <i class="fa-solid fa-chevron-right text-[11px] transition-transform group-hover:translate-x-0.5"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center text-gray-500">
            <div class="w-14 h-14 bg-gray-50 border border-gray-100 text-gray-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i class="fas fa-tags text-lg"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-800 mb-1">No categories set</h3>
            <p class="text-gray-400 text-xs max-w-xs mx-auto">Start organizing your system flows by setting up a custom category track.</p>
        </div>
    @endforelse
</div>
@endsection