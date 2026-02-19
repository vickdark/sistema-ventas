<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRegister extends Model
{
    use HasFactory, \App\Traits\Tenant\BelongsToBranch;

    protected $fillable = [
        'name',
        'branch_id',
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

    protected $casts = [
        'opening_date' => 'datetime',
        'closing_date' => 'datetime',
        'initial_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'total_sales' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Usuario::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'abierta');
    }
}
