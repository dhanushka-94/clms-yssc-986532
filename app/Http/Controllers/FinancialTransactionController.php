<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\FinancialTransaction;
use App\Models\Member;
use App\Models\Staff;
use App\Models\Player;
use App\Models\Sponsor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PDF;
use Illuminate\Support\Str;

class FinancialTransactionController extends Controller
{
    public function index(): View
    {
        $transactions = FinancialTransaction::with(['bankAccount', 'transactionable'])
            ->latest('transaction_date')
            ->paginate(10);

        $totalIncome = FinancialTransaction::where('type', 'income')
            ->where('status', 'completed')
            ->sum('amount');

        $totalExpenses = FinancialTransaction::where('type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');

        $netBalance = $totalIncome - $totalExpenses;

        $pendingTransactions = FinancialTransaction::where('status', 'pending')->count();

        return view('financial-transactions.index', compact(
            'transactions',
            'totalIncome',
            'totalExpenses',
            'netBalance',
            'pendingTransactions'
        ));
    }

    public function create(): View
    {
        $bankAccounts = BankAccount::all();
        $players = Player::orderBy('first_name')->get();
        $staff = Staff::orderBy('first_name')->get();
        $members = Member::orderBy('first_name')->get();
        $sponsors = Sponsor::orderBy('company_name')->get();
        
        // Debug information
        \Log::info('Players count: ' . $players->count());
        \Log::info('Staff count: ' . $staff->count());
        \Log::info('Members count: ' . $members->count());
        \Log::info('Sponsors count: ' . $sponsors->count());
        
        return view('financial-transactions.create', compact(
            'bankAccounts',
            'players',
            'staff',
            'members',
            'sponsors'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'transaction_date' => ['required', 'date'],
            'category' => ['required', 'string'],
            'payment_method' => ['required', 'in:cash,bank_transfer,check,online'],
            'status' => ['required', 'in:completed,pending,cancelled'],
            'reference_number' => ['nullable', 'string'],
            'receipt_number' => ['nullable', 'string'],
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'transactionable_type' => ['nullable', 'string', 'in:App\Models\Player,App\Models\Staff,App\Models\Member,App\Models\Sponsor'],
            'transactionable_id' => ['required_with:transactionable_type', 'nullable', 'integer'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:2048'],
        ]);

        // Validate that the transactionable entity exists if type is provided
        if ($request->filled('transactionable_type') && $request->filled('transactionable_id')) {
            $model = new $request->transactionable_type;
            $request->validate([
                'transactionable_id' => ['exists:' . $model->getTable() . ',id'],
            ]);
        }

        try {
            DB::beginTransaction();

            // Generate transaction number
            $validated['transaction_number'] = 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $transaction = FinancialTransaction::create($validated);

            // Handle file attachments if any
            if ($request->hasFile('attachments')) {
                $transaction->storeAttachments($request->file('attachments'));
            }

            // Update bank account balance
            $bankAccount = BankAccount::findOrFail($request->bank_account_id);
            if ($validated['status'] === 'completed') {
                if ($validated['type'] === 'income') {
                    $bankAccount->increment('current_balance', $validated['amount']);
                } else {
                    if ($bankAccount->current_balance < $validated['amount']) {
                        throw new \Exception('Insufficient balance in bank account.');
                    }
                    $bankAccount->decrement('current_balance', $validated['amount']);
                }
            }

            DB::commit();

            return redirect()->route('financial-transactions.index')
                ->with('success', 'Transaction created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create transaction. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(FinancialTransaction $transaction): View
    {
        $transaction->load(['bankAccount', 'transactionable']);
        return view('financial-transactions.show', compact('transaction'));
    }

    public function edit(FinancialTransaction $transaction): View
    {
        $bankAccounts = BankAccount::all();
        $players = Player::orderBy('first_name')->get();
        $staff = Staff::orderBy('first_name')->get();
        $members = Member::orderBy('first_name')->get();
        $sponsors = Sponsor::orderBy('company_name')->get();
        
        return view('financial-transactions.edit', compact(
            'transaction',
            'bankAccounts',
            'players',
            'staff',
            'members',
            'sponsors'
        ));
    }

    public function update(Request $request, FinancialTransaction $transaction)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'transaction_date' => ['required', 'date'],
            'category' => ['required', 'string'],
            'payment_method' => ['required', 'in:cash,bank_transfer,check,online'],
            'status' => ['required', 'in:completed,pending,cancelled'],
            'reference_number' => ['nullable', 'string'],
            'receipt_number' => ['nullable', 'string'],
            'bank_account_id' => ['required', 'exists:bank_accounts,id'],
            'transactionable_type' => ['nullable', 'string', 'in:App\Models\Player,App\Models\Staff,App\Models\Member,App\Models\Sponsor'],
            'transactionable_id' => ['required_with:transactionable_type', 'nullable', 'integer'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:2048'],
        ]);

        // Validate that the transactionable entity exists if type is provided
        if ($request->filled('transactionable_type') && $request->filled('transactionable_id')) {
            $model = new $request->transactionable_type;
            $request->validate([
                'transactionable_id' => ['exists:' . $model->getTable() . ',id'],
            ]);
        }

        try {
            DB::beginTransaction();

            // Revert previous balance if transaction was completed
            if ($transaction->status === 'completed') {
                if ($transaction->type === 'income') {
                    $transaction->bankAccount->decrement('current_balance', $transaction->amount);
                } else {
                    $transaction->bankAccount->increment('current_balance', $transaction->amount);
                }
            }

            // Update bank account balance with new values if status is completed
            if ($validated['status'] === 'completed') {
                $bankAccount = BankAccount::findOrFail($request->bank_account_id);
                if ($validated['type'] === 'income') {
                    $bankAccount->increment('current_balance', $validated['amount']);
                } else {
                    if ($bankAccount->current_balance < $validated['amount']) {
                        throw new \Exception('Insufficient balance in bank account.');
                    }
                    $bankAccount->decrement('current_balance', $validated['amount']);
                }
            }

            $transaction->update($validated);

            // Handle file attachments if any
            if ($request->hasFile('attachments')) {
                $transaction->storeAttachments($request->file('attachments'));
            }

            DB::commit();

            return redirect()->route('financial-transactions.show', $transaction)
                ->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update transaction. ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(FinancialTransaction $transaction)
    {
        try {
            DB::beginTransaction();

            // Revert balance if transaction was completed
            if ($transaction->status === 'completed') {
                if ($transaction->type === 'income') {
                    $transaction->bankAccount->decrement('current_balance', $transaction->amount);
                } else {
                    $transaction->bankAccount->increment('current_balance', $transaction->amount);
                }
            }

            // Delete attachments if any
            if (!empty($transaction->attachments)) {
                foreach ($transaction->attachments as $attachment) {
                    Storage::delete($attachment);
                }
            }

            $transaction->delete();

            DB::commit();

            return redirect()->route('financial-transactions.index')
                ->with('success', 'Transaction deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete transaction. ' . $e->getMessage());
        }
    }

    public function downloadReceipt(FinancialTransaction $transaction)
    {
        $transaction->load(['bankAccount', 'transactionable']);
        $pdf = PDF::loadView('financial-transactions.receipt', compact('transaction'));
        return $pdf->download('receipt-' . $transaction->transaction_number . '.pdf');
    }

    public function downloadInvoice(FinancialTransaction $transaction)
    {
        if ($transaction->type !== 'income') {
            return back()->with('error', 'Invoices can only be generated for income transactions.');
        }

        $transaction->load(['bankAccount', 'transactionable']);
        $clubSettings = \App\Models\ClubSettings::first();
        
        $pdf = PDF::loadView('financial-transactions.invoice', compact('transaction', 'clubSettings'));
        $pdf->setPaper('a4');
        
        return $pdf->download('invoice-' . $transaction->transaction_number . '.pdf');
    }
}
