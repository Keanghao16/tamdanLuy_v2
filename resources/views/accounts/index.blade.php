<!-- Accounts index view -->

@extends('layouts.app')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Accounts</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage and organize your physical checking, savings, or digital vault structures.</p>
    </div>
    <a href="{{ route('accounts.create') }}" class="bg-primary hover:bg-emerald-600 text-white text-sm font-bold py-2.5 px-4 rounded-xl shadow-sm shadow-emerald-100 transition inline-flex items-center gap-2">
        <i class="fas fa-plus text-xs"></i> New Account
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($accounts as $account)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md hover:border-gray-200/80 transition-all duration-200 relative group">
            <a href="{{ route('accounts.edit', $account) }}" class="absolute top-5 right-5 text-gray-400 hover:text-primary opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                <i class="fas fa-edit text-base"></i>
            </a>
            <div>
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-lg text-white shadow-2xs" style="background-color: {{ $account->color ?? '#3b82f6' }}">
                        <i class="{{ $account->icon ?? 'fas fa-wallet' }}"></i>
                    </div>
                    <span class="px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider rounded-lg bg-gray-50 border border-gray-100 text-gray-500 shadow-3xs">
                        {{ $account->currency }}
                    </span>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1 pr-8 tracking-tight">{{ $account->name }}</h3>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-50 flex justify-between items-end">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Balance</span>
                <span class="text-2xl font-black tracking-tight {{ $account->current_balance < 0 ? 'text-red-500' : 'text-gray-900' }}">
                    @if($account->currency === 'USD')
                        ${{ number_format($account->current_balance, 2) }}
                    @else
                        {{ number_format($account->current_balance, 0) }} ៛
                    @endif
                </span>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl border border-gray-50">
                <i class="fas fa-wallet"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">No accounts yet</h3>
            <p class="text-sm text-gray-500 mb-5">Create your first financial account node to begin populating tracking channels.</p>
            <a href="{{ route('accounts.create') }}" class="text-primary text-sm font-bold hover:underline inline-flex items-center gap-1">
                Add Account <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>
    @endforelse
</div>
@endsection