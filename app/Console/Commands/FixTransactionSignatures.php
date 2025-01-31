<?php

namespace App\Console\Commands;

use App\Models\FinancialTransaction;
use App\Models\ClubSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FixTransactionSignatures extends Command
{
    protected $signature = 'transactions:fix-signatures';
    protected $description = 'Fix missing signature files in transactions by copying from default signature';

    public function handle()
    {
        $this->info('Starting to fix transaction signatures...');

        // Get default signature from club settings
        $clubSettings = ClubSettings::first();
        if (!$clubSettings || !$clubSettings->default_signature) {
            $this->error('No default signature found in club settings!');
            return 1;
        }

        // Ensure default signature exists
        $defaultSignaturePath = $clubSettings->default_signature;
        if (!Str::startsWith($defaultSignaturePath, 'signatures/')) {
            $defaultSignaturePath = 'signatures/' . $defaultSignaturePath;
        }

        if (!Storage::disk('public')->exists($defaultSignaturePath)) {
            $this->error('Default signature file not found!');
            return 1;
        }

        // Get all transactions with missing signature files
        $transactions = FinancialTransaction::whereNotNull('signature')->get();
        $fixed = 0;
        $errors = 0;

        foreach ($transactions as $transaction) {
            $this->info("Processing transaction {$transaction->transaction_number}...");

            try {
                if (!Storage::disk('public')->exists($transaction->signature)) {
                    // Generate new signature path
                    $newSignaturePath = 'signatures/' . Str::random(40) . '.png';
                    
                    // Copy default signature
                    Storage::disk('public')->copy($defaultSignaturePath, $newSignaturePath);
                    
                    // Update transaction
                    $transaction->update([
                        'signature' => $newSignaturePath,
                        'signatory_name' => $transaction->signatory_name ?? $clubSettings->default_signatory_name,
                        'signatory_designation' => $transaction->signatory_designation ?? $clubSettings->default_signatory_designation,
                    ]);

                    $fixed++;
                    $this->info("Fixed signature for transaction {$transaction->transaction_number}");
                }
            } catch (\Exception $e) {
                $this->error("Error fixing signature for transaction {$transaction->transaction_number}: {$e->getMessage()}");
                $errors++;
            }
        }

        $this->info("\nSignature fix completed!");
        $this->info("Fixed: {$fixed} transactions");
        $this->info("Errors: {$errors} transactions");

        return 0;
    }
} 