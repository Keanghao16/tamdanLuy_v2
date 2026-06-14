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

        // 1. Initialize all three financial pools explicitly
        $income = 0;
        $expense = 0;
        $savings = 0;

        foreach ($transactions as $transaction) {
            $type = $this->transactionType($transaction);
            if ($type === 'income') {
                $income += $transaction->amount;
            } elseif ($type === 'saving') {
                $savings += $transaction->amount;
            } else {
                $expense += $transaction->amount;
            }
        }

        // Updated Net Formula: Pure remainder cash flow
        $net = $income - $expense - $savings;
        $reportCurrency = $context['activeAccount']?->currency ?? $transactions->first()?->account?->currency ?? 'USD';

        return view('reports.index', compact(
            'transactions',
            'income',
            'expense',
            'net',
            'savings',
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
        $type = $this->reportType($request); // Now parses income, expense, or saving
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
        $mode = $request->query('mode', session('report_date_mode', 'month'));
        session(['report_date_mode' => $mode]);
        $currentMonth = now()->format('Y-m');

        if ($mode === 'custom') {
            $startDate = $request->query('start_date', session('report_start_date', now()->startOfMonth()->format('Y-m-d')));
            $endDate = $request->query('end_date', session('report_end_date', now()->format('Y-m-d')));

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
                $startDate = session('report_start_date', now()->startOfMonth()->format('Y-m-d'));
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
                $endDate = session('report_end_date', now()->format('Y-m-d'));
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate)) {
                $startDate = now()->startOfMonth()->format('Y-m-d');
            }

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
                $endDate = now()->format('Y-m-d');
            }

            if ($endDate < $startDate) {
                [$startDate, $endDate] = [$endDate, $startDate];
            }

            session([
                'report_start_date' => $startDate,
                'report_end_date' => $endDate,
            ]);

            $currentMonth = \Carbon\Carbon::parse($endDate)->format('Y-m');
        } else {
            $monthParam = $request->query('month', session('report_month', now()->format('Y-m')));

            if (!preg_match('/^\d{4}-\d{2}$/', $monthParam)) {
                $monthParam = session('report_month', now()->format('Y-m'));
            }

            if (!preg_match('/^\d{4}-\d{2}$/', $monthParam)) {
                $monthParam = now()->format('Y-m');
            }

            $date = \Carbon\Carbon::createFromFormat('Y-m', $monthParam);
            $startDate = $date->copy()->startOfMonth()->format('Y-m-d');
            $endDate = $date->copy()->endOfMonth()->format('Y-m-d');
            $currentMonth = $monthParam;

            session(['report_month' => $monthParam]);
        }

        return compact('userId', 'activeAccountId', 'activeAccount', 'mode', 'startDate', 'endDate', 'currentMonth');
    }

    private function fetchTransactions(int $userId, $activeAccountId, string $startDate, string $endDate)
    {
        $query = Transaction::with(['category', 'account'])
            ->where('user_id', $userId)
            ->whereBetween('transaction_date', [$startDate, \Carbon\Carbon::parse($endDate)->endOfDay()->format('Y-m-d H:i:s')]);

        if ($activeAccountId) {
            $query->where('account_id', $activeAccountId);
        }

        return $query->get();
    }

    // 2. Updated to validate and match against three separate types
    private function transactionType($transaction): string
    {
        $type = $transaction->category?->type ?? 'expense';
        return in_array($type, ['income', 'expense', 'saving'], true) ? $type : 'expense';
    }

    // 3. Updated to allow 'saving' as an accepted report view filter parameter
    private function reportType(Request $request): string
    {
        $type = $request->query('type', 'expense');
        return in_array($type, ['income', 'expense', 'saving'], true) ? $type : 'expense';
    }

    // 4. Added support for savings inside historical multi-month datasets
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
                'saving' => 0,
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

            if (!isset($dailyTotals[$dayKey])) {
                continue;
            }

            $dailyTotals[$dayKey]['amount'] += abs((float) $transaction->amount);
            $dailyTotals[$dayKey]['count'] += 1;
        }

        return collect($dailyTotals)
            ->sortKeys()
            ->toArray();
    }

    // 5. Configured dynamic visual context mappings for savings
    private function categoryTotals($transactions, string $type)
    {
        $fallbackColors = [
            'income' => '#10b981',
            'saving' => '#3b82f6',
            'expense' => '#ef4444'
        ];

        $fallbackIcons = [
            'income' => 'fas fa-arrow-down',
            'saving' => 'fas fa-piggy-bank',
            'expense' => 'fas fa-arrow-up'
        ];

        $fallbackColor = $fallbackColors[$type] ?? '#ef4444';
        $fallbackIcon = $fallbackIcons[$type] ?? 'fas fa-arrow-up';

        return $transactions
            ->filter(fn ($transaction) => $this->transactionType($transaction) === $type)
            ->groupBy(fn ($transaction) => $transaction->category?->id ?? 'uncategorized')
            ->map(function ($items) use ($type, $fallbackColor, $fallbackIcon) {
                $first = $items->first();

                return [
                    'key' => (string) ($first->category?->id ?? 'uncategorized'),
                    'name' => $first->category?->name ?? 'Uncategorized',
                    'icon' => $first->category?->icon ?? $fallbackIcon,
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