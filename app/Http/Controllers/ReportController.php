<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController
{
    public function index(Request $request)
    {
        $context = $this->reportContext($request);
        $startDate = $context['startDate'];
        $endDate = $context['endDate'];
        $currentMonth = $context['currentMonth'];
        $mode = $context['mode'];
        $activeAccount = $context['activeAccount'];
        $transactions = $this->fetchTransactions($context['userId'], $context['activeAccountId'], $startDate, $endDate);
        $monthlyTotals = $this->monthlyTotals($transactions, $context['startDate'], $context['endDate']);

        $income = 0;
        $expense = 0;

        foreach ($transactions as $transaction) {
            if ($this->transactionType($transaction) === 'income') {
                $income += $transaction->amount;
            } else {
                $expense += $transaction->amount;
            }
        }

        $net = $income - $expense;
        $reportCurrency = $context['activeAccount']?->currency ?? $transactions->first()?->account?->currency ?? 'USD';

        return view('reports.index', compact(
            'transactions',
            'income',
            'expense',
            'net',
            'monthlyTotals',
            'startDate',
            'endDate',
            'currentMonth',
            'mode',
            'reportCurrency',
            'activeAccount'
        ));
    }

    public function categories(Request $request)
    {
        $context = $this->reportContext($request);
        $startDate = $context['startDate'];
        $endDate = $context['endDate'];
        $currentMonth = $context['currentMonth'];
        $mode = $context['mode'];
        $activeAccount = $context['activeAccount'];
        $type = $this->reportType($request);
        $transactions = $this->fetchTransactions($context['userId'], $context['activeAccountId'], $startDate, $endDate);
        $categories = $this->categoryTotals($transactions, $type);
        $total = $categories->sum('total');
        $reportCurrency = $context['activeAccount']?->currency ?? $transactions->first()?->account?->currency ?? 'USD';

        return view('reports.categories', compact(
            'transactions',
            'categories',
            'total',
            'type',
            'startDate',
            'endDate',
            'currentMonth',
            'mode',
            'reportCurrency',
            'activeAccount'
        ));
    }

    public function ledger(Request $request)
    {
        $context = $this->reportContext($request);
        $startDate = $context['startDate'];
        $endDate = $context['endDate'];
        $currentMonth = $context['currentMonth'];
        $mode = $context['mode'];
        $activeAccount = $context['activeAccount'];
        $type = $this->reportType($request);
        $categoryId = $request->query('category', 'uncategorized') ?: 'uncategorized';
        $transactions = $this->fetchTransactions($context['userId'], $context['activeAccountId'], $startDate, $endDate);

        if ($categoryId === 'uncategorized') {
            $transactions = $transactions->whereNull('category_id');
        } else {
            $transactions = $transactions->where('category_id', $categoryId);
        }

        $transactions = $transactions->sortBy('transaction_date')->values();
        $category = $transactions->first()?->category;
        $dailyTotals = $this->dailyTotals($transactions, $context['startDate'], $context['endDate']);
        $total = $transactions->sum('amount');
        $reportCurrency = $context['activeAccount']?->currency ?? $transactions->first()?->account?->currency ?? 'USD';

        return view('reports.ledger', compact(
            'transactions',
            'category',
            'categoryId',
            'type',
            'total',
            'dailyTotals',
            'startDate',
            'endDate',
            'currentMonth',
            'mode',
            'reportCurrency',
            'activeAccount'
        ));
    }

    private function reportContext(Request $request): array
    {
        $userId = Auth::id() ?? 1;
        $activeAccountId = session('active_account_id');
        $activeAccount = $activeAccountId ? Account::find($activeAccountId) : null;
        $mode = $request->query('mode', 'month');
        $currentMonth = now()->format('Y-m');

        if ($mode === 'custom') {
            $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->query('end_date', now()->format('Y-m-d'));

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
                $startDate = now()->startOfMonth()->format('Y-m-d');
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
                $endDate = now()->format('Y-m-d');
            }

            if ($endDate < $startDate) {
                [$startDate, $endDate] = [$endDate, $startDate];
            }

            $currentMonth = \Carbon\Carbon::parse($endDate)->format('Y-m');
        } else {
            $monthParam = $request->query('month', now()->format('Y-m'));

            if (!preg_match('/^\d{4}-\d{2}$/', $monthParam)) {
                $monthParam = now()->format('Y-m');
            }

            $date = \Carbon\Carbon::createFromFormat('Y-m', $monthParam);
            $startDate = $date->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $date->copy()->endOfMonth()->format('Y-m-d');
            $currentMonth = $monthParam;
        }

        return compact('userId', 'activeAccountId', 'activeAccount', 'mode', 'startDate', 'endDate', 'currentMonth');
    }

    private function fetchTransactions(int $userId, $activeAccountId, string $startDate, string $endDate)
    {
        $query = Transaction::with(['category', 'account'])
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($activeAccountId) {
            $query->where('account_id', $activeAccountId);
        }

        return $query->get();
    }

    private function transactionType($transaction): string
    {
        return ($transaction->category?->type ?? 'expense') === 'income' ? 'income' : 'expense';
    }

    private function reportType(Request $request): string
    {
        $type = $request->query('type', 'expense');

        return in_array($type, ['income', 'expense'], true) ? $type : 'expense';
    }

    private function monthlyTotals($transactions, string $startDate, string $endDate): array
    {
        $monthlyTotals = [];
        $chartEndMonth = \Carbon\Carbon::parse($endDate)->startOfMonth();
        $chartStartMonth = $chartEndMonth->copy()->subMonths(5);
        $periodStartMonth = \Carbon\Carbon::parse($startDate)->startOfMonth();

        if ($periodStartMonth->gt($chartStartMonth)) {
            $chartStartMonth = $periodStartMonth->copy();
        }

        $cursor = $chartStartMonth->copy();
        while ($cursor->lte($chartEndMonth)) {
            $key = $cursor->format('Y-m');
            $monthlyTotals[$key] = [
                'income' => 0,
                'expense' => 0,
            ];
            $cursor->addMonth();
        }

        foreach ($transactions as $transaction) {
            $month = $transaction->transaction_date->format('Y-m');
            $type = $this->transactionType($transaction);

            if (isset($monthlyTotals[$month])) {
                $monthlyTotals[$month][$type] += $transaction->amount;
            }
        }

        return $monthlyTotals;
    }

    private function dailyTotals($transactions, string $startDate, string $endDate): array
    {
        $dailyTotals = [];
        $dailyEnd = \Carbon\Carbon::parse($endDate)->endOfDay();
        $dailyStart = \Carbon\Carbon::parse($startDate)->startOfDay();

        if ($dailyStart->diffInDays($dailyEnd) > 29) {
            $dailyStart = $dailyEnd->copy()->subDays(29)->startOfDay();
        }

        $dailyCursor = $dailyStart->copy();
        while ($dailyCursor->lte($dailyEnd)) {
            $dayKey = $dailyCursor->format('Y-m-d');
            $dailyTotals[$dayKey] = [
                'amount' => 0,
                'count' => 0,
            ];
            $dailyCursor->addDay();
        }

        foreach ($transactions as $transaction) {
            $dayKey = $transaction->transaction_date->format('Y-m-d');

            if (isset($dailyTotals[$dayKey])) {
                $dailyTotals[$dayKey]['amount'] += $transaction->amount;
                $dailyTotals[$dayKey]['count'] += 1;
            }
        }

        return $dailyTotals;
    }

    private function categoryTotals($transactions, string $type)
    {
        $fallbackColor = $type === 'income' ? '#10b981' : '#ef4444';

        return $transactions
            ->filter(fn ($transaction) => $this->transactionType($transaction) === $type)
            ->groupBy(fn ($transaction) => $transaction->category?->id ?? 'uncategorized')
            ->map(function ($items) use ($type, $fallbackColor) {
                $first = $items->first();

                return [
                    'key' => (string) ($first->category?->id ?? 'uncategorized'),
                    'name' => $first->category?->name ?? 'Uncategorized',
                    'icon' => $first->category?->icon ?? ($type === 'income' ? 'fas fa-arrow-down' : 'fas fa-arrow-up'),
                    'color' => $first->category?->color ?? $fallbackColor,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                    'type' => $type,
                ];
            })
            ->sortByDesc('total')
            ->values();
    }
}
