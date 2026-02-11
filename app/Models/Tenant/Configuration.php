<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_register_closing_time',
        'cash_register_names',
    ];

    protected $casts = [
        'cash_register_names' => 'array',
    ];
}
