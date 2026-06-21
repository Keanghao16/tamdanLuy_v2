<!-- Report index view -->

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

<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Financial Reports</h1>
        <p class="text-sm text-gray-500 mt-1">Comprehensive breakdown overview of your currency pools for {{ $periodLabel }}.</p>
    </div>

    <div class="relative w-full sm:w-auto" x-data="{ open: false, mode: '{{ $mode }}' }">
        <button @click="open = !open" class="w-full justify-between sm:justify-center text-sm font-semibold bg-white text-gray-700 px-4 py-2.5 rounded-xl shadow-sm border border-gray-200 flex items-center gap-3 hover:bg-gray-50 transition">
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
             class="absolute right-0 mt-3 w-full sm:w-80 bg-white rounded-2xl shadow-xl border border-gray-100 text-gray-800 p-6 z-[60]">
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
                        <input type="month" name="month" value="{{ $currentMonth }}" class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700 cursor-pointer">
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
                                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">End Date</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="far fa-calendar-check text-gray-400"></i>
                                </div>
                                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex gap-3 mt-8 pt-4 border-t border-gray-50">
                    <button type="button" @click="open = false" class="flex-1 px-4 py-2.5 bg-white text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition border border-gray-200 shadow-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-primary text-white text-sm font-bold py-2.5 px-4 rounded-xl hover:bg-emerald-600 transition shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-filter"></i> Apply
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 font-medium">Total Income</p>
            <h3 class="text-xl font-bold text-gray-800 mt-1">{{ $formatAmount($income) }}</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shadow-sm">
            <i class="fas fa-arrow-down text-sm"></i>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 font-medium">Total Expenses</p>
            <h3 class="text-xl font-bold text-gray-800 mt-1">{{ $formatAmount($expense) }}</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shadow-sm">
            <i class="fas fa-arrow-up text-sm"></i>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 font-medium">Allocated Savings</p>
            <h3 class="text-xl font-bold text-gray-800 mt-1">{{ $formatAmount($savings) }}</h3>
        </div>
        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shadow-sm">
            <i class="fas fa-piggy-bank text-sm"></i>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 font-medium">Net Remainder</p>
            <h3 class="text-xl font-bold mt-1 {{ $net >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $net >= 0 ? '+' : '-' }}{{ $formatAmount(abs($net)) }}</h3>
        </div>
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm {{ $net >= 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
            <i class="fas fa-wallet text-sm"></i>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="font-bold text-gray-800 text-lg">Financial Breakdown Overview</h2>
            <p class="text-xs text-gray-500 mt-0.5">Select a currency loop dimension below to inspect structural accounts.</p>
        </div>
        <span class="text-xs font-semibold bg-gray-50 text-gray-500 border border-gray-200 rounded-full px-3 py-1">{{ $currency }}</span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-gray-100">
        <a href="{{ route('reports.categories', array_merge($reportQueryWithoutType, ['type' => 'income'])) }}" class="group p-6 hover:bg-gray-50/50 transition flex flex-col items-center text-center">
            <div class="relative w-36 h-36 rounded-full mb-5 flex items-center justify-center transition-transform group-hover:scale-105 duration-300" style="background: {{ $incomePieGradient }};">
                <div class="absolute inset-10 rounded-full bg-white shadow-inner"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-arrow-down text-2xl text-green-500/80"></i>
                </div>
            </div>
            <h4 class="font-bold text-gray-800">Income Breakdown</h4>
            <p class="text-xs text-gray-400 mt-1">Review active input revenue maps.</p>
            <span class="mt-4 px-4 py-1.5 rounded-xl bg-green-50 text-green-600 text-sm font-bold group-hover:bg-green-600 group-hover:text-white transition duration-200">
                {{ $formatAmount($income) }} <i class="fas fa-chevron-right text-[10px] ml-1"></i>
            </span>
        </a>

        <a href="{{ route('reports.categories', array_merge($reportQueryWithoutType, ['type' => 'expense'])) }}" class="group p-6 hover:bg-gray-50/50 transition flex flex-col items-center text-center">
            <div class="relative w-36 h-36 rounded-full mb-5 flex items-center justify-center transition-transform group-hover:scale-105 duration-300" style="background: {{ $expensePieGradient }};">
                <div class="absolute inset-10 rounded-full bg-white shadow-inner"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-arrow-up text-2xl text-red-500/80"></i>
                </div>
            </div>
            <h4 class="font-bold text-gray-800">Expense Breakdown</h4>
            <p class="text-xs text-gray-400 mt-1">Track structural system outputs.</p>
            <span class="mt-4 px-4 py-1.5 rounded-xl bg-red-50 text-red-600 text-sm font-bold group-hover:bg-red-600 group-hover:text-white transition duration-200">
                {{ $formatAmount($expense) }} <i class="fas fa-chevron-right text-[10px] ml-1"></i>
            </span>
        </a>

        <a href="{{ route('reports.categories', array_merge($reportQueryWithoutType, ['type' => 'saving'])) }}" class="group p-6 hover:bg-gray-50/50 transition flex flex-col items-center text-center">
            <div class="relative w-36 h-36 rounded-full mb-5 flex items-center justify-center transition-transform group-hover:scale-105 duration-300" style="background: {{ $savingPieGradient }};">
                <div class="absolute inset-10 rounded-full bg-white shadow-inner"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-piggy-bank text-2xl text-blue-500/80"></i>
                </div>
            </div>
            <h4 class="font-bold text-gray-800">Savings Breakdown</h4>
            <p class="text-xs text-gray-400 mt-1">Audit explicitly assigned storage reserves.</p>
            <span class="mt-4 px-4 py-1.5 rounded-xl bg-blue-50 text-blue-600 text-sm font-bold group-hover:bg-blue-600 group-hover:text-white transition duration-200">
                {{ $formatAmount($savings) }} <i class="fas fa-chevron-right text-[10px] ml-1"></i>
            </span>
        </a>
    </div>
</div>
@endsection