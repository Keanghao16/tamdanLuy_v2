<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Http\Request;

class AccountController
{
    /**
     * Switch the globally active account for the user.
     */
    public function switchAccount(Request $request)
    {
        $validated = $request->validate([
            'account_id' => 'required|exists:accounts,id',
        ]);

        session(['active_account_id' => $validated['account_id']]);

        return back();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
        $accounts = Account::where('user_id', $userId)->get();
        return view('accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAccountRequest $request)
    {
        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
        
        $validated = $request->validated();
        $validated['user_id'] = $userId;
        
        if (!isset($validated['current_balance'])) {
            $validated['current_balance'] = 0;
        }
        
        Account::create($validated);
        
        return redirect()->route('accounts.index')->with('success', 'Account created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
        abort_if($account->user_id !== $userId, 403);
        
        return view('accounts.edit', compact('account'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAccountRequest $request, Account $account)
    {
        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
        abort_if($account->user_id !== $userId, 403);

        $validated = $request->validated();
        
        $account->update($validated);
        
        return redirect()->route('accounts.index')->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account)
    {
        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
        abort_if($account->user_id !== $userId, 403);

        $account->delete();

        return redirect()->route('accounts.index')->with('success', 'Account deleted successfully.');
    }
}
