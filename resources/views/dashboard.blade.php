@extends('layouts.app')

@section('content')
<div class="mb-8 flex justify-between items-center">
    <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
    <div class="text-sm text-gray-500 font-medium bg-white px-4 py-2 rounded-lg shadow-sm border">
        {{ now()->format('F Y') }}
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-wallet text-6xl text-blue-500"></i></div>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2 z-10">Total Balance</h3>
        <p class="text-4xl font-bold text-gray-900 z-10">${{ number_format($totalBalance, 2) }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-arrow-down text-6xl text-green-500"></i></div>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2 z-10">Income This Month</h3>
        <p class="text-4xl font-bold text-green-500 z-10">+${{ number_format($thisMonthIncome, 2) }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col items-center justify-center relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-arrow-up text-6xl text-red-500"></i></div>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2 z-10">Expenses This Month</h3>
        <p class="text-4xl font-bold text-red-500 z-10">-${{ number_format($thisMonthExpense, 2) }}</p>
    </div>
</div>

<!-- Latest Transactions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Latest Transactions</h3>
        <a href="{{ route('transactions.index') }}" class="text-sm text-primary hover:underline font-medium">View All <i class="fas fa-arrow-right text-xs ml-1"></i></a>
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($latestTransactions as $tx)
        <div class="p-6 hover:bg-gray-50 transition flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center {{ $tx->type === 'income' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                    <i class="fas {{ $tx->type === 'income' ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-800">{{ $tx->category->name ?? 'Uncategorized' }}</h4>
                    <p class="text-sm text-gray-500">{{ $tx->transaction_date->format('M d, Y') }} &bull; {{ $tx->account->name ?? 'Deleted Account' }}</p>
                    @if($tx->note)
                        <p class="text-xs text-gray-400 mt-1 italic"><i class="fas fa-quote-left mr-1"></i>{{ $tx->note }}</p>
                    @endif
                </div>
            </div>
            <div class="text-lg font-bold {{ $tx->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                {{ $tx->type === 'income' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
            </div>
        </div>
        @empty
        <div class="p-10 text-center text-gray-500">
            <i class="fas fa-receipt text-4xl mb-3 opacity-20"></i>
            <p>No recent transactions found.</p>
            <a href="{{ route('transactions.create') }}" class="inline-block mt-4 text-primary hover:underline">Add your first transaction</a>
        </div>
        @endforelse
    </div>
</div>
@endsection