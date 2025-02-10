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
        'player_id',
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($player) {
            if (!$player->player_id) {
                $latestPlayer = static::latest()->first();
                
                if (!$latestPlayer) {
                    $player->player_id = 'PLY0001';
                } else {
                    $lastNumber = intval(substr($latestPlayer->player_id, 3));
                    $player->player_id = 'PLY' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
                }
            }

            $player->status = $player->status ?? 'active';
            $player->position = $player->position ?? 'unassigned';
            $player->contract_amount = $player->contract_amount ?? 0;
            $player->joined_date = $player->joined_date ?? now();
            $player->contract_start_date = $player->contract_start_date ?? now();
            $player->contract_end_date = $player->contract_end_date ?? now()->addYear();
        });
    }

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
        return $this->morphMany(FinancialTransaction::class, 'transactionable')->where(function($query) {
            $query->where('transactionable_type', 'player')
                  ->orWhere('transactionable_type', Player::class);
        });
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

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
