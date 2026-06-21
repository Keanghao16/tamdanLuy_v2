<!-- Transactions index view -->

@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Transactions</h1>
        <p class="text-sm text-gray-500 mt-1">Review and manage historical cash flow adjustments.</p>
    </div>
    <a href="{{ route('transactions.create') }}" class="w-full sm:w-auto text-center bg-primary text-white text-sm font-bold py-2.5 px-4 rounded-xl hover:bg-emerald-600 transition shadow-sm shadow-emerald-100 flex items-center justify-center gap-2">
        <i class="fas fa-plus"></i> New Transaction
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <!-- Desktop Responsive Table View (Hidden on mobile) -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50/70">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Account</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Note</th>
                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($transactions as $tx)
                @php
                    $isKHR = ($tx->account->currency ?? 'USD') === 'KHR';
                    $formattedAmount = $isKHR ? number_format($tx->amount, 0) . ' ៛' : '$' . number_format($tx->amount, 2);
                    $categoryType = $tx->category->type ?? 'expense';
                @endphp
                <tr class="hover:bg-gray-50/60 transition group cursor-pointer" onclick="window.location='{{ route('transactions.edit', $tx) }}'">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                        {{ $tx->transaction_date->format('M d, Y • h:i A') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold" style="background-color: {{ $tx->category->color ?? '#3b82f6' }}15; color: {{ $tx->category->color ?? '#3b82f6' }}">
                            <i class="{{ $tx->category->icon ?? 'fas fa-tag' }} text-[11px]"></i> {{ $tx->category->name ?? 'Uncategorized' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                        <i class="fas fa-wallet mr-1.5 text-gray-400 text-xs"></i>{{ $tx->account->name ?? 'Deleted Account' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-400 italic max-w-xs truncate">
                        {{ $tx->note ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900 pr-12 relative">
                        <span class="{{ $categoryType === 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $categoryType === 'income' ? '+' : '-' }}{{ $formattedAmount }}
                        </span>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 text-gray-400 transition-opacity">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-receipt text-3xl mb-3 opacity-20 block"></i>
                        No transactions recorded yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Stacked Card View (Matches Dashboard Filtered Rows) -->
    <div class="block md:hidden divide-y divide-gray-100">
        @forelse($transactions as $tx)
        @php
            $isKHR = ($tx->account->currency ?? 'USD') === 'KHR';
            $formattedAmount = $isKHR ? number_format($tx->amount, 0) . ' ៛' : '$' . number_format($tx->amount, 2);
            $categoryType = $tx->category->type ?? 'expense';
        @endphp
        <div class="p-4 flex items-center justify-between hover:bg-gray-50/60 active:bg-gray-50 transition cursor-pointer" onclick="window.location='{{ route('transactions.edit', $tx) }}'">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-sm shadow-2xs shrink-0" style="background-color: {{ $tx->category->color ?? '#9ca3af' }}">
                    <i class="{{ $tx->category->icon ?? 'fas fa-tag' }}"></i>
                </div>
                <div class="min-w-0">
                    <h4 class="font-bold text-gray-800 text-sm truncate">{{ $tx->category->name ?? 'Uncategorized' }}</h4>
                    <p class="text-xs text-gray-400 mt-0.5 truncate">
                        {{ $tx->transaction_date->format('M d • h:i A') }} &bull; {{ $tx->account->name ?? 'Account' }}
                    </p>
                    @if($tx->note)
                        <p class="text-[11px] text-gray-400 italic mt-0.5 truncate"><i class="fas fa-quote-left mr-1 text-[9px] opacity-50"></i>{{ $tx->note }}</p>
                    @endif
                </div>
            </div>
            <div class="text-right shrink-0 pl-2">
                <span class="text-sm font-bold tracking-tight {{ $categoryType === 'income' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $categoryType === 'income' ? '+' : '-' }}{{ $formattedAmount }}
                </span>
                <i class="fas fa-chevron-right text-[10px] text-gray-300 ml-1.5"></i>
            </div>
        </div>
        @empty
        <div class="p-12 text-center text-gray-400">
            <i class="fas fa-receipt text-3xl mb-3 opacity-20 block"></i>
            No transactions recorded yet.
        </div>
        @endforelse
    </div>
</div>
@endsection