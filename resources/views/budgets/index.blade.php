<!-- Budgets index view -->

@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Budgets</h1>
        <p class="text-sm text-gray-500 mt-1">Plan and manage your asset targets across dynamic category pools.</p>
    </div>
    <a href="{{ route('budgets.create') }}" class="w-full sm:w-auto text-center bg-primary hover:bg-emerald-600 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-sm shadow-emerald-200 transition flex items-center justify-center gap-2">
        <i class="fas fa-plus text-xs"></i> New Budget
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="divide-y divide-gray-100">
    @forelse($budgets as $budget)
        @php
            $percentage = $budget->amount > 0 ? min(100, ($budget->spent / $budget->amount) * 100) : 0;
            $colorClass = $percentage >= 100 ? 'bg-red-500' : ($percentage > 75 ? 'bg-yellow-500' : 'bg-green-500');
            $textTrackClass = $percentage >= 100 ? 'text-red-500' : ($percentage > 75 ? 'text-yellow-600' : 'text-green-500');
            
            $remaining = $budget->amount - $budget->spent;
            $currencySymbol = $budget->account->currency === 'KHR' ? '៛' : '$';
            $isKHR = $budget->account->currency === 'KHR';
            
            $spentFormatted = $isKHR ? number_format($budget->spent, 0) : number_format($budget->spent, 2);
            $amountFormatted = $isKHR ? number_format($budget->amount, 0) : number_format($budget->amount, 2);
            $remainingFormatted = $isKHR ? number_format($remaining, 0) : number_format($remaining, 2);
            
            $timeLabel = '';
            if (\Carbon\Carbon::parse($budget->start_date)->startOfMonth()->isSameDay($budget->start_date) && 
                \Carbon\Carbon::parse($budget->end_date)->endOfMonth()->isSameDay($budget->end_date)) {
                $timeLabel = \Carbon\Carbon::parse($budget->start_date)->format('F Y');
            } else {
                $timeLabel = \Carbon\Carbon::parse($budget->start_date)->format('M d') . ' - ' . \Carbon\Carbon::parse($budget->end_date)->format('M d, Y');
            }
        @endphp
        
        <a href="{{ route('budgets.edit', $budget) }}" class="block p-5 sm:p-6 cursor-pointer hover:bg-gray-50/50 transition group">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-3 sm:gap-0">
                <div class="flex items-center space-x-3 w-full sm:w-auto">
                    <div class="flex -space-x-3 relative shrink-0">
                        @foreach($budget->categories->take(3) as $cat)
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-sm border-2 border-white relative transition-transform group-hover:scale-105" style="background-color: {{ $cat->color ?? '#3b82f6' }}; z-index: {{ 30 - ($loop->index * 10) }};">
                                <i class="{{ $cat->icon ?? 'fas fa-tag' }} text-sm"></i>
                            </div>
                        @endforeach
                        @if($budget->categories->count() > 3)
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-100 text-gray-500 font-bold shadow-sm border-2 border-white relative z-0 text-xs">
                                +{{ $budget->categories->count() - 3 }}
                            </div>
                        @endif
                    </div>
                    <div class="pl-2 min-w-0">
                        <h3 class="font-bold text-gray-800 text-sm sm:text-base truncate group-hover:text-primary transition" title="{{ $budget->categories->pluck('name')->implode(', ') }}">
                            {{ \Illuminate\Support\Str::limit($budget->categories->pluck('name')->implode(', '), 30) }}
                        </h3>
                        <p class="text-xs text-gray-400 font-medium mt-0.5 truncate">{{ $budget->account->name }} &bull; {{ $timeLabel }}</p>
                    </div>
                </div>
                <div class="text-left sm:text-right pl-[3.25rem] sm:pl-0 w-full sm:w-auto shrink-0">
                    <span class="text-gray-800 font-bold block text-sm sm:text-base">
                        {{ $isKHR ? '' : $currencySymbol }}{{ $remainingFormatted }}{{ $isKHR ? $currencySymbol : '' }} <span class="text-xs font-medium text-gray-400">left</span>
                    </span>
                </div>
            </div>
            
            <div class="flex justify-between items-center mb-2 pl-[3.25rem] sm:pl-0">
                <span class="text-xs font-semibold text-gray-700">
                    {{ $isKHR ? '' : $currencySymbol }}{{ $spentFormatted }}{{ $isKHR ? $currencySymbol : '' }} <span class="text-gray-400 font-normal">/ {{ $isKHR ? '' : $currencySymbol }}{{ $amountFormatted }}{{ $isKHR ? $currencySymbol : '' }}</span>
                </span>
                <span class="text-xs font-bold {{ $textTrackClass }}">
                    {{ round($percentage) }}%
                </span>
            </div>
            
            <!-- Progress Bar -->
            <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden mb-1 relative ml-[3.25rem] sm:ml-0" style="width: calc(100% - 3.25rem); sm:width: 100%;">
                <div class="{{ $colorClass }} h-2 rounded-full transition-all duration-500 ease-in-out" style="width: {{ $percentage }}%"></div>
            </div>
        </a>
    @empty
        <div class="p-16 text-center text-gray-500">
            <div class="w-14 h-14 bg-gray-50 border border-gray-100 text-gray-400 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i class="fas fa-chart-pie text-lg"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-800 mb-1">No execution budgets allocated</h3>
            <p class="text-gray-400 text-xs max-w-xs mx-auto">Start planning structurally by mapping limits against your transaction channels.</p>
        </div>
    @endforelse
    </div>
</div>
@endsection