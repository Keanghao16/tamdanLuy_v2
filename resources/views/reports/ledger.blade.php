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

    // UI Styles mapped cleanly across three options
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

<div class="max-w-4xl mx-auto space-y-6 pb-32">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('reports.categories', $reportQueryWithoutCategory) }}" class="text-sm text-gray-500 hover:text-primary">
                    <i class="fas fa-chevron-left mr-1"></i> Back
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-sm font-semibold text-gray-500">Layer 3</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $categoryName }}</h1>
            <p class="text-sm text-gray-500 mt-1">Bar chart and ledger for {{ $periodLabel }}.</p>
        </div>

        <div class="w-12 h-12 rounded-full bg-white border border-gray-100 shadow-sm flex items-center justify-center shrink-0" style="color: {{ $categoryColor }}">
            <i class="{{ $categoryIcon }} text-xl"></i>
        </div>
    </div>

    <section class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center gap-3">
            <div>
                <p class="text-xs font-bold text-primary uppercase tracking-wider">Layer 3</p>
                <h2 class="text-lg font-bold text-gray-900">Selected Category Bar Chart</h2>
                <p class="text-sm text-gray-500 mt-1">Only {{ strtolower($categoryName) }} transactions are shown.</p>
            </div>
            <span class="text-xs font-semibold bg-white border border-gray-200 rounded-full px-3 py-1">{{ count($transactions) }} lines</span>
        </div>

        <div class="p-4 sm:p-6 space-y-6">
            <div class="rounded-2xl {{ $bgContainerClass }} border p-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold {{ $amountClass }} uppercase tracking-wider">Total {{ ucfirst($type) }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $formatAmount($total) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center {{ $amountClass }}">
                    <i class="fas fa-chart-bar text-xl"></i>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900">Daily Volume</h3>
                        <p class="text-xs text-gray-500">Bar height follows {{ strtolower($type) }} amount.</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-500 bg-white border border-gray-200 rounded-full px-3 py-1">{{ count($dailyTotals) }} days</span>
                </div>

                <div class="overflow-x-auto pb-2">
                    <div class="flex items-end gap-1 min-w-max h-40">
                        @forelse($dailyTotals as $dayKey => $day)
                            @php
                                $barHeight = $maxAmount > 0 ? max(8, ($day['amount'] / $maxAmount) * 100) : 0;
                            @endphp
                            <div class="flex-[0_0_24px] flex flex-col items-center gap-2">
                                <div class="w-full h-32 bg-white rounded-t-xl border border-gray-100 flex items-end justify-center overflow-hidden">
                                    <div class="w-full rounded-t-md {{ $barFillClass }} transition-all duration-300" style="height: {{ $day['amount'] > 0 ? $barHeight : 0 }}%;" title="{{ \Carbon\Carbon::parse($dayKey)->format('M d, Y') }} · {{ $formatAmount($day['amount']) }} · {{ $day['count'] }} txns"></div>
                                </div>
                                <span class="text-[10px] text-gray-400 -rotate-45 origin-top translate-y-1 whitespace-nowrap">{{ \Carbon\Carbon::parse($dayKey)->format('M d') }}</span>
                            </div>
                        @empty
                            <div class="w-full text-center text-gray-500 py-10">No daily data available.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-100 bg-white flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-gray-900">Chronological Ledger</h3>
                        <p class="text-xs text-gray-500">Negative financial values are highlighted in red.</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-500 bg-gray-50 border border-gray-200 rounded-full px-3 py-1">{{ count($transactions) }} lines</span>
                </div>

                <div class="divide-y divide-gray-100 bg-white">
                    @forelse($transactions as $tx)
                        @php
                            // Income & explicit savings are treated positively on transaction line indicators
                            $isPositive = in_array($type, ['income', 'saving']);
                            $signedAmount = $isPositive ? $tx->amount : -$tx->amount;
                            $sign = $signedAmount < 0 ? '-' : '+';
                            $lineAmountClass = $signedAmount < 0 ? 'text-red-600' : 'text-green-600';
                        @endphp
                        <div class="flex items-center justify-between gap-3 p-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white shrink-0" style="background-color: {{ $tx->category?->color ?? $categoryColor }}">
                                    <i class="{{ $tx->category?->icon ?? $categoryIcon }} text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <h4 class="font-bold text-gray-900 truncate">{{ $tx->category?->name ?? 'Uncategorized' }}</h4>
                                    <p class="text-xs text-gray-500 truncate">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('M d, Y h:i A') }} · {{ $tx->account?->name ?? 'Deleted Account' }}</p>
                                    @if($tx->note)
                                        <p class="text-xs text-gray-400 truncate mt-0.5"><i class="fas fa-quote-left mr-1"></i>{{ $tx->note }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-bold {{ $lineAmountClass }}">{{ $sign }}{{ $formatAmount(abs($signedAmount)) }}</p>
                                <p class="text-[11px] font-semibold uppercase tracking-wider {{ $amountClass }}">{{ $type }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="p-10 text-center">
                            <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <h3 class="font-bold text-gray-900">No ledger lines</h3>
                            <p class="text-sm text-gray-500 mt-1">No transactions match this category filter.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>
</div>
@endsection