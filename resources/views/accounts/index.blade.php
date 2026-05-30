@extends('layouts.app')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-900">Accounts</h1>
    <a href="{{ route('accounts.create') }}" class="bg-primary hover:bg-emerald-600 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition">
        <i class="fas fa-plus mr-2"></i>New Account
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($accounts as $account)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col justify-between hover:shadow-md transition relative group">
            <a href="{{ route('accounts.edit', $account) }}" class="absolute top-4 right-4 text-gray-400 hover:text-primary opacity-0 group-hover:opacity-100 transition-opacity">
                <i class="fas fa-edit text-lg"></i>
            </a>
            <div>
                <div class="flex justify-start items-start mb-4 gap-3">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center text-xl text-white shadow-sm" style="background-color: {{ $account->color ?? '#3b82f6' }}">
                        <i class="{{ $account->icon ?? 'fas fa-wallet' }}"></i>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                        {{ $account->currency }}
                    </span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1 pr-8">{{ $account->name }}</h3>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100 flex justify-between items-end">
                <span class="text-sm font-medium text-gray-500">Balance</span>
                <span class="text-2xl font-bold {{ $account->current_balance < 0 ? 'text-red-500' : 'text-gray-900' }}">
                    @if($account->currency === 'USD')
                        ${{ number_format($account->current_balance, 2) }}
                    @else
                        {{ number_format($account->current_balance, 0) }} ៛
                    @endif
                </span>
            </div>
        </div>
    @empty
        <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-100 p-10 text-center">
            <div class="w-16 h-16 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-wallet"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No accounts yet</h3>
            <p class="text-gray-500 mb-4">Create your first account to start tracking transactions.</p>
            <a href="{{ route('accounts.create') }}" class="text-primary font-medium hover:underline">Add Account</a>
        </div>
    @endforelse
</div>
@endsection