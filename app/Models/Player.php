<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\HasAttachments;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Player extends Model
{
    use HasFactory;
    use HasAttachments;

    protected $fillable = [
        'first_name',
        'last_name',
        'nic',
        'ffsl_number',
        'phone',
        'whatsapp_number',
        'address',
        'position',
        'jersey_number',
        'date_of_birth',
        'joined_date',
        'contract_amount',
        'contract_start_date',
        'contract_end_date',
        'status',
        'achievements',
        'profile_picture',
        'attachments',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joined_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'contract_amount' => 'decimal:2',
        'attachments' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function financialTransactions(): MorphMany
    {
        return $this->morphMany(FinancialTransaction::class, 'transactionable');
    }

    public function attendances(): MorphMany
    {
        return $this->morphMany(Attendance::class, 'attendee');
    }

    public function events(): BelongsToMany
    {
        return $this->morphToMany(Event::class, 'attendee', 'attendances')
            ->withPivot('status', 'check_in_time', 'check_out_time', 'remarks')
            ->withTimestamps();
    }
}
