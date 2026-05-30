@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <h1 class="text-3xl font-bold text-gray-900">Budgets</h1>
    <a href="{{ route('budgets.create') }}" class="w-full sm:w-auto text-center bg-primary hover:bg-emerald-600 text-white px-5 py-2.5 rounded-xl font-bold shadow-sm shadow-emerald-200 transition flex items-center justify-center gap-2">
        <i class="fas fa-plus"></i> New Budget
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
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
        
        <a href="{{ route('budgets.edit', $budget) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-100 p-5 cursor-pointer hover:shadow-md transition">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white shadow-sm" style="background-color: {{ $budget->category->color ?? '#3b82f6' }}">
                        <i class="{{ $budget->category->icon ?? 'fas fa-tag' }} text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-lg">{{ $budget->category->name ?? 'Unknown' }}</h3>
                        <p class="text-xs text-gray-400 font-medium mt-0.5">{{ $budget->account->name }} • {{ $timeLabel }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="text-gray-900 font-medium block text-[15px]">
                        {{ $isKHR ? '' : $currencySymbol }}{{ $remainingFormatted }}{{ $isKHR ? $currencySymbol : '' }} left
                    </span>
                </div>
            </div>
            
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-gray-800">
                    {{ $isKHR ? '' : $currencySymbol }}{{ $spentFormatted }}{{ $isKHR ? $currencySymbol : '' }} <span class="text-gray-400 font-normal">/ {{ $isKHR ? '' : $currencySymbol }}{{ $amountFormatted }}{{ $isKHR ? $currencySymbol : '' }}</span>
                </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden mb-1 relative">
                <div class="{{ $colorClass }} h-2 rounded-full transition-all duration-500 ease-in-out" style="width: {{ $percentage }}%"></div>
            </div>
        </a>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <div class="w-16 h-16 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-chart-pie"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No budgets set</h3>
            <p class="text-gray-500 mb-6 text-sm">Start planning by creating a budget for a category.</p>
        </div>
    @endforelse

    <a href="{{ route('budgets.create') }}" class="block w-full text-center bg-white rounded-2xl shadow-sm border border-gray-100 p-4 hover:bg-gray-50 transition cursor-pointer mt-4">
        <div class="flex items-center justify-start ml-2 space-x-4 text-gray-800 font-medium">
            <div class="w-10 h-10 rounded-full bg-gray-600 text-white flex items-center justify-center">
                <i class="fas fa-plus"></i>
            </div>
            <span>Add budget category</span>
        </div>
    </a>
</div>
@endsection