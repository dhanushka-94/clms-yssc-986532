<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\TransactionsExport;
use App\Models\Category;
use App\Models\Player;
use App\Models\Member;
use App\Models\Staff;
use App\Models\Sponsor;
use App\Exports\BankAccountsExport;

class ReportController extends Controller
{
    public function index(): View
    {
        // Get summary statistics
        $totalIncome = FinancialTransaction::where('type', 'income')
            ->where('status', 'completed')
            ->sum('amount');

        $totalExpenses = FinancialTransaction::where('type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');

        $netBalance = $totalIncome - $totalExpenses;

        $recentTransactions = FinancialTransaction::with(['bankAccount', 'transactionable'])
            ->latest('transaction_date')
            ->take(5)
            ->get();

        return view('reports.index', compact(
            'totalIncome',
            'totalExpenses',
            'netBalance',
            'recentTransactions'
        ));
    }

    public function transactions(Request $request): View
    {
        $query = FinancialTransaction::with(['bankAccount', 'transactionable']);
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        // Get summary data
        $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
        $totalExpenses = (clone $query)->where('type', 'expense')->sum('amount');
        $pendingTransactions = (clone $query)->where('status', 'pending')->count();

        $transactions = $query->latest('transaction_date')->paginate(15);
        $bankAccounts = BankAccount::all();
        $categories = Category::active()
            ->when($request->type, function($query) use ($request) {
                return $query->ofType($request->type);
            })
            ->orderBy('name')
            ->get();
        
        return view('reports.transactions', compact(
            'transactions',
            'bankAccounts',
            'categories',
            'totalIncome',
            'totalExpenses',
            'pendingTransactions'
        ));
    }

