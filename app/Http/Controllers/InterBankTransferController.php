<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\InterBankTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InterBankTransferController extends Controller
{
    public function index(): View
    {
        $query = InterBankTransfer::with(['fromAccount', 'toAccount']);

        if ($accountId = request('account_id')) {
            $query->where(function($q) use ($accountId) {
                $q->where('from_account_id', $accountId)
                  ->orWhere('to_account_id', $accountId);
            });
        }

        $transfers = $query->latest('transfer_date')->paginate(10);

        $totalTransferred = InterBankTransfer::where('status', 'completed')
            ->when($accountId, function($q) use ($accountId) {
                $q->where(function($q) use ($accountId) {
                    $q->where('from_account_id', $accountId)
                      ->orWhere('to_account_id', $accountId);
                });
            })
            ->sum('amount');

        $pendingTransfers = InterBankTransfer::where('status', 'pending')
            ->when($accountId, function($q) use ($accountId) {
                $q->where(function($q) use ($accountId) {
                    $q->where('from_account_id', $accountId)
                      ->orWhere('to_account_id', $accountId);
                });
            })
            ->count();

        return view('interbank-transfers.index', compact(
            'transfers',
            'totalTransferred',
            'pendingTransfers'
        ));
    }

    public function create(): View
    {
        $bankAccounts = BankAccount::where('status', 'active')->get();
        $fromAccountId = request('from_account_id');
        return view('interbank-transfers.create', compact('bankAccounts', 'fromAccountId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_account_id' => ['required', 'exists:bank_accounts,id'],
            'to_account_id' => ['required', 'exists:bank_accounts,id', 'different:from_account_id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'transfer_date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,completed,failed,cancelled'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:2048'],
        ]);

        try {
            DB::beginTransaction();

            // Check if source account has sufficient balance
            $fromAccount = BankAccount::findOrFail($request->from_account_id);
            if ($fromAccount->current_balance < $request->amount) {
                return back()->with('error', 'Insufficient balance in source account.');
            }

            $transfer = InterBankTransfer::create([
                'transfer_number' => 'TRF' . now()->format('Y') . str_pad(InterBankTransfer::count() + 1, 6, '0', STR_PAD_LEFT),
                'from_account_id' => $request->from_account_id,
                'to_account_id' => $request->to_account_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'transfer_date' => $request->transfer_date,
                'reference_number' => $request->reference_number,
                'status' => $request->status,
            ]);

            if ($request->hasFile('attachments')) {
                $transfer->storeAttachments($request->file('attachments'));
            }

            // Update account balances if transfer is completed
            if ($request->status === 'completed') {
                $fromAccount->decrement('current_balance', $request->amount);
                $toAccount = BankAccount::findOrFail($request->to_account_id);
                $toAccount->increment('current_balance', $request->amount);
            }

            DB::commit();

            return redirect()->route('interbank-transfers.show', $transfer)
                ->with('success', 'Transfer created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create transfer. ' . $e->getMessage());
        }
    }

    public function show(InterBankTransfer $interbankTransfer): View
    {
        $interbankTransfer->load(['fromAccount', 'toAccount']);
        return view('interbank-transfers.show', compact('interbankTransfer'));
    }

    public function edit(InterBankTransfer $interbankTransfer): View
    {
        $bankAccounts = BankAccount::where('status', 'active')->get();
        return view('interbank-transfers.edit', compact('interbankTransfer', 'bankAccounts'));
    }

    public function update(Request $request, InterBankTransfer $interbankTransfer)
    {
        $request->validate([
            'from_account_id' => ['required', 'exists:bank_accounts,id'],
            'to_account_id' => ['required', 'exists:bank_accounts,id', 'different:from_account_id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
            'transfer_date' => ['required', 'date'],
            'reference_number' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,completed,failed,cancelled'],
            'attachments.*' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:2048'],
        ]);

        try {
            DB::beginTransaction();

            // Revert previous balances if transfer was completed
            if ($interbankTransfer->status === 'completed') {
                $interbankTransfer->fromAccount->increment('current_balance', $interbankTransfer->amount);
                $interbankTransfer->toAccount->decrement('current_balance', $interbankTransfer->amount);
            }

            // Check if source account has sufficient balance for new amount
            if ($request->status === 'completed') {
                $fromAccount = BankAccount::findOrFail($request->from_account_id);
                if ($fromAccount->current_balance < $request->amount) {
                    return back()->with('error', 'Insufficient balance in source account.');
                }

                // Update account balances with new amount
                $fromAccount->decrement('current_balance', $request->amount);
                $toAccount = BankAccount::findOrFail($request->to_account_id);
                $toAccount->increment('current_balance', $request->amount);
            }

            $interbankTransfer->update([
                'from_account_id' => $request->from_account_id,
                'to_account_id' => $request->to_account_id,
                'amount' => $request->amount,
                'description' => $request->description,
                'transfer_date' => $request->transfer_date,
                'reference_number' => $request->reference_number,
                'status' => $request->status,
            ]);

            if ($request->hasFile('attachments')) {
                $interbankTransfer->storeAttachments($request->file('attachments'));
            }

            DB::commit();

            return redirect()->route('interbank-transfers.show', $interbankTransfer)
                ->with('success', 'Transfer updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update transfer. ' . $e->getMessage());
        }
    }

    public function destroy(InterBankTransfer $interbankTransfer)
    {
        try {
            DB::beginTransaction();

            // Revert balances if transfer was completed
            if ($interbankTransfer->status === 'completed') {
                $interbankTransfer->fromAccount->increment('current_balance', $interbankTransfer->amount);
                $interbankTransfer->toAccount->decrement('current_balance', $interbankTransfer->amount);
            }

            $interbankTransfer->delete();

            DB::commit();

            return redirect()->route('interbank-transfers.index')
                ->with('success', 'Transfer deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete transfer. ' . $e->getMessage());
        }
    }
} 