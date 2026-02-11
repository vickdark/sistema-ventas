<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
 * @property-read \App\Models\Usuarios\Usuario $user
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
    use HasFactory;

    protected $table = 'purchases';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nro_compra',
        'supplier_id',
        'purchase_date',
        'voucher',
        'total',
        'user_id',
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
        return $this->belongsTo(\App\Models\Usuarios\Usuario::class, 'user_id');
    }
}
