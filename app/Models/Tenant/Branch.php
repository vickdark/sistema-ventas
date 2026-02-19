<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'is_active',
        'is_main',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_branch')
            ->withPivot('stock', 'min_stock', 'max_stock')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
