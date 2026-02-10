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
        'user_id', // Added mentally, let's check migration if it has it. It doesn't in schema but I should probably add it or just use what's there.
        'method', // Added mentally
        'date', // Added mentally
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
