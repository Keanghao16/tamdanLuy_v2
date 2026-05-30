<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Account;
use App\Models\Category;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController
{
    public function dashboard(Request $request)
    {
        $userId = Auth::id() ?? 1; // Fallback to 1 for quick dev testing
        $activeAccountId = session('active_account_id');

        $activeAccount = null;
        if ($activeAccountId) {
            $activeAccount = Account::find($activeAccountId);
        }

        // Check if there is an active account, otherwise get totals
        $accountQuery = Account::where('user_id', $userId);
        if ($activeAccountId) {
            $accountQuery->where('id', $activeAccountId);
        }
        $totalBalance = $accountQuery->sum('current_balance');
        
        $transactionQuery = Transaction::where('user_id', $userId);
        if ($activeAccountId) {
            $transactionQuery->where('account_id', $activeAccountId);
        }

        $dateMode = $request->query('mode', session('dashboard_date_mode', 'month'));
        session(['dashboard_date_mode' => $dateMode]);
        
        if ($dateMode === 'custom') {
            $startDate = $request->query('start_date', session('dashboard_start_date', now()->startOfMonth()->format('Y-m-d')));
            $endDate = $request->query('end_date', session('dashboard_end_date', now()->endOfMonth()->format('Y-m-d')));
            
            session([
                'dashboard_start_date' => $startDate,
                'dashboard_end_date' => $endDate
            ]);

            $transactionQuery->whereDate('transaction_date', '>=', $startDate)
                             ->whereDate('transaction_date', '<=', $endDate);
                             
            $currentMonth = session('dashboard_month', now()->format('Y-m'));
        } else {
            $monthParam = $request->query('month', session('dashboard_month', now()->format('Y-m')));
            session(['dashboard_month' => $monthParam]);
            
            $date = \Carbon\Carbon::createFromFormat('Y-m', $monthParam);
            $startDate = $date->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $date->copy()->endOfMonth()->format('Y-m-d');
            $currentMonth = $monthParam;
            
            $transactionQuery->whereYear('transaction_date', $date->year)
                             ->whereMonth('transaction_date', $date->month);
        }

        $thisMonthIncome = (clone $transactionQuery)
            ->whereHas('category', function($q) {
                $q->where('type', 'income');
            })
            ->sum('amount');
            
        $thisMonthExpense = (clone $transactionQuery)
            ->whereHas('category', function($q) {
                $q->where('type', 'expense');
            })
            ->sum('amount');
            
        $transactions = (clone $transactionQuery)->with(['account', 'category'])
            ->latest('transaction_date')
            ->get();
            
        // Group transactions by Date
        $groupedTransactions = $transactions->groupBy(function($tx) {
            return $tx->transaction_date->format('Y-m-d');
        });

        return view('dashboard', compact(
            'totalBalance', 
            'thisMonthIncome', 
            'thisMonthExpense', 
            'groupedTransactions',
            'dateMode',
            'currentMonth',
            'startDate',
            'endDate',
            'activeAccount'
        ));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id() ?? 1;
        $activeAccountId = session('active_account_id');

        $query = Transaction::with(['account', 'category'])
            ->where('user_id', $userId);

        if ($activeAccountId) {
            $query->where('account_id', $activeAccountId);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->get();

        return view('transactions.index', compact('transactions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = Auth::id() ?? 1;
        $accounts = Account::where('user_id', $userId)->get();
        $categories = Category::where(function($q) use ($userId) {
            $q->where('user_id', $userId)->orWhereNull('user_id');
        })->get();

        return view('transactions.create', compact('accounts', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {
        $userId = Auth::id() ?? 1;
        
        $validated = $request->validated();
        $validated['user_id'] = $userId;
        
        $transaction = Transaction::create($validated);
        
        // Update account balance
        $account = Account::find($validated['account_id']);
        $category = Category::find($validated['category_id']);
        if ($account && $category) {
            if ($category->type === 'income') {
                $account->current_balance += $validated['amount'];
            } else {
                $account->current_balance -= $validated['amount'];
            }
            $account->save();
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        $userId = Auth::id() ?? 1;
        abort_if($transaction->user_id !== $userId, 403);
        
        $accounts = Account::where('user_id', $userId)->get();
        $categories = Category::where(function($q) use ($userId) {
            $q->where('user_id', $userId)->orWhereNull('user_id');
        })->get();

        return view('transactions.edit', compact('transaction', 'accounts', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionRequest $request, Transaction $transaction)
    {
        $userId = Auth::id() ?? 1;
        abort_if($transaction->user_id !== $userId, 403);

        $validated = $request->validated();

        // Revert old impact
        $oldAccount = Account::find($transaction->account_id);
        $oldCategory = Category::find($transaction->category_id);
        if ($oldAccount && $oldCategory) {
            if ($oldCategory->type === 'income') {
                $oldAccount->current_balance -= $transaction->amount;
            } else {
                $oldAccount->current_balance += $transaction->amount;
            }
            $oldAccount->save();
        }

        // Update transaction
        $transaction->update($validated);

        // Apply new impact
        $newAccount = Account::find($validated['account_id']);
        $newCategory = Category::find($validated['category_id']);
        if ($newAccount && $newCategory) {
            if ($newCategory->type === 'income') {
                $newAccount->current_balance += $validated['amount'];
            } else {
                $newAccount->current_balance -= $validated['amount'];
            }
            $newAccount->save();
        }

        return redirect()->route('transactions.index')->with('success', 'Transaction updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $userId = Auth::id() ?? 1;
        abort_if($transaction->user_id !== $userId, 403);

        // Revert impact before deleting
        $account = Account::find($transaction->account_id);
        $category = Category::find($transaction->category_id);
        if ($account && $category) {
            if ($category->type === 'income') {
                $account->current_balance -= $transaction->amount;
            } else {
                $account->current_balance += $transaction->amount;
            }
            $account->save();
        }

        $transaction->delete();

        return redirect()->route('transactions.index')->with('success', 'Transaction deleted.');
    }
}
