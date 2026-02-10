<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'opening_date',
        'scheduled_closing_time',
        'closing_date',
        'initial_amount',
        'final_amount',
        'sales_count',
        'total_sales',
        'observations',
        'user_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\Usuarios\Usuario::class, 'user_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'abierta');
    }
}
