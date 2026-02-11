<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HttpLog extends Model
{
    protected $connection = 'central';
    
    public $timestamps = false; // we use created_at current_timestamp in migration

    protected $fillable = [
        'tenant_id',
        'method',
        'url',
        'status',
        'duration',
        'ip',
        'created_at',
    ];
}
