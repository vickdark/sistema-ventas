<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'sale_id',
        'amount',
        'payment_type',
        'voucher',
    ];

    // The migration only has client_id, sale_id, amount. I will stick to that to avoid errors as user requested NOT to change structure.
    protected $table = 'abonos';

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
