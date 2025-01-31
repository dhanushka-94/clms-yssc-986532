<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    use HasFactory;
    use HasAttachments;

    protected $fillable = [
        'bank_name',
        'branch_name',
        'account_name',
        'account_number',
        'account_type',
        'currency',
        'initial_balance',
        'current_balance',
        'status',
        'attachments',
        'swift_code',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'attachments' => 'array',
    ];

    public function financialTransactions(): MorphMany
    {
        return $this->morphMany(FinancialTransaction::class, 'transactionable');
    }

    public function incomingTransfers(): HasMany
    {
        return $this->hasMany(InterBankTransfer::class, 'to_account_id');
    }

    public function outgoingTransfers(): HasMany
    {
        return $this->hasMany(InterBankTransfer::class, 'from_account_id');
    }
}
