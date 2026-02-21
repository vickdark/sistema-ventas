<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'clock_in',
        'clock_out',
        'date',
        'status',
        'ip_address',
        'notes',
    ];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\Tenant\Usuario::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Tenant\Branch::class, 'branch_id');
    }
}
