<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Supplier query()
 * @mixin \Eloquent
 */
class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers'; // Assuming the table name is 'suppliers'
    protected $primaryKey = 'id'; // Assuming 'id' is the primary key

    protected $fillable = [
        'name',
        'phone',
        'secondary_phone',
        'company',
        'email',
        'address',
    ];

    // Define relationship with Purchases if applicable
    // public function purchases()
    // {
    //     return $this->hasMany(Purchase::class);
    // }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_supplier');
    }
}
