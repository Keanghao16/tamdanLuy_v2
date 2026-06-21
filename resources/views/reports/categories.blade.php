<!-- Report categories view -->

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
    
    $colorMaps = [
        'income' => ['text' => 'text-green-600', 'bg' => 'bg-green-50 text-green-600 border-green-100', 'icon' => 'fas fa-arrow-down', 'hover' => 'hover:border-green-200 hover:bg-green-50/50', 'pill' => 'bg-green-600'],
        'saving' => ['text' => 'text-blue-600', 'bg' => 'bg-blue-50 text-blue-600 border-blue-100', 'icon' => 'fas fa-piggy-bank', 'hover' => 'hover:border-blue-200 hover:bg-blue-50/50', 'pill' => 'bg-blue-600'],
        'expense' => ['text' => 'text-red-500', 'bg' => 'bg-red-50 text-red-500 border-red-100', 'icon' => 'fas fa-arrow-up', 'hover' => 'hover:border-red-200 hover:bg-red-50/50', 'pill' => 'bg-red-500']
    ];

    $currentMap = $colorMaps[$type] ?? $colorMaps['expense'];

    $typeColor = $currentMap['text'];
    $typeBg = $currentMap['bg'];
    $typeIcon = $currentMap['icon'];
    $hoverClass = $currentMap['hover'];
    $pillActive = $currentMap['pill'];

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

<div class="mb-6 flex flex-col gap-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('reports.index', request()->query()) }}" class="text-sm font-semibold text-gray-500 hover:text-primary transition flex items-center">
            <i class="fas fa-chevron-left mr-1.5 text-xs"></i> Summary Overview
        </a>
        <span class="text-gray-300 text-xs">/</span>
        <span class="text-sm font-semibold text-gray-400">Category Analytics</span>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 w-full">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $typeLabel }} Breakdown</h1>
            <p class="text-sm text-gray-500 mt-0.5">Category breakdown analytics for {{ $periodLabel }}.</p>
        </div>

        <div class="flex bg-white p-1 rounded-xl border border-gray-200 shadow-sm w-full sm:w-auto shrink-0">
            <a href="{{ route('reports.categories', array_merge($reportQueryWithoutType, ['type' => 'income'])) }}" class="flex-1 sm:flex-none text-center px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $type === 'income' ? 'bg-green-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">Income</a>
            <a href="{{ route('reports.categories', array_merge($reportQueryWithoutType, ['type' => 'saving'])) }}" class="flex-1 sm:flex-none text-center px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $type === 'saving' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">Savings</a>
            <a href="{{ route('reports.categories', array_merge($reportQueryWithoutType, ['type' => 'expense'])) }}" class="flex-1 sm:flex-none text-center px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $type === 'expense' ? $pillActive . ' text-white shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">Expenses</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="space-y-4">
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Aggregate {{ $typeLabel }}</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $formatAmount($total) }}</h3>
            </div>
            <div class="w-11 h-11 rounded-xl shadow-sm flex items-center justify-center {{ $typeBg }} border">
                <i class="{{ $typeIcon }} text-base"></i>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm flex flex-col items-center text-center">
            <div class="relative w-40 h-40 rounded-full mb-4 transition-transform hover:rotate-12 duration-500" style="background: {{ $pieGradient }};">
                <div class="absolute inset-12 rounded-full bg-white shadow-md"></div>
                <div class="absolute inset-0 flex items-center justify-center text-xs text-gray-400 font-bold uppercase tracking-wider">
                    Metrics
                </div>
            </div>
            <p class="text-xs font-medium text-gray-400 leading-relaxed">Structural distribution weights mapped according to total transaction pools.</p>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Monitored Classes</h3>
            <span class="text-xs font-bold text-gray-400 bg-white border px-2.5 py-0.5 rounded-lg shadow-2xs">{{ count($categories) }} Categories</span>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($categories as $category)
                @php
                    $percentage = $total > 0 ? ($category['total'] / $total) * 100 : 0;
                    $ledgerQuery = array_merge(array_diff_key(request()->query(), ['type' => true, 'category' => true]), [
                        'type' => $type,
                        'category' => $category['key'],
                    ]);
                @endphp
                <a href="{{ route('reports.ledger', $ledgerQuery) }}" class="flex items-center justify-between p-4 {{ $hoverClass }} transition group">
                    <div class="flex items-center gap-3.5 min-w-0 flex-1">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0 shadow-sm" style="background-color: {{ $category['color'] }}">
                            <i class="{{ $category['icon'] }} text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex justify-between items-baseline gap-2">
                                <h4 class="font-semibold text-gray-800 truncate text-sm group-hover:text-primary transition">{{ $category['name'] }}</h4>
                                <span class="text-sm font-bold {{ $typeColor }} shrink-0">{{ $formatAmount($category['total']) }}</span>
                            </div>
                            <div class="flex items-center gap-2 mt-1.5">
                                <div class="flex-1 bg-gray-100 h-1.5 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500" style="width: {{ $percentage }}%; background-color: {{ $category['color'] }};"></div>
                                </div>
                                <span class="text-[11px] text-gray-400 font-bold shrink-0">{{ number_format($percentage, 0) }}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 shrink-0 text-gray-300 group-hover:text-gray-500 transition-colors">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                </a>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <i class="fas fa-tags text-3xl mb-3 opacity-20 block"></i>
                    <p class="text-sm">No transaction pools matched this execution parameter context.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection