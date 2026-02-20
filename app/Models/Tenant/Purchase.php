<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Tenant\LogsActivity;

/**
 * @property int $id
 * @property string $nro_compra
 * @property int $supplier_id
 * @property string $purchase_date
 * @property string $voucher
 * @property float $total
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\PurchaseItem[] $items
 * @property-read \App\Models\Tenant\Supplier $supplier
 * @property-read \App\Models\Tenant\Usuario $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase find($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase findOrFail($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase create(array $attributes = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase latest()
 * @mixin \Eloquent
 */
class Purchase extends Model
{
    use HasFactory, \App\Traits\Tenant\BelongsToBranch, LogsActivity;

    protected $table = 'purchases';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nro_compra',
        'supplier_id',
        'branch_id',
        'purchase_date',
        'voucher',
        'total',
        'total_amount',
        'pending_amount',
        'payment_status',
        'due_date',
        'user_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'due_date' => 'date',
        'total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'pending_amount' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\Tenant\Usuario::class, 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }
}
