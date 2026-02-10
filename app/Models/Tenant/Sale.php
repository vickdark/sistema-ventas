<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\Tenant\Client|null $client
 * @property-read \App\Models\Tenant\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Sale query()
 * @mixin \Eloquent
 */
class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales'; // Assuming the table name is 'sales'
    protected $primaryKey = 'id'; // Assuming 'id' is the primary key

    protected $fillable = [
        'client_id',
        'product_id',
        'quantity',
        'price',
        'sale_date',
        'voucher',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
