<?php

namespace App\Models\Tenant;

use App\Models\Tenant\Usuario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'nro_venta',
        'client_id',
        'total_paid',
        'user_id',
        'sale_date',
        'voucher',
        'payment_type',
        'payment_status',
        'credit_payment_date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
