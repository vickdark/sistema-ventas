<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    use \App\Traits\Tenant\BelongsToBranch;

    protected $fillable = [
        'number',
        'sale_id',
        'branch_id',
        'user_id',
        'reason',
        'total',
        'status',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function items()
    {
        return $this->hasMany(CreditNoteItem::class);
    }
}
