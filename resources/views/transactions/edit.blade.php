<!-- Transactions edit view -->
@extends('layouts.app')

@section('content')
<!-- Flatpickr CSS/JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="max-w-md mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-4">
    <div class="px-6 py-5 border-b border-gray-100">
        <h2 class="text-xl font-black text-gray-900 tracking-tight">Edit Transaction</h2>
    </div>

    <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" class="p-6 space-y-5">
        @csrf
        @method('PUT')

        <!-- Amount -->
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Amount</label>
            <input type="number" step="0.01" name="amount" value="{{ $transaction->amount }}" required
                class="w-full text-lg font-bold border border-gray-200 rounded-xl py-3 px-4 outline-none focus:ring-2 focus:ring-primary/20">
        </div>

        <!-- Styled Account Selection -->
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Account</label>
            
            <!-- Using Alpine.js to manage a custom dropdown state -->
            <div x-data="{ open: false, selectedAccount: '{{ $transaction->account->name }}' }" class="relative">
                <button type="button" @click="open = !open" 
                    class="w-full flex justify-between items-center text-sm border border-gray-200 rounded-xl py-3 px-4 bg-white shadow-3xs text-gray-700">
                    <span x-text="selectedAccount"></span>
                    <i class="fa-solid fa-chevron-down text-gray-400 text-xs"></i>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false" 
                    class="absolute z-10 w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-lg py-1">
                    @foreach($accounts as $account)
                        <div @click="selectedAccount = '{{ $account->name }}'; document.getElementById('account_id').value = {{ $account->id }}; open = false"
                            class="px-4 py-3 text-sm hover:bg-gray-50 cursor-pointer">
                            {{ $account->name }} ({{ $account->currency }})
                        </div>
                    @endforeach
                </div>
                
                <!-- Hidden input to submit the actual value -->
                <input type="hidden" name="account_id" id="account_id" value="{{ $transaction->account_id }}">
            </div>
        </div>

        <!-- Category Picker Link -->
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Category</label>
            <!-- Navigates to picker, passing current transaction ID to return later -->
            <a href="{{ route('categories.picker', ['from' => 'edit', 'id' => $transaction->id]) }}" 
               class="w-full flex justify-between items-center text-sm border border-gray-200 rounded-xl py-3 px-4 bg-white shadow-3xs text-gray-700">
               <span>{{ request('category_name') ?? $transaction->category->name }}</span>
               <i class="fa-solid fa-chevron-right text-gray-400 text-xs"></i>
            </a>
            <input type="hidden" name="category_id" value="{{ request('category_id') ?? $transaction->category_id }}" required>
        </div>

        <!-- Date & Time -->
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Date & Time</label>
            <input type="text" name="transaction_date" value="{{ $transaction->transaction_date->format('m/d/Y h:i A') }}" required
                x-data x-init="flatpickr($el, { enableTime: true, dateFormat: 'm/d/Y h:i K', disableMobile: true })"
                class="w-full text-sm border border-gray-200 rounded-xl py-3 px-4 bg-white cursor-pointer">
        </div>

        <!-- Note -->
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Note (Optional)</label>
            <input type="text" name="note" value="{{ $transaction->note }}"
                class="w-full text-sm border border-gray-200 rounded-xl py-3 px-4 outline-none">
        </div>

        <!-- Actions -->
        <div class="pt-4 space-y-3">
            <button type="submit" class="w-full bg-primary text-white font-bold py-3.5 rounded-xl shadow-md">Update Transaction</button>
            <a href="{{ route('transactions.index') }}" class="w-full block text-center bg-white border border-gray-200 text-gray-600 font-bold py-3.5 rounded-xl">Cancel</a>
        </div>
    </form>
</div>
@endsection