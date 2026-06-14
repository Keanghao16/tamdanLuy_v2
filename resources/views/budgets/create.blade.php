@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-2">
        <a href="{{ route('budgets.index') }}" class="hover:text-primary">Budgets</a>
        <span>/</span>
        <span class="text-gray-900 font-medium">New Budget</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Create New Budget</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl" x-data="{
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
    <form action="{{ route('budgets.store') }}" method="POST">
        @csrf
        
        <div class="space-y-5">
            <div>
                <label for="account_id" class="block text-sm font-medium text-gray-700 mb-1">Account</label>
                <select name="account_id" id="account_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50 text-gray-500 bg-gray-50" readonly>
                    @foreach($accounts as $account)
                        @if($activeAccountId == $account->id)
                            <option value="{{ $account->id }}" data-currency="{{ $account->currency }}" selected>
                                {{ $account->name }} ({{ $account->currency }})
                            </option>
                        @endif
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-1">To change account, use the dropdown in the navigation bar.</p>
                @error('account_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Categories (Select one or more scope)</label>
                <div class="w-full rounded-lg border border-gray-300 shadow-sm max-h-48 overflow-y-auto bg-white p-2">
                    @foreach($categories as $category)
                        <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer transition">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" class="text-primary focus:ring-primary h-4 w-4 rounded border-gray-300"
                                {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                            <span class="ml-3 flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center text-white text-xs" style="background-color: {{ $category->color ?? '#9ca3af' }}">
                                    <i class="{{ $category->icon ?? 'fas fa-tag' }}"></i>
                                </span>
                                <span class="text-sm text-gray-700">{{ $category->name }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('category_ids') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                <div class="flex space-x-4 mb-3">
                    <label class="inline-flex items-center">
                        <input type="radio" x-model="dateMode" value="month" class="text-primary focus:ring-primary h-4 w-4" @change="updateDates">
                        <span class="ml-2 text-gray-700">Specific Month</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" x-model="dateMode" value="custom" class="text-primary focus:ring-primary h-4 w-4">
                        <span class="ml-2 text-gray-700">Custom Date Range</span>
                    </label>
                </div>

                <div x-show="dateMode === 'month'" x-transition class="mb-4">
                    <input type="month" x-model="selectedMonth" @change="updateDates"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>

                <div x-show="dateMode === 'custom'" x-transition class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Start Date (Custom)</label>
                        <input type="date" x-model="startDate" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">End Date (Custom)</label>
                        <input type="date" x-model="endDate" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    </div>
                </div>
                
                <!-- Hidden inputs to submit actual dates derived from Alpine State -->
                <input type="hidden" name="start_date" :value="startDate">
                <input type="hidden" name="end_date" :value="endDate">
                
                @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                <input type="number" name="amount" id="amount" required min="0" step="0.01" value="{{ old('amount') }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                       placeholder="e.g. 500.00">
                <p class="text-xs text-gray-500 mt-1">This will be calculated using the selected account's currency.</p>
                @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
        </div>

        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('budgets.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition shadow-sm">
                Cancel
            </a>
            <button type="submit" class="bg-primary hover:bg-emerald-600 text-white px-5 py-2.5 rounded-lg font-medium shadow-sm transition inline-flex items-center">
                <span>Save Budget</span>
            </button>
        </div>
    </form>
</div>
@endsection