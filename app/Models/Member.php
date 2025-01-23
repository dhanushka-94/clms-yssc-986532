<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasAttachments;

class Member extends Model
{
    use HasFactory;
    use HasAttachments;

    protected $fillable = [
        'first_name',
        'last_name',
        'nic',
        'phone',
        'whatsapp_number',
        'address',
        'date_of_birth',
        'joined_date',
        'membership_fee',
        'status',
        'profile_picture',
        'attachments',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joined_date' => 'date',
        'membership_fee' => 'decimal:2',
        'attachments' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function financialTransactions()
    {
        return $this->morphMany(FinancialTransaction::class, 'transactionable');
    }

    public function attendances(): MorphMany
    {
        return $this->morphMany(Attendance::class, 'attendee');
    }

    public function events()
    {
        return $this->morphToMany(Event::class, 'attendee', 'attendances')
            ->withPivot('status', 'check_in_time', 'check_out_time', 'remarks')
            ->withTimestamps();
    }
}
