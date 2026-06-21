@extends('layouts.app')

@section('content')
<div class="mb-6 flex flex-col gap-4">
    <div class="flex items-center gap-2">
        <a href="{{ route('budgets.index') }}" class="text-sm font-semibold text-gray-500 hover:text-primary transition flex items-center">
            Budgets
        </a>
        <span class="text-gray-300 text-xs">/</span>
        <span class="text-sm font-semibold text-gray-400">New Budget</span>
    </div>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Create New Budget</h1>
        <p class="text-sm text-gray-500 mt-0.5">Initialize a structural expenditure limit mapping context.</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 w-full" x-data="{
    dateMode: 'month',
    selectedMonth: '{{ old('month') ?? date('Y-m') }}',
    startDate: '{{ old('start_date') ?? date('Y-m-01') }}',
    endDate: '{{ old('end_date') ?? date('Y-m-t') }}',
    updateDates() {
        if(this.dateMode === 'month') {
            const [year, month] = this.selectedMonth.split('-');
            const lastDay = new Date(year, month, 0).getDate();
            this.startDate = `${year}-${month}-01`;
            this.endDate = `${year}-${month}-${lastDay.toString().padStart(2, '0')}`;
        }
    }
}">
    <form action="{{ route('budgets.store') }}" method="POST" class="text-left">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <div class="space-y-6">
                <div>
                    <label for="account_id" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Account Pool</label>
                    <div class="relative">
                        <select name="account_id" id="account_id" required class="w-full text-sm border border-gray-200 rounded-xl p-3 outline-none shadow-sm transition-all text-gray-500 bg-gray-50 opacity-80 cursor-not-allowed" readonly>
                            @foreach($accounts as $account)
                                @if($activeAccountId == $account->id)
                                    <option value="{{ $account->id }}" data-currency="{{ $account->currency }}" selected>
                                        {{ $account->name }} ({{ $account->currency }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <p class="text-xs text-gray-400 mt-2 italic"><i class="fas fa-info-circle mr-1 text-[10px]"></i>To switch account limits, alter the execution context dropdown inside the navigation deck.</p>
                    @error('account_id') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="amount" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Target Volume Pool</label>
                    <div class="relative">
                        <input type="number" name="amount" id="amount" required min="0" step="0.01" value="{{ old('amount') }}"
                               class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary p-3 outline-none shadow-sm transition-all text-gray-700 placeholder-gray-400"
                               placeholder="e.g. 500.00">
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Evaluation balances map explicitly around chosen terminal standard currencies.</p>
                    @error('amount') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Target Class Scopes (Select one or more)</label>
                    <div class="w-full rounded-xl border border-gray-200 shadow-sm h-[244px] overflow-y-auto bg-white p-1 divide-y divide-gray-50">
                        @foreach($categories as $category)
                            <label class="flex items-center p-2.5 hover:bg-gray-50/70 rounded-lg cursor-pointer transition">
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="text-primary focus:ring-primary h-4 w-4 rounded border-gray-300"
                                    {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                                <span class="ml-3 flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs shadow-2xs shrink-0" style="background-color: {{ $category->color ?? '#9ca3af' }}">
                                        <i class="{{ $category->icon ?? 'fas fa-tag' }}"></i>
                                    </span>
                                    <span class="text-sm font-semibold text-gray-700">{{ $category->name }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('category_ids') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">System Duration Mode</label>
                    
                    <div class="flex bg-gray-100 p-1 rounded-xl mb-4">
                        <button type="button" @click="dateMode = 'month'; updateDates()" :class="dateMode === 'month' ? 'bg-white shadow-sm text-primary font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" class="flex-1 py-2.5 text-xs uppercase tracking-wider rounded-lg transition-all duration-200">
                            By Month
                        </button>
                        <button type="button" @click="dateMode = 'custom'" :class="dateMode === 'custom' ? 'bg-white shadow-sm text-primary font-bold' : 'text-gray-500 hover:text-gray-700 font-medium'" class="flex-1 py-2.5 text-xs uppercase tracking-wider rounded-lg transition-all duration-200">
                            Custom Range
                        </button>
                    </div>

                    <div x-show="dateMode === 'month'" x-collapse.duration.300ms class="mb-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="far fa-calendar text-gray-400"></i>
                            </div>
                            <input type="month" x-model="selectedMonth" @change="updateDates"
                                   class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700 cursor-pointer">
                        </div>
                    </div>

                    <div x-show="dateMode === 'custom'" x-collapse.duration.300ms class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-2" style="display: none;">
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Start Execution</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="far fa-calendar-alt text-gray-400"></i>
                                </div>
                                <input type="date" x-model="startDate" 
                                       class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1.5">End Execution</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <i class="far fa-calendar-check text-gray-400"></i>
                                </div>
                                <input type="date" x-model="endDate" 
                                       class="w-full text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary pl-10 p-3 outline-none shadow-sm transition-all text-gray-700">
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="start_date" :value="startDate">
                    <input type="hidden" name="end_date" :value="endDate">
                    
                    @error('start_date') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                    @error('end_date') <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>
            
        </div>

        <div class="mt-8 pt-5 border-t border-gray-100 flex justify-end gap-3">
            <a href="{{ route('budgets.index') }}" class="px-5 py-2.5 bg-white text-gray-600 text-sm font-bold rounded-xl hover:bg-gray-50 transition border border-gray-200 shadow-sm">
                Cancel
            </a>
            <button type="submit" class="bg-primary hover:bg-emerald-600 text-white text-sm font-bold py-2.5 px-5 rounded-xl shadow-sm shadow-emerald-200 transition inline-flex items-center gap-2">
                <i class="fas fa-check text-xs"></i> Save Budget
            </button>
        </div>
    </form>
</div>
@endsection