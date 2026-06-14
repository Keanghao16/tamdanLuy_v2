<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use Illuminate\Support\Facades\Auth;

class BudgetController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id() ?? 1;
        $activeAccountId = session('active_account_id');

        $query = Budget::with(['categories', 'account'])->where('user_id', $userId);
        
        if ($activeAccountId) {
            $query->where('account_id', $activeAccountId);
        }

        $budgets = $query->get();

        // Calculate spent amount for each budget using transactions
        foreach ($budgets as $budget) {
            $categoryIds = $budget->categories->pluck('id')->toArray();
            
            $budget->spent = \App\Models\Transaction::where('user_id', $userId)
                ->where('account_id', $budget->account_id)
                ->whereIn('category_id', $categoryIds)
                ->whereBetween('transaction_date', [$budget->start_date, $budget->end_date])
                ->sum('amount');
        }

        return view('budgets.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userId = Auth::id() ?? 1;
        $categories = Category::where(function($q) use ($userId) {
            $q->where('user_id', $userId)->orWhereNull('user_id');
        })->where('type', 'expense')->get();
        
        $accounts = \App\Models\Account::where('user_id', $userId)->get();
        return view('budgets.create', compact('categories', 'accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBudgetRequest $request)
    {
        $userId = Auth::id() ?? 1;
        $validated = $request->validated();
        
        $budgetData = collect($validated)->except('category_ids')->toArray();
        $budgetData['user_id'] = $userId;

        $budget = Budget::create($budgetData);
        $budget->categories()->attach($validated['category_ids']);

        return redirect()->route('budgets.index')->with('success', 'Budget created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Budget $budget)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Budget $budget)
    {
        $userId = Auth::id() ?? 1;
        abort_if($budget->user_id !== $userId, 403);

        $categories = Category::where(function($q) use ($userId) {
            $q->where('user_id', $userId)->orWhereNull('user_id');
        })->where('type', 'expense')->get();
        
        $accounts = \App\Models\Account::where('user_id', $userId)->get();
        
        return view('budgets.edit', compact('budget', 'categories', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBudgetRequest $request, Budget $budget)
    {
        $userId = Auth::id() ?? 1;
        abort_if($budget->user_id !== $userId, 403);

        $validated = $request->validated();
        
        $budgetData = collect($validated)->except('category_ids')->toArray();
        $budget->update($budgetData);
        
        $budget->categories()->sync($validated['category_ids']);

        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Budget $budget)
    {
        $userId = Auth::id() ?? 1;
        abort_if($budget->user_id !== $userId, 403);

        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget deleted successfully.');
    }
}
