<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function index(): View
    {
        $bankAccounts = BankAccount::orderBy('created_at', 'desc')->paginate(10);
        return view('bank-accounts.index', compact('bankAccounts'));
    }

    public function create(): View
    {
        return view('bank-accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:bank_accounts',
            'account_type' => 'required|string|max:255',
            'currency' => 'required|string|max:3',
            'initial_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
        ]);

        $bankAccount = new BankAccount($validated);

        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profile-pictures/bank-accounts', 'public');
            $bankAccount->profile_picture = $path;
        }

        $bankAccount->save();

        // Handle attachments after saving to get the bank account ID
        if ($request->hasFile('attachments')) {
            $attachmentPaths = $bankAccount->storeAttachments($request->file('attachments'));
            $bankAccount->attachments = $attachmentPaths;
            $bankAccount->save();
        }

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Bank account created successfully.');
    }

    public function show(BankAccount $bankAccount): View
    {
        $bankAccount->load(['financialTransactions' => function($query) {
            $query->latest('transaction_date')->take(5);
        }]);
        
        return view('bank-accounts.show', compact('bankAccount'));
    }

    public function edit(BankAccount $bankAccount): View
    {
        return view('bank-accounts.edit', compact('bankAccount'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'branch_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255|unique:bank_accounts,account_number,' . $bankAccount->id,
            'account_type' => 'required|string|max:255',
            'currency' => 'required|string|max:3',
            'initial_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'status' => 'required|in:active,inactive',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'attachments.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
            'delete_attachments.*' => 'nullable|string',
        ]);

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture
            if ($bankAccount->profile_picture) {
                Storage::disk('public')->delete($bankAccount->profile_picture);
            }
            $path = $request->file('profile_picture')->store('profile-pictures/bank-accounts', 'public');
            $validated['profile_picture'] = $path;
        }

        // Handle attachment deletions
        if ($request->has('delete_attachments')) {
            $currentAttachments = $bankAccount->attachments ?? [];
            foreach ($request->delete_attachments as $path) {
                $bankAccount->deleteAttachment($path);
                $currentAttachments = array_diff($currentAttachments, [$path]);
            }
            $validated['attachments'] = array_values($currentAttachments);
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            $newAttachments = $bankAccount->storeAttachments($request->file('attachments'));
            $currentAttachments = $bankAccount->attachments ?? [];
            $validated['attachments'] = array_merge($currentAttachments, $newAttachments);
        }

        $bankAccount->update($validated);

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Bank account updated successfully.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        // Delete profile picture
        if ($bankAccount->profile_picture) {
            Storage::disk('public')->delete($bankAccount->profile_picture);
        }

        // Delete all attachments
        $bankAccount->deleteAllAttachments();

        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')
            ->with('success', 'Bank account deleted successfully.');
    }
}
