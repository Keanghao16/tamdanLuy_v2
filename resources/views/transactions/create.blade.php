<!-- Trasactions create view -->

@extends('layouts.app')

@section('content')
<!-- Add Flatpickr CSS & JS for the custom Date/Time picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="max-w-md mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-4">
    <div class="px-6 py-5 border-b border-gray-100">
        <h2 class="text-xl font-black text-gray-900 tracking-tight">Add Transaction</h2>
        <p class="text-xs text-gray-500 mt-1">Record a new cash flow change variant entry item.</p>
    </div>

    <form action="{{ route('transactions.store') }}" method="POST" class="p-6 space-y-5">
        @csrf

        <!-- Amount -->
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Amount</label>
            <div class="relative">
                <input type="number" step="0.01" name="amount" placeholder="0.00" required
                    class="w-full text-lg font-bold border border-gray-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all shadow-3xs placeholder-gray-300">
            </div>
            <p class="text-[10px] text-gray-400 mt-1.5 flex items-start gap-1">
                <i class="fas fa-info-circle mt-0.5"></i> 
                <span>Currency type is dictated by the scope of the target active account selection loop.</span>
            </p>
        </div>

        <!-- Custom Alpine.js Active Account Dropdown -->
        <div x-data="{ 
            open: false, 
            selectedId: '{{ $activeAccountId ?? '' }}', 
            selectedLabel: '{{ $activeAccountName ?? 'Select Account' }}' 
        }" class="relative">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Active Account</label>
            <input type="hidden" name="account_id" :value="selectedId">
            
            <button type="button" @click="open = !open" @click.away="open = false" 
                class="w-full flex justify-between items-center text-sm border border-gray-200 rounded-xl py-3 px-4 bg-white focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all shadow-3xs">
                <span x-text="selectedLabel" :class="selectedId === '' ? 'text-gray-400' : 'text-gray-700 font-medium'"></span>
                <i class="fa-solid fa-chevron-down text-gray-400 text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
            </button>

            <!-- Custom Dropdown Menu -->
            <div x-show="open" x-cloak
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute z-50 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-lg max-h-48 overflow-y-auto custom-scrollbar">
                
                @foreach($globalAccounts as $account)
                    <div @click="selectedId = '{{ $account->id }}'; selectedLabel = '{{ $account->name }} ({{ $account->currency }})'; open = false" 
                         class="px-4 py-3 text-sm hover:bg-emerald-50 cursor-pointer border-b border-gray-50 last:border-b-0 transition-colors flex items-center justify-between"
                         :class="selectedId === '{{ $account->id }}' ? 'bg-emerald-50/50 text-primary font-bold' : 'text-gray-600'">
                        <span>{{ $account->name }}</span>
                        <span class="text-[10px] text-gray-400">{{ $account->currency }}</span>
                    </div>
                @endforeach
            </div>
            <p class="text-[10px] text-gray-400 mt-1.5">Change context via the standard top header control bar menu.</p>
        </div>

        <!-- Custom Alpine.js Category Dropdown -->
        <!-- Replace the old Category dropdown with this -->
<div>
    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Category</label>
    <a href="{{ route('categories.picker') }}" 
       class="w-full flex justify-between items-center text-sm border border-gray-200 rounded-xl py-3 px-4 bg-white shadow-3xs text-gray-700">
       <span>{{ request('category_name') ?? 'Select a category' }}</span>
       <i class="fa-solid fa-chevron-right text-gray-400 text-xs"></i>
    </a>
    <input type="hidden" name="category_id" value="{{ request('category_id') }}" required>
</div>

        <!-- Flatpickr Date & Time Input -->    
        <div class="relative z-30">
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Date & Time</label>
            <div class="relative">
                <input type="text" name="transaction_date" required placeholder="Select date and time"
                    x-data 
                    x-init="flatpickr($el, { 
                        enableTime: true, 
                        dateFormat: 'm/d/Y h:i K', 
                        defaultDate: new Date(),
                        disableMobile: true /* Forces the custom UI instead of native mobile pickers */
                    })"
                    class="w-full text-sm border border-gray-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all shadow-3xs bg-white text-gray-700 cursor-pointer">
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-400">
                    <i class="fa-regular fa-calendar"></i>
                </div>
            </div>
        </div>

        <!-- Note -->
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Note (Optional)</label>
            <input type="text" name="note" placeholder="What was this transaction variant for?"
                class="w-full text-sm border border-gray-200 rounded-xl py-3 px-4 focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all shadow-3xs placeholder-gray-300">
        </div>

        <!-- Actions -->
        <div class="pt-4 space-y-3">
            <button type="submit" class="w-full bg-primary hover:bg-emerald-600 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-emerald-200 transition-colors active:scale-[0.98]">
                Save Transaction
            </button>
            <a href="{{ route('dashboard') }}" class="w-full flex justify-center bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 font-bold py-3.5 px-4 rounded-xl shadow-3xs transition-colors active:scale-[0.98]">
                Cancel
            </a>
        </div>
    </form>
</div>

<!-- Custom Scrollbar Styling for Dropdowns -->
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f9fafb; 
        border-radius: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e5e7eb; 
        border-radius: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #d1d5db; 
    }
</style>
@endsection