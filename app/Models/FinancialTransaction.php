<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Traits\HasAttachments;
use App\Models\Staff;
use App\Models\Member;
use App\Models\Player;
use App\Models\Sponsor;
use App\Models\BankAccount;

class FinancialTransaction extends Model
{
    use HasFactory, HasAttachments;

    protected $fillable = [
        'transaction_number',
        'transaction_date',
        'type',
        'amount',
        'description',
        'category',
        'payment_method',
        'bank_account_id',
        'status',
        'remarks',
        'attachments',
        'transactionable_type',
        'transactionable_id'
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'amount' => 'decimal:2',
        'attachments' => 'array'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Register the morph map
        Relation::morphMap([
            'staff' => Staff::class,
            'member' => Member::class,
            'player' => Player::class,
            'sponsor' => Sponsor::class,
            'bank_account' => BankAccount::class,
        ]);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getTransactionableNameAttribute()
    {
        if (!$this->transactionable) {
            return null;
        }

        return match ($this->transactionable_type) {
            'App\\Models\\Player' => $this->transactionable->name . ' (Player)',
            'App\\Models\\Staff' => $this->transactionable->name . ' (Staff)',
            'App\\Models\\Member' => $this->transactionable->name . ' (Member)',
            'App\\Models\\Sponsor' => $this->transactionable->name . ' (Sponsor)',
            default => null,
        };
    }

    public function getRouteKeyName()
    {
        return 'transaction_number';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (!$transaction->transaction_number) {
                $prefix = $transaction->type === 'income' ? 'INC' : 'EXP';
                $latestTransaction = static::where('type', $transaction->type)
                    ->latest()
                    ->first();

                if (!$latestTransaction) {
                    $transaction->transaction_number = $prefix . '0001';
                } else {
                    $lastNumber = intval(substr($latestTransaction->transaction_number, 3));
                    $transaction->transaction_number = $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                }
            }

            $transaction->status = $transaction->status ?? 'pending';
        });
    }
}
