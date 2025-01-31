<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'logo_path',
        'description',
        'registration_number',
        'tax_number',
        'default_signature',
        'default_signatory_name',
        'default_signatory_designation',
    ];
}
