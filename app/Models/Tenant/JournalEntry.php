<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'description',
        'reference_type',
        'reference_id',
        'reference_number',
        'branch_id',
        'user_id',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(JournalEntryDetail::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
