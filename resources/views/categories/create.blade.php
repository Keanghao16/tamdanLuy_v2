@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('categories.index') }}" class="text-sm font-semibold text-gray-500 hover:text-primary transition flex items-center">
            Categories
        </a>
        <span class="text-gray-300 text-xs">/</span>
        <span class="text-sm font-semibold text-gray-400">Add Category</span>
    </div>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Add Category</h1>
        <p class="text-sm text-gray-500 mt-0.5">Create a new category container for your ledger transactions.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 w-full overflow-hidden" x-data="{
    selectedType: 'expense',
    selectedIcon: 'fa-solid fa-tag',
    selectedColor: '#0ea5e9',
    iconIsDown: false, iconStartX: 0, iconScrollLeft: 0, iconDragged: false,
    colorIsDown: false, colorStartX: 0, colorScrollLeft: 0, colorDragged: false,
    icons: [
        'fa-solid fa-tag', 'fa-solid fa-utensils', 'fa-solid fa-car', 'fa-solid fa-house', 
        'fa-solid fa-cart-shopping', 'fa-solid fa-bold', 'fa-solid fa-heart-pulse', 'fa-solid fa-graduation-cap',
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
    <form action="{{ route('categories.store') }}" method="POST" class="p-6 md:p-8 text-left">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Category Type</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer relative">
                            <input type="radio" name="type" value="expense" x-model="selectedType" class="peer sr-only">
                            <div class="text-center text-xs uppercase tracking-wider py-3 rounded-xl border border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 transition-all font-bold text-gray-500 peer-checked:text-red-600 shadow-2xs">
                                Expense
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" name="type" value="income" x-model="selectedType" class="peer sr-only">
                            <div class="text-center text-xs uppercase tracking-wider py-3 rounded-xl border border-gray-200 peer-checked:border-emerald-500 peer-checked:bg-green-50 hover:bg-gray-50 transition-all font-bold text-gray-500 peer-checked:text-green-600 shadow-2xs">
                                Income
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" name="type" value="saving" x-model="selectedType" class="peer sr-only">
                            <div class="text-center text-xs uppercase tracking-wider py-3 rounded-xl border border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50 transition-all font-bold text-gray-500 peer-checked:text-blue-600 shadow-2xs">
                                Saving
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Category Name</label>
                        <input type="text" name="name" required class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary p-3 outline-none shadow-sm transition-all text-gray-700 placeholder-gray-400" placeholder="e.g. Groceries">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">System Group Pool</label>
                        <select name="group" required class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary p-3 outline-none shadow-sm transition-all text-gray-700 bg-white cursor-pointer">
                            <option value="Entertainment">Entertainment</option>
                            <option value="Food & drinks">Food & drinks</option>
                            <option value="Housing">Housing</option>
                            <option value="Income">Income</option>
                            <option value="Lifestyle">Lifestyle</option>
                            <option value="Miscellaneous">Miscellaneous</option>
                            <option value="Savings">Savings</option>
                            <option value="Transportation">Transportation</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Visual Glyph Representation</label>
                    <input type="hidden" name="icon" x-model="selectedIcon">
                    <style>
                        .no-scrollbar::-webkit-scrollbar { display: none; }
                        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
                    </style>
                    
                    <div @wheel="if ($event.deltaX === 0) { $event.preventDefault(); $el.scrollLeft += $event.deltaY; }"
                         @mousedown="iconIsDown = true; iconStartX = $event.pageX - $el.offsetLeft; iconScrollLeft = $el.scrollLeft; iconDragged = false;"
                         @mouseleave="iconIsDown = false;"
                         @mouseup="iconIsDown = false;"
                         @mousemove="if(!iconIsDown) return; $event.preventDefault(); const x = $event.pageX - $el.offsetLeft; const walk = (x - iconStartX) * 1.5; $el.scrollLeft = iconScrollLeft - walk; if(Math.abs(x - iconStartX) > 6) iconDragged = true;"
                         :class="iconIsDown && iconDragged ? 'cursor-grabbing' : 'cursor-grab'"
                         class="flex overflow-x-auto gap-3 pb-2.5 snap-x no-scrollbar items-center select-none transition-colors duration-150">
                        <template x-for="icon in icons" :key="icon">
                            <button type="button" 
                                    @click="if (!iconDragged) selectedIcon = icon"
                                    :class="selectedIcon === icon ? 'border-primary bg-primary/10 text-primary ring-2 ring-primary/20 scale-105' : 'border-gray-200 text-gray-400 hover:bg-gray-50 hover:text-gray-700'"
                                    class="flex-none flex items-center justify-center w-12 h-12 rounded-xl border transition-all snap-start shadow-2xs pointer-events-auto">
                                <i :class="icon" class="text-base pointer-events-none"></i>
                            </button>
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Design Layer Color</label>
                    <input type="hidden" name="color" x-model="selectedColor">
                    
                    <div @wheel="if ($event.deltaX === 0) { $event.preventDefault(); $el.scrollLeft += $event.deltaY; }"
                         @mousedown="colorIsDown = true; colorStartX = $event.pageX - $el.offsetLeft; colorScrollLeft = $el.scrollLeft; colorDragged = false;"
                         @mouseleave="colorIsDown = false;"
                         @mouseup="colorIsDown = false;"
                         @mousemove="if(!colorIsDown) return; $event.preventDefault(); const x = $event.pageX - $el.offsetLeft; const walk = (x - colorStartX) * 1.5; $el.scrollLeft = colorScrollLeft - walk; if(Math.abs(x - colorStartX) > 6) colorDragged = true;"
                         :class="colorIsDown && colorDragged ? 'cursor-grabbing' : 'cursor-grab'"
                         class="flex overflow-x-auto gap-3 pb-2.5 snap-x no-scrollbar items-center select-none transition-colors duration-150">
                        <template x-for="color in colors" :key="color">
                            <button type="button" 
                                    @click="if (!colorDragged) selectedColor = color"
                                    :style="`background-color: ${color}`"
                                    :class="selectedColor === color ? 'ring-2 ring-offset-2 ring-gray-400 scale-110' : 'hover:scale-110 opacity-95'"
                                    class="flex-none w-10 h-10 rounded-xl transition-all snap-start shadow-xs focus:outline-none pointer-events-auto">
                                <div x-show="selectedColor === color" class="flex h-full w-full items-center justify-center text-white text-opacity-95 pointer-events-none">
                                    <i class="fa-solid fa-check text-xs block"></i>
                                </div>
                            </button>
                        </template>
                        <div @click="if (colorDragged) $event.preventDefault()" 
                             class="flex-none relative w-10 h-10 rounded-xl overflow-hidden border-2 border-dashed border-gray-300 hover:border-gray-400 transition-all snap-start flex items-center justify-center bg-gray-50/50">
                            <input type="color" x-model="selectedColor" class="absolute inset-0 w-20 h-20 -top-5 -left-5 cursor-pointer opacity-0">
                            <i class="fa-solid fa-plus text-gray-400 text-xs pointer-events-none"></i>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

        <div class="pt-5 border-t border-gray-100 flex justify-end gap-3 mt-8">
            <a href="{{ route('categories.index') }}" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl shadow-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="bg-primary hover:bg-emerald-600 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-sm shadow-emerald-200 transition inline-flex items-center gap-2">
                <i class="fas fa-check text-xs"></i> Create Category
            </button>
        </div>
    </form>
</div>
@endsection