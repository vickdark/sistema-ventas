<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
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
    ];

    // Define relationship with Category if applicable
    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }
}
