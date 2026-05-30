@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Add Account</h1>
    <p class="text-gray-500 text-sm mt-1">Create a new checking, savings, or credit account.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 max-w-2xl" x-data="{
    selectedIcon: 'fa-solid fa-wallet',
    selectedColor: '#3b82f6',
    icons: [
        'fa-solid fa-wallet', 'fa-solid fa-building-columns', 'fa-solid fa-money-check-dollar',
        'fa-brands fa-cc-visa', 'fa-brands fa-cc-mastercard', 'fa-solid fa-credit-card',
        'fa-solid fa-vault', 'fa-solid fa-piggy-bank', 'fa-solid fa-coins', 'fa-solid fa-money-bill-wave'
    ],
    colors: [
        '#ef4444', '#f97316', '#f59e0b', '#eab308', '#84cc16', 
        '#22c55e', '#10b981', '#14b8a6', '#06b6d4', '#0ea5e9',
        '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7', '#d946ef',
        '#ec4899', '#f43f5e', '#f472b6', '#fb7185', '#64748b'
    ]
}">
    <form action="{{ route('accounts.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Name -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Account Name</label>
                <input type="text" name="name" required class="w-full rounded-lg border border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2.5 outline-none placeholder-gray-400" placeholder="e.g. Chase Checking">
            </div>

            <!-- Currency -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Currency</label>
                <select name="currency" required class="w-full rounded-lg border border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2.5 outline-none bg-white">
                    <option value="USD">USD ($)</option>
                    <option value="KHR">KHR (៛)</option>
                </select>
            </div>

            <!-- Initial Balance -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Initial Balance</label>
                <input type="number" step="0.01" name="current_balance" value="0.00" required class="w-full rounded-lg border border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 p-2.5 outline-none placeholder-gray-400">
            </div>

            <!-- Icon -->
            <div class="md:col-span-2">
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
            <div class="md:col-span-2">
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

        <div class="pt-4 border-t border-gray-100 flex justify-end gap-3 mt-8">
            <a href="{{ route('accounts.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg shadow-sm hover:bg-emerald-600 transition">Create Account</button>
        </div>
    </form>
</div>
@endsection