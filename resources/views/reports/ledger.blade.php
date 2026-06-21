<!-- Report ledger view -->

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

    $categoryName = $category?->name ?? ($categoryId === 'uncategorized' ? 'Uncategorized' : 'Selected Category');

    $themeMaps = [
        'income' => ['color' => '#10b981', 'bg' => 'bg-green-50 border-green-100', 'bar' => 'bg-green-500', 'text' => 'text-green-600', 'icon' => 'fas fa-arrow-down'],
        'saving' => ['color' => '#3b82f6', 'bg' => 'bg-blue-50 border-blue-100', 'bar' => 'bg-blue-500', 'text' => 'text-blue-600', 'icon' => 'fas fa-piggy-bank'],
        'expense' => ['color' => '#ef4444', 'bg' => 'bg-red-50 border-red-100', 'bar' => 'bg-red-500', 'text' => 'text-red-600', 'icon' => 'fas fa-arrow-up']
    ];

    $activeTheme = $themeMaps[$type] ?? $themeMaps['expense'];

    $categoryIcon = $category?->icon ?? $activeTheme['icon'];
    $categoryColor = $category?->color ?? $activeTheme['color'];
    $lineColor = $activeTheme['color'];
    $amountClass = $activeTheme['text'];
    $bgContainerClass = $activeTheme['bg'];
    $barFillClass = $activeTheme['bar'];

    $maxAmount = collect($dailyTotals)->max('amount') ?: 0;
    $reportQueryWithoutCategory = array_diff_key(request()->query(), ['category' => true]);
@endphp

<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="{{ route('reports.categories', $reportQueryWithoutCategory) }}" class="text-sm font-semibold text-gray-500 hover:text-primary transition flex items-center">
                <i class="fas fa-chevron-left mr-1.5 text-xs"></i> Category Analytics
            </a>
            <span class="text-gray-300 text-xs">/</span>
            <span class="text-sm font-semibold text-gray-400">Detailed Ledger</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $categoryName }} Ledger</h1>
        <p class="text-sm text-gray-500 mt-0.5">Detailed category ledger accounts tracking for {{ $periodLabel }}.</p>
    </div>

    <div class="w-12 h-12 rounded-xl bg-white border border-gray-100 shadow-sm flex items-center justify-center shrink-0" style="color: {{ $categoryColor }}">
        <i class="{{ $categoryIcon }} text-xl"></i>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="space-y-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Ledger Accumulation</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $formatAmount($total) }}</h3>
            </div>
            <div class="w-11 h-11 rounded-xl border flex items-center justify-center shadow-sm {{ $bgContainerClass }} {{ $amountClass }}">
                <i class="fas fa-coins text-sm"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <h4 class="font-bold text-sm text-gray-800 mb-4">Daily Velocity Map</h4>
            <div class="overflow-x-auto pb-1">
                <div class="flex items-end gap-1.5 h-32 min-w-max px-1">
                    @forelse($dailyTotals as $dayKey => $day)
                        @php
                            $barHeight = $maxAmount > 0 ? max(6, ($day['amount'] / $maxAmount) * 100) : 0;
                        @endphp
                        <div class="flex flex-col items-center gap-1.5 group cursor-pointer">
                            <div class="w-6 h-24 bg-gray-50 border border-gray-100/70 rounded-t-lg flex items-end overflow-hidden relative shadow-2xs">
                                <div class="w-full {{ $barFillClass }} rounded-t-md opacity-80 group-hover:opacity-100 transition-all duration-200" 
                                     style="height: {{ $day['amount'] > 0 ? $barHeight : 0 }}%;" 
                                     title="{{ \Carbon\Carbon::parse($dayKey)->format('M d') }}: {{ $formatAmount($day['amount']) }}"></div>
                             </div>
                            <span class="text-[9px] font-bold text-gray-400 group-hover:text-gray-600 transition-colors">{{ \Carbon\Carbon::parse($dayKey)->format('d') }}</span>
                        </div>
                    @empty
                        <div class="w-full text-center text-xs text-gray-400 py-8">Empty matrix pools.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Chronological Flow Accounts</h3>
            <span class="text-xs font-bold text-gray-400 bg-white border px-2.5 py-0.5 rounded-lg">{{ count($transactions) }} Items</span>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($transactions as $tx)
                @php
                    $isPositive = in_array($type, ['income', 'saving']);
                    $signedAmount = $isPositive ? $tx->amount : -$tx->amount;
                    $sign = $signedAmount < 0 ? '-' : '+';
                    $lineAmountClass = $signedAmount < 0 ? 'text-red-600' : 'text-green-600';
                @endphp
                <div class="p-4 flex items-center justify-between hover:bg-gray-50/40 transition gap-3">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0 shadow-sm transition-transform hover:scale-105" 
                             style="background-color: {{ $tx->category?->color ?? $categoryColor }}">
                            <i class="{{ $tx->category?->icon ?? $categoryIcon }} text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-semibold text-gray-800 text-sm truncate">{{ $tx->category?->name ?? 'Uncategorized' }}</h4>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($tx->transaction_date)->format('h:i A') }} &bull; {{ $tx->account?->name ?? 'Deleted Account' }}
                            </p>
                            @if($tx->note)
                                <p class="text-xs text-gray-400 mt-1 italic max-w-md truncate">
                                    <i class="fas fa-quote-left mr-1 text-[10px] opacity-60"></i>{{ $tx->note }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="text-base font-bold tracking-tight {{ $lineAmountClass }}">
                            {{ $sign }}{{ $formatAmount(abs($signedAmount)) }}
                        </span>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mt-0.5">{{ $type }}</p>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <i class="fas fa-receipt text-3xl mb-3 opacity-20 block"></i>
                    <p class="text-sm">No transaction files mapped inside this historical matrix index.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection