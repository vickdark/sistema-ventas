<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\Tenant\Product|null $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase query()
 * @mixin \Eloquent
 */
class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchases'; // Assuming the table name is 'purchases'
    protected $primaryKey = 'id'; // Assuming 'id' is the primary key

    protected $fillable = [
        'product_id',
        'supplier_id',
        'quantity',
        'price',
        'purchase_date',
        'voucher',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // public function supplier()
    // {
    //     return $this->belongsTo(Supplier::class);
    // }
}
