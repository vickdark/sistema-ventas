<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Client query()
 * @mixin \Eloquent
 */
class Client extends Model
{
    use HasFactory;

    protected $table = 'clients'; // Assuming the table name is 'clients'
    protected $primaryKey = 'id'; // Assuming 'id' is the primary key

    protected $fillable = [
        'name',
        'nit_ci',
        'phone',
        'email',
        'address',
    ];
}
