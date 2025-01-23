<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasAttachments;

class InterBankTransfer extends Model
{
    use HasFactory, HasAttachments;

    protected $table = 'inter_bank_transfers';

    protected $fillable = [
        'transfer_number',
        'from_account_id',
        'to_account_id',
        'amount',
        'description',
        'transfer_date',
        'reference_number',
        'status',
        'attachments'
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'amount' => 'decimal:2',
        'attachments' => 'array'
    ];

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class, 'to_account_id');
    }

    public function getRouteKeyName()
    {
        return 'transfer_number';
    }
} 