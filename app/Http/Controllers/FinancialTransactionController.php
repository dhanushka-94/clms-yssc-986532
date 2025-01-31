<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\FinancialTransaction;
use App\Models\Member;
use App\Models\Staff;
use App\Models\Player;
use App\Models\Sponsor;
use App\Models\Category;
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
        $query = FinancialTransaction::with(['bankAccount', 'transactionable']);

        // Apply filters
        if (request()->filled('type')) {
            $query->where('type', request('type'));
        }
        
        if (request()->filled('category')) {
            $category = Category::find(request('category'));
            if ($category) {
                $query->where('category', $category->name);
            }
        }

        if (request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if (request()->filled('payment_method')) {
            $query->where('payment_method', request('payment_method'));
        }

        if (request()->filled('bank_account_id')) {
            $query->where('bank_account_id', request('bank_account_id'));
        }

        if (request()->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', request('date_from'));
        }

        if (request()->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', request('date_to'));
        }

        $transactions = $query->latest('transaction_date')->paginate(10);

        $totalIncome = FinancialTransaction::where('type', 'income')
            ->where('status', 'completed')
            ->sum('amount');

        $totalExpenses = FinancialTransaction::where('type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');

        $pendingTransactions = FinancialTransaction::where('status', 'pending')->count();

        // Get all bank accounts with their current balances
        $bankAccounts = BankAccount::orderBy('bank_name')->get();

        // Get active categories for the filter
        $categories = Category::active()
            ->when(request('type'), function($query) {
                return $query->ofType(request('type'));
            })
            ->orderBy('name')
            ->get();

        return view('financial-transactions.index', compact(
            'transactions',
            'totalIncome',
            'totalExpenses',
            'pendingTransactions',
            'bankAccounts',
            'categories'
        ));
    }

    public function create(): View
    {
        $bankAccounts = BankAccount::all();
        $players = Player::orderBy('first_name')->get();
        $staff = Staff::orderBy('first_name')->get();
        $members = Member::orderBy('first_name')->get();
        $sponsors = Sponsor::orderBy('name')->get();
        $incomeCategories = Category::active()->ofType('income')->get();
        $expenseCategories = Category::active()->ofType('expense')->get();
        
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
            'sponsors',
            'incomeCategories',
            'expenseCategories'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'category' => 'required|string',
            'payment_method' => 'required|in:cash,bank_transfer,check,online',
            'status' => 'required|in:completed,pending,cancelled',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transactionable_type' => 'nullable|string',
            'transactionable_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'signature' => 'nullable|file|mimes:png|max:2048',
            'signatory_name' => 'nullable|string|max:255',
            'signatory_designation' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Get club settings for default signature
            $clubSettings = \App\Models\ClubSettings::first();

            // Handle signature
            $signaturePath = null;
            if ($request->hasFile('signature')) {
                $signaturePath = $request->file('signature')->store('signatures', 'public');
                if (!Storage::disk('public')->exists($signaturePath)) {
                    throw new \Exception('Failed to store signature file.');
                }
            } elseif ($clubSettings && $clubSettings->default_signature) {
                $defaultSignaturePath = $clubSettings->default_signature;
                if (!Str::startsWith($defaultSignaturePath, 'signatures/')) {
                    $defaultSignaturePath = 'signatures/' . $defaultSignaturePath;
                }
                
                if (Storage::disk('public')->exists($defaultSignaturePath)) {
                    $signaturePath = 'signatures/' . Str::random(40) . '.png';
                    if (!Storage::disk('public')->copy($defaultSignaturePath, $signaturePath)) {
                        throw new \Exception('Failed to copy default signature.');
                    }
                }
            }

            // Handle attachments
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('attachments', 'public');
                    if (!Storage::disk('public')->exists($path)) {
                        throw new \Exception('Failed to store attachment file.');
                    }
                    $attachments[] = $path;
                }
            }

            // Generate transaction number
            $transactionNumber = 'TRX-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Create transaction
            $transaction = FinancialTransaction::create([
                'transaction_number' => $transactionNumber,
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'category' => Category::find($validated['category'])->name,
                'payment_method' => $validated['payment_method'],
                'status' => $validated['status'],
                'bank_account_id' => $validated['bank_account_id'],
                'transactionable_type' => $validated['transactionable_type'] ?? null,
                'transactionable_id' => $validated['transactionable_id'] ?? null,
                'description' => $validated['description'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'attachments' => $attachments,
                'signature' => $signaturePath,
                'signatory_name' => $validated['signatory_name'] ?? ($clubSettings ? $clubSettings->default_signatory_name : null),
                'signatory_designation' => $validated['signatory_designation'] ?? ($clubSettings ? $clubSettings->default_signatory_designation : null),
            ]);

            // Update bank account balance
            $bankAccount = BankAccount::findOrFail($validated['bank_account_id']);
            if ($validated['type'] === 'income') {
                $bankAccount->current_balance += $validated['amount'];
            } else {
                $bankAccount->current_balance -= $validated['amount'];
            }
            $bankAccount->save();

            DB::commit();

            return redirect()->route('financial-transactions.index')
                ->with('success', 'Transaction created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction creation failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to create transaction. ' . $e->getMessage());
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
        $sponsors = Sponsor::orderBy('name')->get();
        $incomeCategories = Category::active()->ofType('income')->get();
        $expenseCategories = Category::active()->ofType('expense')->get();
        
        return view('financial-transactions.edit', compact(
            'transaction',
            'bankAccounts',
            'players',
            'staff',
            'members',
            'sponsors',
            'incomeCategories',
            'expenseCategories'
        ));
    }

    public function update(Request $request, FinancialTransaction $transaction)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'category' => 'required|string',
            'payment_method' => 'required|in:cash,bank_transfer,check,online',
            'status' => 'required|in:completed,pending,cancelled',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'transactionable_type' => 'nullable|string',
            'transactionable_id' => 'nullable|integer',
            'description' => 'nullable|string',
            'reference_number' => 'nullable|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
            'signature' => 'nullable|file|mimes:png|max:2048',
            'signatory_name' => 'nullable|string|max:255',
            'signatory_designation' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Handle signature upload
            if ($request->hasFile('signature')) {
                // Delete old signature if exists
                if ($transaction->signature) {
                    Storage::disk('public')->delete($transaction->signature);
                }

                // Store new signature
                $signaturePath = $request->file('signature')->store('signatures', 'public');
                
                // Verify file was stored successfully
                if (!Storage::disk('public')->exists($signaturePath)) {
                    throw new \Exception('Failed to store signature file.');
                }
                
                $validated['signature'] = $signaturePath;
            } elseif ($clubSettings && $clubSettings->default_signature) {
                // Use default signature
                $defaultSignaturePath = $clubSettings->default_signature;
                if (!Str::startsWith($defaultSignaturePath, 'signatures/')) {
                    $defaultSignaturePath = 'signatures/' . $defaultSignaturePath;
                }
                
                // Copy default signature to transaction's signature
                if (Storage::disk('public')->exists($defaultSignaturePath)) {
                    $newSignaturePath = 'signatures/' . Str::random(40) . '.png';
                    Storage::disk('public')->copy($defaultSignaturePath, $newSignaturePath);
                    $validated['signature'] = $newSignaturePath;
                }
            }

            // Handle attachments
            if ($request->hasFile('attachments')) {
                $attachments = $transaction->attachments ?? [];
                foreach ($request->file('attachments') as $file) {
                    $attachments[] = $file->store('attachments', 'public');
                }
                $validated['attachments'] = $attachments;
            }

            // Update bank account balance
            $oldBankAccount = BankAccount::findOrFail($transaction->bank_account_id);
            if ($transaction->type === 'income') {
                $oldBankAccount->current_balance -= $transaction->amount;
            } else {
                $oldBankAccount->current_balance += $transaction->amount;
            }
            $oldBankAccount->save();

            $newBankAccount = BankAccount::findOrFail($validated['bank_account_id']);
            if ($validated['type'] === 'income') {
                $newBankAccount->current_balance += $validated['amount'];
            } else {
                $newBankAccount->current_balance -= $validated['amount'];
            }
            $newBankAccount->save();

            // Update transaction
            $transaction->update([
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'category' => Category::find($validated['category'])->name,
                'payment_method' => $validated['payment_method'],
                'status' => $validated['status'],
                'bank_account_id' => $validated['bank_account_id'],
                'transactionable_type' => $validated['transactionable_type'] ?? null,
                'transactionable_id' => $validated['transactionable_id'] ?? null,
                'description' => $validated['description'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'attachments' => $validated['attachments'] ?? $transaction->attachments,
                'signature' => $validated['signature'] ?? $transaction->signature,
                'signatory_name' => $validated['signatory_name'] ?? null,
                'signatory_designation' => $validated['signatory_designation'] ?? null,
            ]);

            DB::commit();

            return redirect()->route('financial-transactions.index')
                ->with('success', 'Transaction updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update transaction. ' . $e->getMessage());
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
        $clubSettings = \App\Models\ClubSettings::first();
        
        $pdf = PDF::loadView('financial-transactions.receipt', compact('transaction', 'clubSettings'));
        $pdf->setPaper('a4');
        
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

    public function updateSignature(Request $request, FinancialTransaction $transaction)
    {
        $request->validate([
            'signature_file' => 'required|file|mimes:png|max:2048',
            'signatory_name' => 'required|string|max:255',
            'signatory_designation' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Store the signature file
            $path = $request->file('signature_file')->store('signatures', 'public');
            
            // Update transaction signature
            $transaction->update([
                'signature' => $path,
                'signatory_name' => $request->signatory_name,
                'signatory_designation' => $request->signatory_designation,
            ]);

            // Update or create club settings
            \App\Models\ClubSettings::updateOrCreate(
                ['id' => 1],
                [
                    'name' => 'Young Silver Sports Club',
                    'email' => config('club.email'),
                    'phone' => config('club.phone'),
                    'address' => implode(', ', array_filter(config('club.address'))),
                    'default_signature' => $path,
                    'default_signatory_name' => $request->signatory_name,
                    'default_signatory_designation' => $request->signatory_designation,
                ]
            );

            DB::commit();
            return back()->with('success', 'Signature has been added to the invoice.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update signature. ' . $e->getMessage());
        }
    }
}
