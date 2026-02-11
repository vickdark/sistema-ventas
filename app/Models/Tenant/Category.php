<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @mixin \Eloquent
 */
class Category extends Model
{
    use HasFactory;

    protected $table = 'categories'; // Assuming the table name is 'categories'
    protected $primaryKey = 'id'; // Assuming 'id' is the primary key

    protected $fillable = [
        'name',
        'description',
    ];

    // Define relationship with Products if applicable
    // public function products()
    // {
    //     return $this->hasMany(Product::class);
    // }
}
