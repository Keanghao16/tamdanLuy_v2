@extends('layouts.app')

@section('content')
@php
    $currency = $activeAccount->currency ?? 'USD';
    $isKHR = $currency === 'KHR';
    
    $formatAmount = function($amount) use ($isKHR) {
        return $isKHR ? number_format($amount, 0) . ' ៛' : '$' . number_format($amount, 2);
    };

    $prevMonth = \Carbon\Carbon::createFromFormat('Y-m', $currentMonth)->subMonth()->format('Y-m');
    $nextMonth = \Carbon\Carbon::createFromFormat('Y-m', $currentMonth)->addMonth()->format('Y-m');

    $prevStart = isset($startDate) ? \Carbon\Carbon::parse($startDate)->subMonth()->format('Y-m-d') : null;
    $prevEnd = isset($endDate) ? \Carbon\Carbon::parse($endDate)->subMonth()->format('Y-m-d') : null;
    
    $nextStart = isset($startDate) ? \Carbon\Carbon::parse($startDate)->addMonth()->format('Y-m-d') : null;
    $nextEnd = isset($endDate) ? \Carbon\Carbon::parse($endDate)->addMonth()->format('Y-m-d') : null;
@endphp

<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    
    <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-end">
        @if($dateMode === 'custom')
            <a href="?mode=custom&start_date={{ $prevStart }}&end_date={{ $prevEnd }}" class="text-gray-400 hover:text-gray-600 bg-white p-2 rounded-lg shadow-sm border transition flex items-center justify-center w-10 h-10 sm:w-9 sm:h-9 shrink-0"><i class="fas fa-chevron-left text-sm"></i></a>
        @else
            <a href="?mode=month&month={{ $prevMonth }}" class="text-gray-400 hover:text-gray-600 bg-white p-2 rounded-lg shadow-sm border transition flex items-center justify-center w-10 h-10 sm:w-9 sm:h-9 shrink-0"><i class="fas fa-chevron-left text-sm"></i></a>
        @endif
        
        <div class="relative flex-1 sm:flex-none" x-data="{ open: false, mode: '{{ $dateMode }}' }">
            <button @click="open = !open" class="w-full justify-between sm:justify-center text-sm text-gray-700 font-medium bg-white px-4 py-2 h-10 sm:h-9 rounded-lg shadow-sm border flex items-center gap-3 hover:bg-gray-50 transition">
                <div class="flex items-center gap-2 flex-1 justify-center">
                    <i class="far fa-calendar-alt text-gray-400"></i>
                    <span class="min-w-[100px] text-center">
                        @if($dateMode === 'custom')
                            {{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('Y') }}
                        @else
                            {{ \Carbon\Carbon::createFromFormat('Y-m', $currentMonth)->format('F Y') }}
                        @endif
                    </span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
            </button>
            
            <!-- Filter Dropdown -->
            <div x-show="open" 
                 x-transition:enter="transition ease-out duration-300 origin-top"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200 origin-top"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
                 @click.away="open = false" 
                 x-cloak 
                 class="absolute top-full left-1/2 -translate-x-1/2 sm:left-auto sm:translate-x-0 sm:right-0 mt-3 w-[calc(100vw-2rem)] max-w-sm sm:w-80 bg-white rounded-2xl shadow-xl border border-gray-100 text-gray-800 p-6 z-[60]">
                
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-gray-900">Filter Period</h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 w-8 h-8 rounded-full flex items-center justify-center transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="" method="GET" class="text-left">
                    <input type="hidden" name="mode" :value="mode">
                    
                    <!-- Segmented Control Mode Switcher -->
                    <div class="flex bg-gray-100 p-1 rounded-xl mb-6">
                        <button type="button" @click="mode = 'month'" :class="mode === 'month' ? 'bg-white shadow-sm text-primary font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" class="flex-1 py-2 text-xs uppercase tracking-wider rounded-lg transition-all duration-200">
                            By Month
                        </button>
                        <button type="button" @click="mode = 'custom'" :class="mode === 'custom' ? 'bg-white shadow-sm text-primary font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" class="flex-1 py-2 text-xs uppercase tracking-wider rounded-lg transition-all duration-200">
                            Custom Range
                        </button>
                    </div>
                    
                    <!-- Month Input block -->
                    <div x-show="mode === 'month'" x-collapse.duration.300ms>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Select Month</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="far fa-calendar text-gray-400"></i>
                            </div>
                            <input type="month" name="month" value="{{ $currentMonth }}" class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700 cursor-pointer hover:border-gray-300">
                        </div>
                    </div>
                    
                    <!-- Custom Range Inputs block -->
                    <div x-show="mode === 'custom'" x-collapse.duration.300ms style="display: none;">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Start Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <i class="far fa-calendar-alt text-gray-400"></i>
                                    </div>
                                    <input type="date" name="start_date" value="{{ $startDate ?? now()->startOfMonth()->format('Y-m-d') }}" class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700 cursor-pointer hover:border-gray-300">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">End Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <i class="far fa-calendar-check text-gray-400"></i>
                                    </div>
                                    <input type="date" name="end_date" value="{{ $endDate ?? now()->endOfMonth()->format('Y-m-d') }}" class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700 cursor-pointer hover:border-gray-300">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 mt-8 pt-4 border-t border-gray-50">
                        <button type="button" @click="open = false" class="flex-1 px-4 py-2.5 bg-white text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-colors border border-gray-200 shadow-sm">
                            Cancel
                        </button>
                        <button type="submit" class="flex-1 bg-primary text-white text-sm font-bold py-2.5 px-4 rounded-xl hover:bg-emerald-600 transition-colors shadow-sm shadow-emerald-200 flex items-center justify-center gap-2">
                            <i class="fas fa-filter"></i> Apply
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if($dateMode === 'custom')
            <a href="?mode=custom&start_date={{ $nextStart }}&end_date={{ $nextEnd }}" class="text-gray-400 hover:text-gray-600 bg-white p-2 rounded-lg shadow-sm border transition flex items-center justify-center w-10 h-10 sm:w-9 sm:h-9 shrink-0"><i class="fas fa-chevron-right text-sm"></i></a>
        @else
            <a href="?mode=month&month={{ $nextMonth }}" class="text-gray-400 hover:text-gray-600 bg-white p-2 rounded-lg shadow-sm border transition flex items-center justify-center w-10 h-10 sm:w-9 sm:h-9 shrink-0"><i class="fas fa-chevron-right text-sm"></i></a>
        @endif
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-3 gap-2 sm:gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-6 flex flex-col items-center justify-center relative overflow-hidden">
        <div class="hidden sm:block absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-arrow-down text-6xl text-green-500"></i></div>
        <p class="text-[11px] min-[400px]:text-sm sm:text-2xl md:text-3xl lg:text-4xl font-bold text-green-500 z-10 order-1 sm:order-2 truncate w-full text-center" title="{{ $formatAmount($thisMonthIncome) }}">{{ $formatAmount($thisMonthIncome) }}</p>
        <h3 class="text-[9px] min-[400px]:text-[10px] sm:text-sm font-semibold text-gray-500 uppercase tracking-wider mb-0 sm:mb-2 mt-1 sm:mt-0 z-10 order-2 sm:order-1 text-center truncate w-full">Income</h3>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-6 flex flex-col items-center justify-center relative overflow-hidden">
        <div class="hidden sm:block absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-arrow-up text-6xl text-red-500"></i></div>
        <p class="text-[11px] min-[400px]:text-sm sm:text-2xl md:text-3xl lg:text-4xl font-bold text-red-500 z-10 order-1 sm:order-2 truncate w-full text-center" title="{{ $formatAmount($thisMonthExpense) }}">{{ $formatAmount($thisMonthExpense) }}</p>
        <h3 class="text-[9px] min-[400px]:text-[10px] sm:text-sm font-semibold text-gray-500 uppercase tracking-wider mb-0 sm:mb-2 mt-1 sm:mt-0 z-10 order-2 sm:order-1 text-center truncate w-full">Expenses</h3>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 sm:p-6 flex flex-col items-center justify-center relative overflow-hidden">
        <div class="hidden sm:block absolute top-0 right-0 p-4 opacity-10"><i class="fas fa-wallet text-6xl text-blue-500"></i></div>
        <p class="text-[11px] min-[400px]:text-sm sm:text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 z-10 order-1 sm:order-2 truncate w-full text-center" title="{{ $formatAmount($totalBalance) }}">{{ $formatAmount($totalBalance) }}</p>
        <h3 class="text-[9px] min-[400px]:text-[10px] sm:text-sm font-semibold text-gray-500 uppercase tracking-wider mb-0 sm:mb-2 mt-1 sm:mt-0 z-10 order-2 sm:order-1 text-center truncate w-full">Balance</h3>
    </div>
