@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Edit Transaction</h1>
        <p class="text-gray-500 text-sm mt-1">Update record details.</p>
    </div>

    <form action="{{ route('transactions.destroy', $transaction) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transaction?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg font-medium shadow-sm transition flex items-center">
            <i class="fas fa-trash-alt mr-2"></i>Delete
        </button>
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 max-w-2xl">
    <form action="{{ route('transactions.update', $transaction) }}" method="POST" class="p-6 md:p-8 space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             <!-- Amount -->
             <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Amount</label>
                <div class="relative">
                    <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $transaction->amount) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 border p-2.5 outline-none placeholder-gray-400" placeholder="0.00">
                </div>
                 <p class="text-xs text-gray-500 mt-1">Currency is tied to the selected account.</p>
            </div>

            <!-- Account -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Account</label>
                <select name="account_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 border p-2.5 outline-none">
                    <option value="" disabled>Select an account</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" {{ old('account_id', $transaction->account_id) == $account->id ? 'selected' : '' }}>{{ $account->name }} ({{ $account->currency }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                <select name="category_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 border p-2.5 outline-none">
                    <option value="" disabled>Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }} ({{ ucfirst($category->type) }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Date & Time -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date & Time</label>
                <!-- Formatting existing date for HTML input type datetime-local (e.g. 2026-05-24T12:00) -->
                <input type="datetime-local" name="transaction_date" required value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d\TH:i')) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 border p-2.5 outline-none">
            </div>
            
            <!-- Note -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Note (Optional)</label>
                <input type="text" name="note" value="{{ old('note', $transaction->note) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 border p-2.5 outline-none" placeholder="What was this for?">
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end gap-3 mt-8">
            <a href="{{ route('transactions.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg shadow-sm hover:bg-emerald-600 transition">Save Changes</button>
        </div>
    </form>
</div>
@endsection