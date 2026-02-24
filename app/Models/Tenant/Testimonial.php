<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'content',
        'rating',
        'image_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating' => 'integer',
    ];
}
