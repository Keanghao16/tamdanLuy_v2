@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900">Transactions</h1>
    <a href="{{ route('transactions.create') }}" class="bg-primary hover:bg-emerald-600 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition">
        <i class="fas fa-plus mr-2"></i>New Transaction
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Category</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Account</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Note</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($transactions as $tx)
                @php
                    $isKHR = $tx->account->currency === 'KHR';
                    $formattedAmount = $isKHR ? number_format($tx->amount, 0) . ' ៛' : '$' . number_format($tx->amount, 2);
                    $categoryType = $tx->category->type ?? 'expense';
                @endphp
                <tr class="hover:bg-gray-50 transition group cursor-pointer" onclick="window.location='{{ route('transactions.edit', $tx) }}'">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                        {{ $tx->transaction_date->format('M d, Y h:i A') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" style="background-color: {{ $tx->category->color ?? '#3b82f6' }}20; color: {{ $tx->category->color ?? '#3b82f6' }}">
                            <i class="{{ $tx->category->icon ?? 'fas fa-tag' }} mr-1"></i> {{ $tx->category->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        <i class="{{ $tx->account->icon ?? 'fas fa-wallet' }} mr-2" style="color: {{ $tx->account->color ?? '#9ca3af' }}"></i>{{ $tx->account->name ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 italic max-w-xs truncate">
                        {{ $tx->note ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold relative {{ $categoryType === 'income' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $categoryType === 'income' ? '+' : '-' }}{{ $formattedAmount }}
                        <a href="{{ route('transactions.edit', $tx) }}" class="absolute right-2 opacity-0 group-hover:opacity-100 text-gray-400 hover:text-primary transition-opacity ml-2" onclick="event.stopPropagation()">
                            <i class="fas fa-edit"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                        No transactions recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection