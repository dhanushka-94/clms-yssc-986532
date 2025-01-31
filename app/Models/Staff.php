<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\HasAttachments;

class Staff extends Model
{
    use HasFactory;
    use HasAttachments;

    protected $table = 'staff';

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'nic',
        'phone',
        'whatsapp_number',
        'address',
        'role',
        'date_of_birth',
        'joined_date',
        'salary',
        'contract_start_date',
        'contract_end_date',
        'status',
        'profile_picture',
        'attachments',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joined_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'salary' => 'decimal:2',
        'attachments' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($staff) {
            // Generate employee ID
            if (!$staff->employee_id) {
                $latestStaff = static::latest()->first();
                
                if (!$latestStaff) {
                    $staff->employee_id = 'EMP0001';
                } else {
                    $lastNumber = intval(substr($latestStaff->employee_id, 3));
                    $staff->employee_id = 'EMP' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                }
            }

            // Set default values for nullable fields
            $staff->status = $staff->status ?? 'active';
            $staff->role = $staff->role ?? 'staff';
            $staff->salary = $staff->salary ?? 0;
            $staff->joined_date = $staff->joined_date ?? now();
            $staff->contract_start_date = $staff->contract_start_date ?? now();
            $staff->contract_end_date = $staff->contract_end_date ?? now()->addYear();
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
