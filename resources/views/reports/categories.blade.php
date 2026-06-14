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

    $typeLabel = ucfirst($type);
    $typeColor = $type === 'income' ? 'text-green-600' : 'text-red-500';
    $typeBg = $type === 'income' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-500 border-red-100';
    $pieGradient = 'conic-gradient(#e5e7eb 0deg 360deg)';
    $pieStart = 0;
    $pieSegments = [];

    if ($total > 0) {
        foreach ($categories as $category) {
            $pieEnd = $pieStart + (($category['total'] / $total) * 360);
            $pieSegments[] = $category['color'] . ' ' . number_format($pieStart, 2) . 'deg ' . number_format($pieEnd, 2) . 'deg';
            $pieStart = $pieEnd;
        }

        $pieGradient = 'conic-gradient(' . implode(', ', $pieSegments) . ')';
    }

    $reportQueryWithoutType = array_diff_key(request()->query(), ['type' => true, 'category' => true]);
@endphp

<div class="max-w-4xl mx-auto space-y-6 pb-32">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('reports.index', request()->query()) }}" class="text-sm text-gray-500 hover:text-primary">
                    <i class="fas fa-chevron-left mr-1"></i> Back
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-sm font-semibold text-gray-500">Layer 2</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $typeLabel }} Categories</h1>
            <p class="text-sm text-gray-500 mt-1">Category breakdown for {{ $periodLabel }}.</p>
        </div>

        <div class="flex gap-2 w-full sm:w-auto">
            <a href="{{ route('reports.categories', array_merge($reportQueryWithoutType, ['type' => 'income'])) }}" class="flex-1 sm:flex-none text-center px-4 py-2 rounded-xl text-sm font-bold border {{ $type === 'income' ? 'bg-green-600 border-green-600 text-white' : 'bg-white border-gray-200 text-gray-600' }}">Income</a>
            <a href="{{ route('reports.categories', array_merge($reportQueryWithoutType, ['type' => 'expense'])) }}" class="flex-1 sm:flex-none text-center px-4 py-2 rounded-xl text-sm font-bold border {{ $type === 'expense' ? 'bg-red-500 border-red-500 text-white' : 'bg-white border-gray-200 text-gray-600' }}">Expenses</a>
        </div>
    </div>

    <section class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center gap-3">
            <div>
                <p class="text-xs font-bold text-primary uppercase tracking-wider">Layer 2</p>
                <h2 class="text-lg font-bold text-gray-900">Category List</h2>
                <p class="text-sm text-gray-500 mt-1">Tap a category to open its selected line chart.</p>
            </div>
            <span class="text-xs font-semibold {{ $typeBg }} border rounded-full px-3 py-1">{{ count($categories) }} categories</span>
        </div>

        <div class="p-4 sm:p-6">
            <div class="rounded-2xl {{ $type === 'income' ? 'bg-green-50 border-green-100' : 'bg-red-50 border-red-100' }} border p-4 mb-5 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold {{ $typeColor }} uppercase tracking-wider">Total {{ $typeLabel }}</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $formatAmount($total) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-white shadow-sm flex items-center justify-center {{ $typeColor }}">
                    <i class="{{ $type === 'income' ? 'fas fa-arrow-down' : 'fas fa-arrow-up' }} text-xl"></i>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5 mb-5 flex flex-col items-center text-center">
                <div class="relative w-48 h-48 rounded-full mb-4" style="background: {{ $pieGradient }};">
                    <div class="absolute inset-14 rounded-full bg-white shadow-sm"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div>
                            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Total {{ $typeLabel }}</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">{{ $formatAmount($total) }}</p>
                        </div>
                    </div>
                </div>
                <p class="text-sm text-gray-500">Pie chart shows each category weight.</p>
            </div>

            <div class="space-y-3">
                @forelse($categories as $category)
                    @php
                        $percentage = $total > 0 ? ($category['total'] / $total) * 100 : 0;
                        $ledgerQuery = array_merge(array_diff_key(request()->query(), ['type' => true, 'category' => true]), [
                            'type' => $type,
                            'category' => $category['key'],
                        ]);
                    @endphp
                    <a href="{{ route('reports.ledger', $ledgerQuery) }}" class="block rounded-2xl border border-gray-100 bg-white p-4 hover:border-emerald-200 hover:bg-emerald-50 transition">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 rounded-full flex items-center justify-center text-white shrink-0" style="background-color: {{ $category['color'] }}">
                                    <i class="{{ $category['icon'] }} text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-bold text-gray-900 truncate">{{ $category['name'] }}</h3>
                                    <p class="text-xs text-gray-500">{{ $category['count'] }} items · {{ number_format($percentage, 1) }}% weight</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-bold {{ $typeColor }}">{{ $formatAmount($category['total']) }}</p>
                                <p class="text-xs text-primary">Open</p>
                            </div>
                        </div>
                        <div class="mt-3 w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%; background-color: {{ $category['color'] }};"></div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center">
                        <div class="w-14 h-14 {{ $type === 'income' ? 'bg-green-50 text-green-500' : 'bg-red-50 text-red-500' }} rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="{{ $type === 'income' ? 'fas fa-arrow-down' : 'fas fa-tags' }} text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">No {{ strtolower($typeLabel) }} categories</h3>
                        <p class="text-sm text-gray-500 mt-1">No {{ strtolower($typeLabel) }} transactions match this period.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
