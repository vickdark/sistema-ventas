<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Tenant\LogsActivity;

class Quote extends Model
{
    use LogsActivity, \App\Traits\Tenant\BelongsToBranch;

    protected $fillable = [
        'nro_cotizacion',
        'client_id',
        'branch_id',
        'user_id',
        'total',
        'status',
        'expiration_date',
        'notes'
    ];

    protected $casts = [
        'expiration_date' => 'date',
        'total' => 'decimal:2'
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
