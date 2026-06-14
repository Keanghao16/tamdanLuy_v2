@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <h1 class="text-3xl font-bold text-gray-900">Budgets</h1>
    <a href="{{ route('budgets.create') }}" class="w-full sm:w-auto text-center bg-primary hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-sm shadow-emerald-200 transition flex items-center justify-center gap-2">
        <i class="fas fa-plus"></i> New Budget
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="divide-y divide-gray-100">
    @forelse($budgets as $budget)
        @php
            $percentage = $budget->amount > 0 ? min(100, ($budget->spent / $budget->amount) * 100) : 0;
            $colorClass = $percentage >= 100 ? 'bg-red-500' : ($percentage > 75 ? 'bg-yellow-500' : 'bg-green-500');
            $remaining = $budget->amount - $budget->spent;
            $currencySymbol = $budget->account->currency === 'KHR' ? '៛' : '$';
            $isKHR = $budget->account->currency === 'KHR';
            
            $spentFormatted = $isKHR ? number_format($budget->spent, 0) : number_format($budget->spent, 2);
            $amountFormatted = $isKHR ? number_format($budget->amount, 0) : number_format($budget->amount, 2);
            $remainingFormatted = $isKHR ? number_format($remaining, 0) : number_format($remaining, 2);
            
            // Format labels
            $timeLabel = '';
            if (\Carbon\Carbon::parse($budget->start_date)->startOfMonth()->isSameDay($budget->start_date) && 
                \Carbon\Carbon::parse($budget->end_date)->endOfMonth()->isSameDay($budget->end_date)) {
                $timeLabel = \Carbon\Carbon::parse($budget->start_date)->format('F Y');
            } else {
                $timeLabel = \Carbon\Carbon::parse($budget->start_date)->format('M d') . ' - ' . \Carbon\Carbon::parse($budget->end_date)->format('M d, Y');
            }
        @endphp
        
        <a href="{{ route('budgets.edit', $budget) }}" class="block p-4 sm:p-6 cursor-pointer hover:bg-gray-50 transition">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-3 sm:gap-0">
                <div class="flex items-center space-x-3 w-full sm:w-auto">
                    <div class="flex -space-x-3 relative shrink-0">
                        @foreach($budget->categories->take(3) as $cat)
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center text-white shadow-sm border-2 border-white relative z-{{ 30 - ($loop->index * 10) }}" style="background-color: {{ $cat->color ?? '#3b82f6' }}">
                                <i class="{{ $cat->icon ?? 'fas fa-tag' }} text-base sm:text-lg"></i>
                            </div>
                        @endforeach
                        @if($budget->categories->count() > 3)
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex items-center justify-center bg-gray-100 text-gray-500 font-bold shadow-sm border-2 border-white relative z-0 text-xs sm:text-base">
                                +{{ $budget->categories->count() - 3 }}
                            </div>
                        @endif
                    </div>
                    <div class="pl-2 min-w-0">
                        <h3 class="font-bold text-gray-900 text-base sm:text-lg truncate max-w-full" title="{{ $budget->categories->pluck('name')->implode(', ') }}">
                            {{ \Illuminate\Support\Str::limit($budget->categories->pluck('name')->implode(', '), 25) }}
                        </h3>
                        <p class="text-xs text-gray-400 font-medium mt-0.5 truncate">{{ $budget->account->name }} • {{ $timeLabel }}</p>
                    </div>
                </div>
                <div class="text-left sm:text-right pl-[3.25rem] sm:pl-0 w-full sm:w-auto">
                    <span class="text-gray-900 font-medium block text-sm sm:text-[15px]">
                        {{ $isKHR ? '' : $currencySymbol }}{{ $remainingFormatted }}{{ $isKHR ? $currencySymbol : '' }} left
                    </span>
                </div>
            </div>
            
            <div class="flex justify-between items-center mb-2 pl-[3.25rem] sm:pl-0">
                <span class="text-xs sm:text-sm font-medium text-gray-800">
                    {{ $isKHR ? '' : $currencySymbol }}{{ $spentFormatted }}{{ $isKHR ? $currencySymbol : '' }} <span class="text-gray-400 font-normal">/ {{ $isKHR ? '' : $currencySymbol }}{{ $amountFormatted }}{{ $isKHR ? $currencySymbol : '' }}</span>
                </span>
                <span class="text-xs font-bold {{ $colorClass === 'bg-red-500' ? 'text-red-500' : ($colorClass === 'bg-yellow-500' ? 'text-yellow-600' : 'text-green-500') }}">
                    {{ round($percentage) }}%
                </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden mb-1 relative ml-[3.25rem] sm:ml-0" style="width: calc(100% - 3.25rem); sm:width: 100%;">
                <div class="{{ $colorClass }} h-2 rounded-full transition-all duration-500 ease-in-out" style="width: {{ $percentage }}%"></div>
            </div>
        </a>
    @empty
        <div class="p-10 text-center">
            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-chart-pie"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No budgets set</h3>
            <p class="text-gray-500 text-sm">Start planning by creating a budget for a category.</p>
        </div>
    @endforelse
    </div>
</div>
@endsection