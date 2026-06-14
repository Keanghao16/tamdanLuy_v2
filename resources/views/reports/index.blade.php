@extends('layouts.app')

@section('content')
@php
    $currency = $activeAccount?->currency ?? $reportCurrency ?? 'USD';
    $isKHR = $currency === 'KHR';
    $formatAmount = function($amount) use ($isKHR) {
        return $isKHR ? number_format($amount, 0) . ' ៛' : '$' . number_format($amount, 2);
    };

    $periodLabel = $mode === 'custom'
        ? \Carbon\Carbon::parse($startDate)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('M d, Y')
        : \Carbon\Carbon::createFromFormat('Y-m', $currentMonth)->format('F Y');

    $incomePieGradient = $income > 0 ? 'conic-gradient(#10b981 0deg 360deg)' : 'conic-gradient(#e5e7eb 0deg 360deg)';
    $expensePieGradient = $expense > 0 ? 'conic-gradient(#ef4444 0deg 360deg)' : 'conic-gradient(#e5e7eb 0deg 360deg)';
    $savingPieGradient = $savings > 0 ? 'conic-gradient(#3b82f6 0deg 360deg)' : 'conic-gradient(#e5e7eb 0deg 360deg)';
    $reportQueryWithoutType = array_diff_key(request()->query(), ['type' => true]);
@endphp

