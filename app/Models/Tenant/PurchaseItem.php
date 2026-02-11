<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $purchase_id
 * @property int $product_id
 * @property int $quantity
 * @property float $price
 * @property float $subtotal
 * @property-read \App\Models\Tenant\Purchase $purchase
 * @property-read \App\Models\Tenant\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem find($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem findOrFail($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PurchaseItem create(array $attributes = [])
 * @mixin \Eloquent
 */
class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
