<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SignatureController extends Controller
{
    public function index()
    {
        $signatures = FinancialTransaction::whereNotNull('signature')
            ->select('id', 'transaction_number', 'signature', 'signatory_name', 'signatory_designation')
            ->latest()
            ->paginate(10);
            
        return view('signatures.index', compact('signatures'));
    }

    public function create()
    {
        return view('signatures.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'signature_file' => 'required|file|mimes:png|max:2048',
            'signatory_name' => 'required|string|max:255',
            'signatory_designation' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $path = $request->file('signature_file')->store('signatures', 'public');
            
            // Create a new signature record
            $transaction = FinancialTransaction::findOrFail($request->transaction_id);
            $transaction->update([
                'signature' => $path,
                'signatory_name' => $request->signatory_name,
                'signatory_designation' => $request->signatory_designation,
            ]);

            DB::commit();
            return redirect()->route('signatures.index')->with('success', 'Signature added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add signature. ' . $e->getMessage());
        }
    }

    public function edit(FinancialTransaction $transaction)
    {
        return view('signatures.edit', compact('transaction'));
    }

    public function update(Request $request, FinancialTransaction $transaction)
    {
        $request->validate([
            'signature_file' => 'nullable|file|mimes:png|max:2048',
            'signatory_name' => 'required|string|max:255',
            'signatory_designation' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $data = [
                'signatory_name' => $request->signatory_name,
                'signatory_designation' => $request->signatory_designation,
            ];

            // Handle new signature file if uploaded
            if ($request->hasFile('signature_file')) {
                // Delete old signature file if exists
                if ($transaction->signature) {
                    Storage::disk('public')->delete($transaction->signature);
                }

                // Store new signature file
                $path = $request->file('signature_file')->store('signatures', 'public');
                $data['signature'] = $path;
            }

            $transaction->update($data);

            DB::commit();
            return redirect()->route('signatures.index')->with('success', 'Signature updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update signature. ' . $e->getMessage());
        }
    }

    public function destroy(FinancialTransaction $transaction)
    {
        try {
            DB::beginTransaction();

            // Delete signature file
            if ($transaction->signature) {
                Storage::disk('public')->delete($transaction->signature);
            }

            // Remove signature data from transaction
            $transaction->update([
                'signature' => null,
                'signatory_name' => null,
                'signatory_designation' => null,
            ]);

            DB::commit();
            return redirect()->route('signatures.index')->with('success', 'Signature removed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to remove signature. ' . $e->getMessage());
        }
    }
}