</div>

<!-- Filtered Transactions -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden min-h-[400px]">
    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Filtered Transactions</h3>
        <a href="{{ route('transactions.index') }}" class="text-sm text-primary hover:underline font-medium">View All <i class="fas fa-arrow-right text-xs ml-1"></i></a>
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($groupedTransactions as $date => $transactions)
            <div class="bg-gray-50/60 px-6 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider shadow-sm border-b border-gray-100">
                {{ \Carbon\Carbon::parse($date)->isToday() ? 'Today' : (\Carbon\Carbon::parse($date)->isYesterday() ? 'Yesterday' : \Carbon\Carbon::parse($date)->format('M d, Y')) }}
            </div>
            
            @foreach($transactions as $tx)
                @php
                    $categoryType = $tx->category->type ?? 'expense';
                @endphp
                <div class="p-6 hover:bg-gray-50 transition flex items-center justify-between cursor-pointer" onclick="window.location='{{ route('transactions.edit', $tx) }}'">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-lg shadow-sm" style="background-color: {{ $tx->category->color ?? '#9ca3af' }}">
                            <i class="{{ $tx->category->icon ?? 'fas fa-tag' }}"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $tx->category->name ?? 'Uncategorized' }}</h4>
                            <p class="text-sm text-gray-500">{{ $tx->transaction_date->format('h:i A') }} &bull; {{ $tx->account->name ?? 'Deleted Account' }}</p>
                            @if($tx->note)
                                <p class="text-xs text-gray-400 mt-1 italic"><i class="fas fa-quote-left mr-1"></i>{{ $tx->note }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-lg font-bold {{ $categoryType === 'income' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $categoryType === 'income' ? '+' : '-' }}{{ $formatAmount($tx->amount) }}
                    </div>
                </div>
            @endforeach
        @empty
        <div class="p-10 text-center text-gray-500 mt-8">
            <i class="fas fa-receipt text-4xl mb-3 opacity-20"></i>
            <p>No transactions found for this period.</p>
            <a href="{{ route('transactions.create') }}" class="inline-block mt-4 text-primary hover:underline">Add your first transaction</a>
        </div>
        @endforelse
    </div>
</div>
@endsection