<div class="max-w-4xl mx-auto space-y-6 pb-32">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Reports</h1>
            <p class="text-sm text-gray-500 mt-1">Layer 1 compares income, expense, and savings for {{ $periodLabel }}.</p>
        </div>

        <div class="relative w-full sm:w-auto" x-data="{ open: false, mode: '{{ $mode }}' }">
            <button @click="open = !open" class="w-full justify-between sm:justify-center text-sm text-gray-700 font-medium bg-white px-4 py-2 h-11 rounded-xl shadow-sm border flex items-center gap-3 hover:bg-gray-50 transition">
                <div class="flex items-center gap-2">
                    <i class="far fa-calendar-alt text-gray-400"></i>
                    <span>{{ $periodLabel }}</span>
                </div>
                <i class="fas fa-chevron-down text-xs text-gray-400 transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-300 origin-top"
                 x-transition:enter-start="opacity-0 scale-95 translate-y-[-10px]"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200 origin-top"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 translate-y-[-10px]"
                 @click.away="open = false"
                 x-cloak
                 class="absolute right-0 mt-3 w-[calc(100vw-2rem)] max-w-sm sm:w-80 bg-white rounded-2xl shadow-xl border border-gray-100 text-gray-800 p-6 z-[60]">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-bold text-gray-900">Filter Period</h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 bg-gray-50 hover:bg-gray-100 w-8 h-8 rounded-full flex items-center justify-center transition">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form action="" method="GET" class="text-left">
                    <input type="hidden" name="mode" :value="mode">
                    <div class="flex bg-gray-100 p-1 rounded-xl mb-6">
                        <button type="button" @click="mode = 'month'" :class="mode === 'month' ? 'bg-white shadow-sm text-primary font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" class="flex-1 py-2 text-xs uppercase tracking-wider rounded-lg transition-all duration-200">By Month</button>
                        <button type="button" @click="mode = 'custom'" :class="mode === 'custom' ? 'bg-white shadow-sm text-primary font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" class="flex-1 py-2 text-xs uppercase tracking-wider rounded-lg transition-all duration-200">Custom Range</button>
                    </div>

                    <div x-show="mode === 'month'" x-collapse.duration.300ms>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Select Month</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="far fa-calendar text-gray-400"></i>
                            </div>
                            <input type="month" name="month" value="{{ $currentMonth }}" class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700 cursor-pointer hover:border-gray-300">
                        </div>
                    </div>

                    <div x-show="mode === 'custom'" x-collapse.duration.300ms style="display: none;">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Start Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <i class="far fa-calendar-alt text-gray-400"></i>
                                    </div>
                                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700 cursor-pointer hover:border-gray-300">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">End Date</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                        <i class="far fa-calendar-check text-gray-400"></i>
                                    </div>
                                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700 cursor-pointer hover:border-gray-300">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-8 pt-4 border-t border-gray-50">
                        <button type="button" @click="open = false" class="flex-1 px-4 py-2.5 bg-white text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition-colors border border-gray-200 shadow-sm">Cancel</button>
                        <button type="submit" class="flex-1 bg-primary text-white text-sm font-bold py-2.5 px-4 rounded-xl hover:bg-emerald-600 transition-colors shadow-sm shadow-emerald-200 flex items-center justify-center gap-2">
                            <i class="fas fa-filter"></i> Apply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <section class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50">
            <p class="text-xs font-bold text-primary uppercase tracking-wider">Layer 1</p>
            <h2 class="text-lg font-bold text-gray-900">Income, Expense, and Savings Overview</h2>
            <p class="text-sm text-gray-500 mt-1">Each pie shows the total amount for its type.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 sm:p-6">
            <a href="{{ route('reports.categories', array_merge($reportQueryWithoutType, ['type' => 'income'])) }}" class="group rounded-2xl border border-gray-100 bg-gray-50 p-5 hover:border-emerald-200 hover:bg-emerald-50 transition">
                <div class="relative w-44 h-44 mx-auto rounded-full mb-5" style="background: {{ $incomePieGradient }};">
                    <div class="absolute inset-12 rounded-full bg-white shadow-sm"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Income</p>
                            <p class="text-xl font-bold text-gray-900 mt-1">{{ $formatAmount($income) }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-gray-900">Income Categories</h3>
                        <p class="text-xs text-gray-500 mt-1">Open income breakdown</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-emerald-600"></i>
                </div>
            </a>

            <a href="{{ route('reports.categories', array_merge($reportQueryWithoutType, ['type' => 'expense'])) }}" class="group rounded-2xl border border-gray-100 bg-gray-50 p-5 hover:border-red-200 hover:bg-red-50 transition">
                <div class="relative w-44 h-44 mx-auto rounded-full mb-5" style="background: {{ $expensePieGradient }};">
                    <div class="absolute inset-12 rounded-full bg-white shadow-sm"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Expenses</p>
                            <p class="text-xl font-bold text-gray-900 mt-1">{{ $formatAmount($expense) }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-gray-900">Expense Categories</h3>
                        <p class="text-xs text-gray-500 mt-1">Open expense breakdown</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-red-600"></i>
                </div>
            </a>

            <a href="{{ route('reports.categories', array_merge($reportQueryWithoutType, ['type' => 'saving'])) }}" class="group rounded-2xl border border-gray-100 bg-gray-50 p-5 hover:border-blue-200 hover:bg-blue-50 transition">
                <div class="relative w-44 h-44 mx-auto rounded-full mb-5" style="background: {{ $savingPieGradient }};">
                    <div class="absolute inset-12 rounded-full bg-white shadow-sm"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Savings</p>
                            <p class="text-xl font-bold text-gray-900 mt-1">{{ $formatAmount($savings) }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-gray-900">Saving Categories</h3>
                        <p class="text-xs text-gray-500 mt-1">Open saving breakdown</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 group-hover:text-blue-600"></i>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-3 gap-2 px-4 pb-5 sm:px-6">
            <div class="rounded-2xl bg-white border border-gray-100 p-3 text-center">
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Net (Cash Remainder)</p>
                <p class="text-base sm:text-lg font-bold {{ $net >= 0 ? 'text-green-600' : 'text-red-500' }} mt-1">{{ $net >= 0 ? '+' : '-' }}{{ $formatAmount(abs($net)) }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-gray-100 p-3 text-center">
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Transactions</p>
                <p class="text-base sm:text-lg font-bold text-gray-900 mt-1">{{ count($transactions) }}</p>
            </div>
            <div class="rounded-2xl bg-white border border-gray-100 p-3 text-center">
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Currency</p>
                <p class="text-base sm:text-lg font-bold text-gray-900 mt-1">{{ $currency }}</p>
            </div>
        </div>
    </section>
</div>
@endsection