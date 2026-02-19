<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenant\BelongsToBranch;

class StockMovement extends Model
{
    use HasFactory, BelongsToBranch;

    protected $fillable = [
        'product_id',
        'branch_id',
        'user_id',
        'type',
        'quantity',
        'reason',
        'description',
        'prev_stock',
        'new_stock',
        'reference_id',
        'reference_type',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
