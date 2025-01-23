<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Traits\HasAttachments;

class Sponsor extends Model
{
    use HasFactory;
    use HasAttachments;

    protected $fillable = [
        'sponsor_id',
        'company_name',
        'contact_person',
        'email',
        'phone',
        'whatsapp_number',
        'address',
        'sponsorship_type',
        'sponsorship_amount',
        'sponsorship_start_date',
        'sponsorship_end_date',
        'contract_start_date',
        'contract_end_date',
        'status',
        'profile_picture',
        'attachments',
    ];

    protected $casts = [
        'sponsorship_start_date' => 'date',
        'sponsorship_end_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'sponsorship_amount' => 'decimal:2',
        'attachments' => 'array',
    ];

    public function financialTransactions(): MorphMany
    {
        return $this->morphMany(FinancialTransaction::class, 'transactionable');
    }
}
