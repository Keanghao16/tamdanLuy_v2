@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Edit Category</h1>
    <p class="text-gray-500 text-sm mt-1">Update your custom category.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 max-w-2xl overflow-hidden" x-data="{
    selectedType: '{{ old('type', $category->type) }}',
    selectedIcon: '{{ old('icon', $category->icon) ?? 'fa-solid fa-tag' }}',
    selectedColor: '{{ old('color', $category->color) ?? '#0ea5e9' }}',
    icons: [
        'fa-solid fa-tag', 'fa-solid fa-utensils', 'fa-solid fa-car', 'fa-solid fa-house', 
        'fa-solid fa-cart-shopping', 'fa-solid fa-bolt', 'fa-solid fa-heart-pulse', 'fa-solid fa-graduation-cap',
        'fa-solid fa-plane', 'fa-solid fa-piggy-bank', 'fa-solid fa-money-bill', 'fa-solid fa-gift',
        'fa-solid fa-mug-hot', 'fa-solid fa-bag-shopping', 'fa-solid fa-tv', 'fa-solid fa-basketball',
        'fa-solid fa-gamepad', 'fa-solid fa-briefcase-medical', 'fa-solid fa-paw', 'fa-solid fa-child',
        'fa-solid fa-gas-pump', 'fa-solid fa-film', 'fa-solid fa-dumbbell', 'fa-solid fa-umbrella-beach',
        'fa-solid fa-wine-glass', 'fa-solid fa-bowl-food', 'fa-solid fa-chart-line', 'fa-solid fa-percent',
        'fa-solid fa-star', 'fa-solid fa-spray-can', 'fa-solid fa-capsules', 'fa-solid fa-tooth',
        'fa-solid fa-bed', 'fa-solid fa-user-doctor', 'fa-solid fa-hand-holding-heart', 'fa-solid fa-shirt',
        'fa-solid fa-plane-departure', 'fa-solid fa-file-invoice', 'fa-solid fa-taxi', 'fa-solid fa-wrench'
    ],
    colors: [
        '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16', 
        '#22c55e', '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9',
        '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7', '#d946ef',
        '#ec4899', '#f43f5e', '#f472b6', '#fb7185', '#64748b'
    ]
}">
    <form action="{{ route('categories.update', $category) }}" method="POST" class="p-6 md:p-8 space-y-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <!-- Type Selection (Radio Buttons) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Category Type</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer relative">
                        <input type="radio" name="type" value="expense" x-model="selectedType" class="peer sr-only">
                        <div class="text-center px-4 py-3 rounded-xl border border-gray-200 peer-checked:border-rose-500 peer-checked:bg-rose-50 hover:bg-gray-50 transition-all font-medium text-gray-600 peer-checked:text-rose-600">
                            Expense
                        </div>
                    </label>
                    <label class="cursor-pointer relative">
                        <input type="radio" name="type" value="income" x-model="selectedType" class="peer sr-only">
                        <div class="text-center px-4 py-3 rounded-xl border border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:bg-gray-50 transition-all font-medium text-gray-600 peer-checked:text-emerald-600">
                            Income
                        </div>
                    </label>
                    <label class="cursor-pointer relative">
                        <input type="radio" name="type" value="saving" x-model="selectedType" class="peer sr-only">
                        <div class="text-center px-4 py-3 rounded-xl border border-gray-200 peer-checked:border-amber-500 peer-checked:bg-amber-50 hover:bg-gray-50 transition-all font-medium text-gray-600 peer-checked:text-amber-600">
                            Saving
                        </div>
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Category Name</label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-xl border border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2.5 outline-none placeholder-gray-400" placeholder="e.g. Groceries">
                </div>

                <!-- Group -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Group</label>
                    <select name="group" required class="w-full rounded-xl border border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-3 outline-none bg-white">
                        <option value="Entertainment" {{ old('group', $category->group) == 'Entertainment' ? 'selected' : '' }}>Entertainment</option>
                        <option value="Food & drinks" {{ old('group', $category->group) == 'Food & drinks' ? 'selected' : '' }}>Food & drinks</option>
                        <option value="Housing" {{ old('group', $category->group) == 'Housing' ? 'selected' : '' }}>Housing</option>
                        <option value="Income" {{ old('group', $category->group) == 'Income' ? 'selected' : '' }}>Income</option>
                        <option value="Lifestyle" {{ old('group', $category->group) == 'Lifestyle' ? 'selected' : '' }}>Lifestyle</option>
                        <option value="Miscellaneous" {{ old('group', $category->group) == 'Miscellaneous' ? 'selected' : '' }}>Miscellaneous</option>
                        <option value="Savings" {{ old('group', $category->group) == 'Savings' ? 'selected' : '' }}>Savings</option>
                        <option value="Transportation" {{ old('group', $category->group) == 'Transportation' ? 'selected' : '' }}>Transportation</option>
                    </select>
                </div>
            </div>

            <!-- Icon -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Icon</label>
                <input type="hidden" name="icon" x-model="selectedIcon">
                <style>
                    .no-scrollbar::-webkit-scrollbar { display: none; }
                    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
                </style>
                <div class="flex overflow-x-auto gap-3 pb-2 snap-x no-scrollbar">
                    <template x-for="icon in icons" :key="icon">
                        <button type="button" 
                                @click="selectedIcon = icon"
                                :class="selectedIcon === icon ? 'border-primary bg-primary/10 text-primary ring-2 ring-primary/20 scale-105' : 'border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700'"
                                class="flex-none flex items-center justify-center w-12 h-12 rounded-xl border transition-all snap-start">
                            <i :class="icon" class="text-xl"></i>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Color -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-3">Color</label>
                <input type="hidden" name="color" x-model="selectedColor">
                <div class="flex overflow-x-auto gap-3 pb-2 snap-x no-scrollbar items-center">
                    <template x-for="color in colors" :key="color">
                        <button type="button" 
                                @click="selectedColor = color"
                                :style="`background-color: ${color}`"
                                :class="selectedColor === color ? 'ring-2 ring-offset-2 ring-gray-400 scale-110' : 'hover:scale-110 opacity-90 hover:opacity-100'"
                                class="flex-none w-10 h-10 rounded-full transition-all snap-start shadow-sm focus:outline-none">
                            <div x-show="selectedColor === color" class="flex h-full w-full items-center justify-center text-white text-opacity-90">
                                <i class="fa-solid fa-check text-sm block"></i>
                            </div>
                        </button>
                    </template>
                    <!-- Custom Color Picker -->
                    <div class="flex-none relative w-10 h-10 rounded-full overflow-hidden border-2 border-dashed border-gray-300 hover:border-gray-400 transition-all snap-start flex items-center justify-center bg-gray-50">
                        <input type="color" x-model="selectedColor" class="absolute inset-0 w-20 h-20 -top-5 -left-5 cursor-pointer opacity-0">
                        <i class="fa-solid fa-plus text-gray-400 text-sm pointer-events-none"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 flex justify-end gap-3 mt-8">
            <a href="{{ route('categories.index') }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-primary rounded-xl shadow-sm hover:bg-emerald-600 transition">Update Category</button>
        </div>
    </form>
</div>
@endsection
