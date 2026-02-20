<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\Tenant\LogsActivity;

class StockTransfer extends Model
{
    use LogsActivity;

    protected $fillable = [
        'nro_traslado',
        'origin_branch_id',
        'destination_branch_id',
        'user_id',
        'status',
        'shipped_at',
        'received_at',
        'notes'
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
        'received_at' => 'datetime'
    ];

    public function originBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'origin_branch_id');
    }

    public function destinationBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'destination_branch_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }
}
