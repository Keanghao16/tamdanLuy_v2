@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto p-4" x-data="{ tab: 'expense' }">
    <div class="flex items-center mb-6">
        <a href="{{ route('transactions.create') }}" class="mr-4"><i class="fa-solid fa-arrow-left"></i></a>
        
        <h2 class="text-lg font-bold">Categories</h2>
    </div>

    <!-- Tabs -->
    <div class="flex bg-gray-100 p-1 rounded-full mb-6">
        <button @click="tab = 'expense'" :class="tab === 'expense' ? 'bg-white shadow-sm text-primary' : 'text-gray-500'" class="flex-1 py-2 rounded-full text-sm font-bold transition-all">EXPENSES</button>
        <button @click="tab = 'income'" :class="tab === 'income' ? 'bg-white shadow-sm text-primary' : 'text-gray-500'" class="flex-1 py-2 rounded-full text-sm font-bold transition-all">INCOME</button>
        <button @click="tab = 'saving'" :class="tab === 'saving' ? 'bg-white shadow-sm text-primary' : 'text-gray-500'" class="flex-1 py-2 rounded-full text-sm font-bold transition-all">SAVINGS</button>
    </div>

    <!-- Category Groups -->
    <div x-show="['expense', 'income', 'saving'].includes(tab)">
        <template x-for="t in ['expense', 'income', 'saving']">
            <div x-show="tab === t">
                @foreach(['expense', 'income', 'saving'] as $type)
                    <div x-show="tab === '{{ $type }}'">
                        @foreach($categories->get($type, collect())->groupBy('group') as $groupName => $groupCategories)
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 mt-6">{{ $groupName }}</h3>
                            <div class="grid grid-cols-4 gap-6">
                                @foreach($groupCategories as $category)
                                    <a href="{{ route(request('from') === 'edit' ? 'transactions.edit' : 'transactions.create', 
                                        array_merge(
                                            ['category_id' => $category->id, 'category_name' => $category->name], 
                                            request('from') === 'edit' ? ['transaction' => request('id')] : []
                                        )) }}" 
                                    class="flex flex-col items-center space-y-2">
                                    
                                        <!-- Icon and Name content remains here -->
                                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white" 
                                            style="background-color: {{ $category->color }}">
                                            <i class="fa-solid {{ $category->icon }}"></i>
                                        </div>
                                        <span class="text-[10px] text-gray-600 text-center">{{ $category->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </template>
    </div>
</div>
@endsection