    public function income(Request $request): View
    {
        // Get income categories first
        $categories = Category::where('type', 'income')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $query = FinancialTransaction::with(['bankAccount', 'transactionable'])
            ->where('type', 'income')
            ->where('status', 'completed');
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        $transactions = $query->latest('transaction_date')->paginate(15);
        
        // Get income statistics
        $totalIncome = $query->sum('amount');
        $monthlyIncome = $query->whereMonth('transaction_date', Carbon::now()->month)->sum('amount');
        $yearlyIncome = $query->whereYear('transaction_date', Carbon::now()->year)->sum('amount');
        
        // Get monthly data for trend chart - ensure we get data for all months in range
        $monthlyData = FinancialTransaction::where('type', 'income')
            ->where('status', 'completed')
            ->when($request->filled('date_from'), function($q) use ($request) {
                return $q->whereDate('transaction_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($q) use ($request) {
                return $q->whereDate('transaction_date', '<=', $request->date_to);
            })
            ->select(
                DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as month'),
                DB::raw('COALESCE(SUM(amount), 0) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('DATE_FORMAT(transaction_date, "%Y-%m")'))
            ->orderBy('month')
            ->get();

        // If no date range is specified, get last 12 months
        if (!$request->filled('date_from') && !$request->filled('date_to')) {
            $startDate = Carbon::now()->subMonths(11)->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            
            // Create a collection of all months in range
            $allMonths = collect();
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                $monthKey = $currentDate->format('Y-m');
                if (!$monthlyData->contains('month', $monthKey)) {
                    $allMonths->push([
                        'month' => $monthKey,
                        'total' => 0,
                        'count' => 0
                    ]);
                } else {
                    $monthData = $monthlyData->firstWhere('month', $monthKey);
                    $allMonths->push($monthData);
                }
                $currentDate->addMonth();
            }
            
            $monthlyData = $allMonths->sortBy('month')->values();
        }

        // Get category data for pie chart
        $categoryData = FinancialTransaction::where('type', 'income')
            ->where('status', 'completed')
            ->when($request->filled('date_from'), function($q) use ($request) {
                return $q->whereDate('transaction_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function($q) use ($request) {
                return $q->whereDate('transaction_date', '<=', $request->date_to);
            })
            ->select(
                'category',
                DB::raw('COALESCE(SUM(amount), 0) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Get bank accounts for filter
        $bankAccounts = BankAccount::all();
        
        return view('reports.income', compact(
            'transactions',
            'totalIncome',
            'monthlyIncome',
            'yearlyIncome',
            'monthlyData',
            'categoryData',
            'bankAccounts',
            'categories'
        ));
    }

    public function expenses(Request $request): View
    {
        // Get expense categories first
        $categories = Category::where('type', 'expense')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $query = FinancialTransaction::with(['bankAccount', 'transactionable'])
            ->where('type', 'expense');
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        $transactions = $query->latest('transaction_date')->paginate(15);
        
        // Get expense statistics
        $totalExpenses = $query->sum('amount');
        $monthlyExpenses = $query->whereMonth('transaction_date', Carbon::now()->month)->sum('amount');
        $yearlyExpenses = $query->whereYear('transaction_date', Carbon::now()->year)->sum('amount');
        
        // Get monthly data for trend chart
        $monthlyData = FinancialTransaction::where('type', 'expense')
            ->select(
                DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get category data for pie chart
        $categoryData = FinancialTransaction::where('type', 'expense')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
            ->get();

        // Get bank accounts for filter
        $bankAccounts = BankAccount::all();
        
        return view('reports.expenses', compact(
            'transactions',
            'totalExpenses',
            'monthlyExpenses',
            'yearlyExpenses',
            'monthlyData',
            'categoryData',
            'bankAccounts',
            'categories'
        ));
    }

    public function entities(Request $request): View
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->get('date_to', Carbon::now());

        // Define entity types and their display names
        $entityTypes = [
            'App\\Models\\Player' => 'Players',
            'App\\Models\\Staff' => 'Staff',
            'App\\Models\\Member' => 'Members',
            'App\\Models\\Sponsor' => 'Sponsors',
            'player' => 'Players',
            'staff' => 'Staff',
            'member' => 'Members',
            'sponsor' => 'Sponsors'
        ];

        // Get income distribution
        $incomeDistribution = FinancialTransaction::where('type', 'income')
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->whereNotNull('transactionable_type')
            ->select(
                'transactionable_type',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('transactionable_type')
            ->get()
            ->map(function ($item) use ($entityTypes) {
                $item->display_name = $entityTypes[$item->transactionable_type] ?? $item->transactionable_type;
                return $item;
            });

        // Get expense distribution
        $expenseDistribution = FinancialTransaction::where('type', 'expense')
            ->where('status', 'completed')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->whereNotNull('transactionable_type')
            ->select(
                'transactionable_type',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('transactionable_type')
            ->get()
            ->map(function ($item) use ($entityTypes) {
                $item->display_name = $entityTypes[$item->transactionable_type] ?? $item->transactionable_type;
                return $item;
            });

        // Calculate totals
        $totalIncome = $incomeDistribution->sum('total_amount');
        $totalExpenses = $expenseDistribution->sum('total_amount');

        // Calculate percentages
        $incomeDistribution = $incomeDistribution->map(function ($item) use ($totalIncome) {
            $item->percentage = $totalIncome > 0 ? ($item->total_amount / $totalIncome) * 100 : 0;
            return $item;
        });

        $expenseDistribution = $expenseDistribution->map(function ($item) use ($totalExpenses) {
            $item->percentage = $totalExpenses > 0 ? ($item->total_amount / $totalExpenses) * 100 : 0;
            return $item;
        });

        // Group similar entities (combine full class name and short name)
        $incomeDistribution = $this->groupSimilarEntities($incomeDistribution);
        $expenseDistribution = $this->groupSimilarEntities($expenseDistribution);

        return view('reports.entities', compact(
            'incomeDistribution',
            'expenseDistribution',
            'totalIncome',
            'totalExpenses',
            'dateFrom',
            'dateTo'
        ));
    }

    private function groupSimilarEntities($distribution)
    {
        $grouped = collect();
        $mapping = [
            'App\\Models\\Player' => 'Players',
            'App\\Models\\Staff' => 'Staff',
            'App\\Models\\Member' => 'Members',
            'App\\Models\\Sponsor' => 'Sponsors',
            'player' => 'Players',
            'staff' => 'Staff',
            'member' => 'Members',
            'sponsor' => 'Sponsors'
        ];

        foreach ($mapping as $type => $displayName) {
            $items = $distribution->filter(function ($item) use ($type, $mapping) {
                return $item->transactionable_type === $type || 
                       $mapping[$item->transactionable_type] === $mapping[$type];
            });

            if ($items->isNotEmpty()) {
                $grouped->push((object)[
                    'display_name' => $displayName,
                    'transaction_count' => $items->sum('transaction_count'),
                    'total_amount' => $items->sum('total_amount'),
                    'percentage' => $items->sum('percentage')
                ]);
            }
        }

        return $grouped->sortByDesc('total_amount')->values();
    }

    public function bankAccounts(Request $request): View
    {
        $bankAccounts = BankAccount::select([
            'bank_accounts.*',
            DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM financial_transactions 
                WHERE financial_transactions.bank_account_id = bank_accounts.id 
                AND type = "income" 
                AND status = "completed") as total_income'),
            DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM financial_transactions 
                WHERE financial_transactions.bank_account_id = bank_accounts.id 
                AND type = "expense" 
                AND status = "completed") as total_expenses')
        ])->get();
        
        return view('reports.bank-accounts', compact('bankAccounts'));
    }

    public function categorySummary(Request $request): View
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth());
        $dateTo = $request->get('date_to', Carbon::now());

        // Get income categories summary
        $incomeCategories = FinancialTransaction::where('type', 'income')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->select(
                'category',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();

        // Get expense categories summary
        $expenseCategories = FinancialTransaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->select(
                'category',
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('category')
            ->orderByDesc('total_amount')
            ->get();

        // Calculate totals
        $totalIncome = $incomeCategories->sum('total_amount');
        $totalExpenses = $expenseCategories->sum('total_amount');

        return view('reports.category-summary', compact(
            'incomeCategories',
            'expenseCategories',
            'totalIncome',
            'totalExpenses',
            'dateFrom',
            'dateTo'
        ));
    }

    public function exportPdf(Request $request)
    {
        try {
            if ($request->report_type === 'transactions') {
                // Get filtered transactions
        $query = FinancialTransaction::with(['bankAccount', 'transactionable']);
        
        // Apply filters
        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

                // Get transactions and summary data
                $transactions = $query->latest('transaction_date')->get();
                $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
                $totalExpenses = (clone $query)->where('type', 'expense')->sum('amount');
                $pendingTransactions = (clone $query)->where('status', 'pending')->count();

                $data = [
                    'transactions' => $transactions,
                    'totalIncome' => $totalIncome,
                    'totalExpenses' => $totalExpenses,
                    'pendingTransactions' => $pendingTransactions
                ];

                $pdf = PDF::loadView('reports.exports.transactions-pdf', $data);
                return $pdf->download('transactions_report_' . now()->format('Y-m-d') . '.pdf');
            } elseif ($request->report_type === 'individual') {
                // Get the model based on type
                $modelClass = match ($request->model_type) {
                    'player' => Player::class,
                    'staff' => Staff::class,
                    'member' => Member::class,
                    'sponsor' => Sponsor::class,
                    default => throw new \Exception('Invalid model type')
                };

                $model = $modelClass::findOrFail($request->model_id);

                // Get transactions for the date range
                $query = FinancialTransaction::where(function($q) use ($model, $request) {
                    $q->where('transactionable_type', get_class($model))
                      ->orWhere('transactionable_type', $request->model_type);
                })
                ->where('transactionable_id', $model->id);

                if ($request->filled('date_from')) {
                    $query->whereDate('transaction_date', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $query->whereDate('transaction_date', '<=', $request->date_to);
                }

                // Get transactions
        $transactions = $query->latest('transaction_date')->get();
        
                // Calculate totals using a separate query
                $totals = DB::table('financial_transactions')
                    ->where(function($q) use ($model, $request) {
                        $q->where('transactionable_type', get_class($model))
                          ->orWhere('transactionable_type', $request->model_type);
                    })
                    ->where('transactionable_id', $model->id)
                    ->when($request->filled('date_from'), function($q) use ($request) {
                        $q->whereDate('transaction_date', '>=', $request->date_from);
                    })
                    ->when($request->filled('date_to'), function($q) use ($request) {
                        $q->whereDate('transaction_date', '<=', $request->date_to);
                    })
                    ->selectRaw('
                        SUM(CASE WHEN type = "income" AND status = "completed" THEN amount ELSE 0 END) as total_income,
                        SUM(CASE WHEN type = "expense" AND status = "completed" THEN amount ELSE 0 END) as total_expenses,
                        SUM(CASE WHEN type = "income" THEN 1 ELSE 0 END) as income_count,
                        SUM(CASE WHEN type = "expense" THEN 1 ELSE 0 END) as expense_count
                    ')
                    ->first();

                $data = [
                    'model' => $model,
                    'type' => $request->model_type,
                    'transactions' => $transactions,
                    'date_from' => $request->date_from,
                    'date_to' => $request->date_to,
                    'total_income' => $totals->total_income ?? 0,
                    'total_expenses' => $totals->total_expenses ?? 0,
                    'income_count' => $totals->income_count ?? 0,
                    'expense_count' => $totals->expense_count ?? 0
                ];

                \Log::info('PDF Export Data', [
                    'date_range' => [$request->date_from, $request->date_to],
                    'totals' => $totals
                ]);

                $pdf = PDF::loadView('reports.exports.individual-finances-pdf', $data);
                
                // Generate appropriate filename based on model type
                $filename = match ($request->model_type) {
                    'sponsor' => $model->name,
                    default => $model->first_name . '_' . $model->last_name
                };
                
                return $pdf->download($filename . '_financial_report.pdf');
            } elseif ($request->report_type === 'bank-accounts') {
                $dateFrom = $request->get('date_from');
                $dateTo = $request->get('date_to');

                $bankAccounts = BankAccount::select([
                    'bank_accounts.*',
                    DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM financial_transactions 
                        WHERE financial_transactions.bank_account_id = bank_accounts.id 
                        AND type = "income" 
                        AND status = "completed"
                        ' . ($dateFrom ? 'AND transaction_date >= "' . $dateFrom . '"' : '') . '
                        ' . ($dateTo ? 'AND transaction_date <= "' . $dateTo . '"' : '') . '
                        ) as total_income'),
                    DB::raw('(SELECT COALESCE(SUM(amount), 0) FROM financial_transactions 
                        WHERE financial_transactions.bank_account_id = bank_accounts.id 
                        AND type = "expense" 
                        AND status = "completed"
                        ' . ($dateFrom ? 'AND transaction_date >= "' . $dateFrom . '"' : '') . '
                        ' . ($dateTo ? 'AND transaction_date <= "' . $dateTo . '"' : '') . '
                        ) as total_expenses')
                ])->get();

                $data = [
                    'bankAccounts' => $bankAccounts,
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo
                ];

                $pdf = PDF::loadView('reports.exports.bank-accounts-pdf', $data);
                return $pdf->download('bank_accounts_report_' . now()->format('Y-m-d') . '.pdf');
            } elseif ($request->report_type === 'income') {
                // Get filtered income transactions
                $query = FinancialTransaction::with(['bankAccount', 'transactionable'])
                    ->where('type', 'income')
                    ->where('status', 'completed');
                
                // Apply filters
                if ($request->filled('date_from')) {
                    $query->whereDate('transaction_date', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $query->whereDate('transaction_date', '<=', $request->date_to);
                }
                if ($request->filled('category')) {
                    $query->where('category', $request->category);
                }
                if ($request->filled('payment_method')) {
                    $query->where('payment_method', $request->payment_method);
                }
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
                if ($request->filled('bank_account_id')) {
                    $query->where('bank_account_id', $request->bank_account_id);
                }

                // Get transactions and summary data
                $transactions = $query->latest('transaction_date')->get();
                $totalIncome = $query->sum('amount');
                $monthlyIncome = $query->whereMonth('transaction_date', Carbon::now()->month)->sum('amount');
                $yearlyIncome = $query->whereYear('transaction_date', Carbon::now()->year)->sum('amount');

                // Get category summary
                $categoryData = $query->select(
                    'category',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('category')
                ->orderByDesc('total')
                ->get();

                $data = [
                    'transactions' => $transactions,
                    'totalIncome' => $totalIncome,
                    'monthlyIncome' => $monthlyIncome,
                    'yearlyIncome' => $yearlyIncome,
                    'categoryData' => $categoryData,
                    'filters' => [
                        'date_from' => $request->date_from,
                        'date_to' => $request->date_to,
                        'category' => $request->category,
                        'payment_method' => $request->payment_method,
                        'status' => $request->status,
                        'bank_account_id' => $request->bank_account_id
                    ]
                ];

                $pdf = PDF::loadView('reports.exports.income-pdf', $data);
                return $pdf->download('income_report_' . now()->format('Y-m-d') . '.pdf');
            } elseif ($request->report_type === 'expenses') {
                // Get filtered expense transactions
                $query = FinancialTransaction::with(['bankAccount', 'transactionable'])
                    ->where('type', 'expense')
                    ->where('status', 'completed');
                
                // Apply filters
                if ($request->filled('date_from')) {
                    $query->whereDate('transaction_date', '>=', $request->date_from);
                }
                if ($request->filled('date_to')) {
                    $query->whereDate('transaction_date', '<=', $request->date_to);
                }
                if ($request->filled('category')) {
                    $query->where('category', $request->category);
                }
                if ($request->filled('payment_method')) {
                    $query->where('payment_method', $request->payment_method);
                }
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                }
                if ($request->filled('bank_account_id')) {
                    $query->where('bank_account_id', $request->bank_account_id);
                }

                // Get transactions and summary data
                $transactions = $query->latest('transaction_date')->get();
                $totalExpenses = $query->sum('amount');
                $monthlyExpenses = $query->whereMonth('transaction_date', Carbon::now()->month)->sum('amount');
                $yearlyExpenses = $query->whereYear('transaction_date', Carbon::now()->year)->sum('amount');

                // Get category summary
                $categoryData = $query->select(
                    'category',
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('category')
                ->orderByDesc('total')
                ->get();

                $data = [
                    'transactions' => $transactions,
                    'totalExpenses' => $totalExpenses,
                    'monthlyExpenses' => $monthlyExpenses,
                    'yearlyExpenses' => $yearlyExpenses,
                    'categoryData' => $categoryData,
                    'filters' => [
                        'date_from' => $request->date_from,
                        'date_to' => $request->date_to,
                        'category' => $request->category,
                        'payment_method' => $request->payment_method,
                        'status' => $request->status,
                        'bank_account_id' => $request->bank_account_id
                    ]
                ];

                $pdf = PDF::loadView('reports.exports.expenses-pdf', $data);
                return $pdf->download('expense_report_' . now()->format('Y-m-d') . '.pdf');
            } elseif ($request->report_type === 'category-summary') {
                // Get income categories
                $incomeCategories = FinancialTransaction::where('type', 'income')
                    ->where('status', 'completed')
                    ->when($request->filled('date_from'), function($query) use ($request) {
                        $query->whereDate('transaction_date', '>=', $request->date_from);
                    })
                    ->when($request->filled('date_to'), function($query) use ($request) {
                        $query->whereDate('transaction_date', '<=', $request->date_to);
                    })
                    ->select('category', 
                        DB::raw('COUNT(*) as transaction_count'),
                        DB::raw('SUM(amount) as total_amount'))
                    ->groupBy('category')
                    ->orderByDesc('total_amount')
                    ->get();

                // Get expense categories
                $expenseCategories = FinancialTransaction::where('type', 'expense')
                    ->where('status', 'completed')
                    ->when($request->filled('date_from'), function($query) use ($request) {
                        $query->whereDate('transaction_date', '>=', $request->date_from);
                    })
                    ->when($request->filled('date_to'), function($query) use ($request) {
                        $query->whereDate('transaction_date', '<=', $request->date_to);
                    })
                    ->select('category', 
                        DB::raw('COUNT(*) as transaction_count'),
                        DB::raw('SUM(amount) as total_amount'))
                    ->groupBy('category')
                    ->orderByDesc('total_amount')
                    ->get();

                // Calculate totals
                $totalIncome = $incomeCategories->sum('total_amount');
                $totalExpenses = $expenseCategories->sum('total_amount');

                $data = [
                    'incomeCategories' => $incomeCategories,
                    'expenseCategories' => $expenseCategories,
                    'totalIncome' => $totalIncome,
                    'totalExpenses' => $totalExpenses,
                    'dateFrom' => $request->date_from,
                    'dateTo' => $request->date_to
                ];

                $pdf = PDF::loadView('reports.exports.category-summary-pdf', $data);
                return $pdf->download('category_summary_report_' . now()->format('Y-m-d') . '.pdf');
            }
            
        } catch (\Exception $e) {
            \Log::error('PDF export failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate PDF report. ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            if ($request->has('report_type') && $request->report_type === 'bank-accounts') {
                return Excel::download(
                    new BankAccountsExport($request->date_from, $request->date_to),
                    'bank_accounts_report_' . now()->format('Y-m-d') . '.xlsx'
                );
            }

        $filters = json_decode($request->filters, true);
        return Excel::download(
            new TransactionsExport($filters),
            'financial-report-' . now()->format('Y-m-d') . '.xlsx'
        );
        } catch (\Exception $e) {
            \Log::error('Excel export failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate Excel report. ' . $e->getMessage());
        }
    }

    public function exportCsv(Request $request)
    {
        try {
            if ($request->has('report_type') && $request->report_type === 'bank-accounts') {
                return Excel::download(
                    new BankAccountsExport($request->date_from, $request->date_to),
                    'bank_accounts_report_' . now()->format('Y-m-d') . '.csv'
                );
            }

        $filters = json_decode($request->filters, true);
        return Excel::download(
            new TransactionsExport($filters),
            'financial-report-' . now()->format('Y-m-d') . '.csv'
        );
        } catch (\Exception $e) {
            \Log::error('CSV export failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to generate CSV report. ' . $e->getMessage());
        }
    }

    private function getFilteredQuery($filters)
    {
        $query = FinancialTransaction::with(['bankAccount', 'transactionable']);

        if (!empty($filters['date_from'])) {
            $query->where('transaction_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('transaction_date', '<=', $filters['date_to']);
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }
        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['bank_account_id'])) {
            $query->where('bank_account_id', $filters['bank_account_id']);
        }

        return $query->latest('transaction_date');
    }

    private function getIndividualFinanceReport($model, $type, Request $request)
    {
        try {
            \Log::info('Starting individual finance report', [
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'type' => $type
            ]);

            // Base query
            $baseQuery = FinancialTransaction::where(function($q) use ($model, $type) {
                $q->where('transactionable_type', get_class($model))
                  ->orWhere('transactionable_type', $type);
            })
            ->where('transactionable_id', $model->id);

            // Clone the base query for different purposes
            $query = clone $baseQuery;
            $summaryQuery = clone $baseQuery;
            $trendQuery = clone $baseQuery;
            $categoryQuery = clone $baseQuery;

            // Apply date filters if provided
            if ($request->filled('date_from')) {
                $dateFrom = date('Y-m-d', strtotime($request->date_from));
                $query->whereDate('transaction_date', '>=', $dateFrom);
                $summaryQuery->whereDate('transaction_date', '>=', $dateFrom);
                $trendQuery->whereDate('transaction_date', '>=', $dateFrom);
                $categoryQuery->whereDate('transaction_date', '>=', $dateFrom);
            }
            if ($request->filled('date_to')) {
                $dateTo = date('Y-m-d', strtotime($request->date_to));
                $query->whereDate('transaction_date', '<=', $dateTo);
                $summaryQuery->whereDate('transaction_date', '<=', $dateTo);
                $trendQuery->whereDate('transaction_date', '<=', $dateTo);
                $categoryQuery->whereDate('transaction_date', '<=', $dateTo);
            }

            // Get transactions
            $transactions = $query->latest('transaction_date')->paginate(15);

            // Calculate summary statistics
            $summary = $summaryQuery->selectRaw('
                COALESCE(SUM(CASE WHEN type = "income" AND status = "completed" THEN amount ELSE 0 END), 0) as total_income,
                COALESCE(SUM(CASE WHEN type = "expense" AND status = "completed" THEN amount ELSE 0 END), 0) as total_expenses,
                COUNT(CASE WHEN type = "income" THEN 1 END) as income_count,
                COUNT(CASE WHEN type = "expense" THEN 1 END) as expense_count,
                COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_count
            ')->first();

            \Log::info('Summary calculated', [
                'total_income' => $summary->total_income,
                'total_expenses' => $summary->total_expenses
            ]);

            // Get monthly trend data
            $monthlyTrend = $trendQuery
                ->selectRaw('
                    DATE_FORMAT(transaction_date, "%Y-%m") as month,
                    COALESCE(SUM(CASE WHEN type = "income" THEN amount ELSE 0 END), 0) as income,
                    COALESCE(SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END), 0) as expenses
                ')
                ->groupBy(DB::raw('DATE_FORMAT(transaction_date, "%Y-%m")'))
                ->orderBy('month')
                ->get();

            \Log::info('Monthly trend data', [
                'count' => $monthlyTrend->count(),
                'data' => $monthlyTrend->toArray()
            ]);

            // Get category distribution
            $categoryDistribution = $categoryQuery
                ->where('status', 'completed')
                ->selectRaw('
                    type,
                    category,
                    COALESCE(SUM(amount), 0) as total
                ')
                ->groupBy('type', 'category')
                ->orderBy('type')
                ->orderBy('total', 'desc')
                ->get();

            \Log::info('Category distribution', [
                'count' => $categoryDistribution->count(),
                'data' => $categoryDistribution->toArray()
            ]);

            return view('reports.individual-finances', compact(
                'model',
                'type',
                'transactions',
                'summary',
                'monthlyTrend',
                'categoryDistribution'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in getIndividualFinanceReport: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            
            return view('reports.individual-finances', [
                'model' => $model,
                'type' => $type,
                'transactions' => collect([])->paginate(15),
                'summary' => (object)[
                    'total_income' => 0,
                    'total_expenses' => 0,
                    'income_count' => 0,
                    'expense_count' => 0,
                    'pending_count' => 0
                ],
                'monthlyTrend' => collect([]),
                'categoryDistribution' => collect([])
            ])->withErrors(['error' => 'An error occurred while generating the report. Please try again.']);
        }
    }

    public function playerFinances(Request $request, Player $player)
    {
        return $this->getIndividualFinanceReport($player, 'player', $request);
    }

    public function memberFinances(Request $request, Member $member)
    {
        return $this->getIndividualFinanceReport($member, 'member', $request);
    }

    public function staffFinances(Request $request, Staff $staff)
    {
        return $this->getIndividualFinanceReport($staff, 'staff', $request);
    }

    public function sponsorFinances(Request $request, Sponsor $sponsor)
    {
        return $this->getIndividualFinanceReport($sponsor, 'sponsor', $request);
    }
} 