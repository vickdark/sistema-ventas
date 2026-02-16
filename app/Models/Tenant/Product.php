<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $stock
 * @property int $min_stock
 * @property int $max_stock
 * @property float $purchase_price
 * @property float $sale_price
 * @property string $entry_date
 * @property string|null $image
 * @property int $category_id
 * @property int $user_id
 * @property-read \App\Models\Tenant\Category $category
 * @property-read \App\Models\Tenant\Usuario|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\Supplier[] $suppliers
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product find($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product findOrFail($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product create(array $attributes = [])
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory;

    protected $table = 'products'; // Assuming the table name is 'products'
    protected $primaryKey = 'id'; // Assuming 'id' is the primary key

    protected $fillable = [
        'code',
        'name',
        'description',
        'stock',
        'min_stock',
        'max_stock',
        'purchase_price',
        'sale_price',
        'entry_date',
        'image',
        'category_id',
        'user_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier');
    }
}
