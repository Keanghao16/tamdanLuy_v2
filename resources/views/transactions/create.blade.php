@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Add Transaction</h1>
    <p class="text-gray-500 text-sm mt-1">Record a new income or expense.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 max-w-2xl">
    <form action="{{ route('transactions.store') }}" method="POST" class="p-6 md:p-8 space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
             <!-- Amount -->
             <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Amount</label>
                <div class="relative">
                    <input type="number" step="0.01" min="0.01" name="amount" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 border p-2.5 outline-none placeholder-gray-400" placeholder="0.00">
                </div>
                 <p class="text-xs text-gray-500 mt-1">Currency is tied to the selected account.</p>
            </div>

            <!-- Account -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Account</label>
                <select name="account_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 border p-2.5 outline-none bg-gray-50 text-gray-500" readonly>
                    @foreach($accounts as $account)
                        @if($activeAccountId == $account->id)
                            <option value="{{ $account->id }}" selected>{{ $account->name }} ({{ $account->currency }})</option>
                        @endif
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">To change account, use the dropdown in the navigation bar.</p>
            </div>

            <!-- Category -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                <select name="category_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 border p-2.5 outline-none">
                    <option value="" disabled selected>Select a category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }} ({{ ucfirst($category->type) }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Date & Time -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date & Time</label>
                <!-- Formatting current date for HTML input type datetime-local using browser local time -->
                <input type="datetime-local" id="transaction_date" name="transaction_date" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 border p-2.5 outline-none">
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        const now = new Date();
                        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                        document.getElementById('transaction_date').value = now.toISOString().slice(0, 16);
                    });
                </script>
            </div>
            
            <!-- Note -->
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Note (Optional)</label>
                <input type="text" name="note" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 border p-2.5 outline-none" placeholder="What was this for?">
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 flex justify-end gap-3 mt-8">
            <a href="{{ route('transactions.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary rounded-lg shadow-sm hover:bg-emerald-600 transition">Save Transaction</button>
        </div>
    </form>
</div>
@endsection