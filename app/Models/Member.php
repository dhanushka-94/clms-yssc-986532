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
        'membership_number',
        'first_name',
        'last_name',
        'nic',
        'phone',
        'whatsapp_number',
        'address',
        'date_of_birth',
        'joined_date',
        'membership_type',
        'designation',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($member) {
            // Generate membership number
            if (!$member->membership_number) {
                $latestMember = static::orderBy('id', 'desc')->first();
                
                if (!$latestMember) {
                    $member->membership_number = 'M-0001';
                } else {
                    $lastNumber = intval(substr($latestMember->membership_number, 2));
                    $member->membership_number = 'M-' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                }
            }

            // Set default values for nullable fields
            $member->status = $member->status ?? 'active';
            $member->membership_type = $member->membership_type ?? 'regular';
            $member->membership_fee = $member->membership_fee ?? 0;
            $member->joined_date = $member->joined_date ?? now();
        });
    }

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
