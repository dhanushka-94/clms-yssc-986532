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
            ->where('type', 'income');
        
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
        
        // Get income statistics
        $totalIncome = $query->sum('amount');
        $monthlyIncome = $query->whereMonth('transaction_date', Carbon::now()->month)->sum('amount');
        $yearlyIncome = $query->whereYear('transaction_date', Carbon::now()->year)->sum('amount');
        
        // Get monthly data for trend chart
        $monthlyData = FinancialTransaction::where('type', 'income')
            ->select(
                DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Get category data for pie chart
        $categoryData = FinancialTransaction::where('type', 'income')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderBy('total', 'desc')
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

        // Get entity-wise summary
        $entitySummary = FinancialTransaction::whereBetween('transaction_date', [$dateFrom, $dateTo])
            ->whereNotNull('transactionable_type')
            ->select(
                'transactionable_type',
                'type',
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as transaction_count')
            )
            ->groupBy('transactionable_type', 'type')
            ->get();

        return view('reports.entities', compact('entitySummary'));
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

        $transactions = $query->latest('transaction_date')->get();
        
        // Get summary data
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpenses = $transactions->where('type', 'expense')->sum('amount');
        $pendingTransactions = $transactions->where('status', 'pending')->count();

        $pdf = PDF::loadView('reports.pdf.transactions', compact(
            'transactions',
            'totalIncome',
            'totalExpenses',
            'pendingTransactions'
        ));

        return $pdf->download('transactions-report.pdf');
    }

    public function exportExcel(Request $request)
    {
        $filters = json_decode($request->filters, true);
        return Excel::download(
            new TransactionsExport($filters),
            'financial-report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportCsv(Request $request)
    {
        $filters = json_decode($request->filters, true);
        return Excel::download(
            new TransactionsExport($filters),
            'financial-report-' . now()->format('Y-m-d') . '.csv'
        );
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

            // Use the correct morphMap key instead of the full class name
            $morphType = match (get_class($model)) {
                Player::class => 'player',
                Staff::class => 'staff',
                Member::class => 'member',
                default => strtolower(class_basename($model))
            };

            $query = FinancialTransaction::where('transactionable_type', $morphType)
                ->where('transactionable_id', $model->id);

            \Log::info('Initial query built', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings(),
                'morph_type' => $morphType
            ]);

            // Apply date filters
            if ($request->filled('date_from')) {
                $query->where('transaction_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('transaction_date', '<=', $request->date_to);
            }

            // Get transactions with error handling
            try {
                $transactions = $query->latest('transaction_date')->paginate(15);
                \Log::info('Transactions found', ['count' => $transactions->count()]);
            } catch (\Exception $e) {
                \Log::error('Error retrieving transactions: ' . $e->getMessage());
                $transactions = collect([])->paginate(15);
            }

            // Calculate summary statistics with error handling
            try {
                $summary = $query->selectRaw('
                    COALESCE(SUM(CASE WHEN type = "income" AND status = "completed" THEN amount ELSE 0 END), 0) as total_income,
                    COALESCE(SUM(CASE WHEN type = "expense" AND status = "completed" THEN amount ELSE 0 END), 0) as total_expenses,
                    COUNT(CASE WHEN type = "income" THEN 1 END) as income_count,
                    COUNT(CASE WHEN type = "expense" THEN 1 END) as expense_count,
                    COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_count
                ')->first();
                \Log::info('Summary calculated', ['summary' => $summary]);
            } catch (\Exception $e) {
                \Log::error('Error calculating summary: ' . $e->getMessage());
                $summary = (object)[
                    'total_income' => 0,
                    'total_expenses' => 0,
                    'income_count' => 0,
                    'expense_count' => 0,
                    'pending_count' => 0
                ];
            }

            // Get monthly trend data with error handling
            try {
                $monthlyTrend = $query->select(
                    DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as month'),
                    DB::raw('COALESCE(SUM(CASE WHEN type = "income" THEN amount ELSE 0 END), 0) as income'),
                    DB::raw('COALESCE(SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END), 0) as expenses')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                \Log::info('Monthly trend retrieved', ['count' => $monthlyTrend->count()]);
            } catch (\Exception $e) {
                \Log::error('Error retrieving monthly trend: ' . $e->getMessage());
                $monthlyTrend = collect([]);
            }

            // Get category distribution with error handling
            try {
                $categoryDistribution = $query->where('status', 'completed')
                    ->select('type', 'category', DB::raw('SUM(amount) as total'))
                    ->groupBy('type', 'category')
                    ->get();
                \Log::info('Category distribution retrieved', ['count' => $categoryDistribution->count()]);
            } catch (\Exception $e) {
                \Log::error('Error retrieving category distribution: ' . $e->getMessage());
                $categoryDistribution = collect([]);
            }

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
            
            // Return a view with empty data
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
} 