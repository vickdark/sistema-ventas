<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $description
 * @property int $stock
 * @property int $min_stock
 * @property int $max_stock
 * @property float $purchase_price
 * @property float $sale_price
 * @property string $entry_date
 * @property string|null $image
 * @property int $category_id
 * @property int $user_id
 * @property-read \App\Models\Tenant\Category $category
 * @property-read \App\Models\Tenant\Usuario|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Tenant\Supplier[] $suppliers
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product find($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product findOrFail($id)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product create(array $attributes = [])
 * @mixin \Eloquent
 */
class Product extends Model
{
    use HasFactory, \App\Traits\Tenant\BelongsToBranch;

    protected $table = 'products'; // Assuming the table name is 'products'
    protected $primaryKey = 'id'; // Assuming 'id' is the primary key

    protected $fillable = [
        'code',
        'name',
        'description',
        'stock',
        'min_stock',
        'max_stock',
        'purchase_price',
        'sale_price',
        'entry_date',
        'image',
        'category_id',
        'branch_id',
        'user_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'product_supplier');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'product_branch')
            ->withPivot('stock', 'min_stock', 'max_stock')
            ->withTimestamps();
    }

    public function addStock($quantity, $reason, $description = null, $reference = null)
    {
        $prevStock = $this->stock;
        $this->stock += $quantity;
        $this->save();

        return StockMovement::create([
            'product_id' => $this->id,
            'branch_id' => $this->branch_id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'type' => 'input',
            'quantity' => $quantity,
            'reason' => $reason,
            'description' => $description,
            'prev_stock' => $prevStock,
            'new_stock' => $this->stock,
            'reference_id' => $reference ? $reference->id : null,
            'reference_type' => $reference ? get_class($reference) : null,
        ]);
    }

    public function removeStock($quantity, $reason, $description = null, $reference = null)
    {
        $prevStock = $this->stock;
        $this->stock -= $quantity;
        $this->save();

        return StockMovement::create([
            'product_id' => $this->id,
            'branch_id' => $this->branch_id,
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'type' => 'output',
            'quantity' => $quantity,
            'reason' => $reason,
            'description' => $description,
            'prev_stock' => $prevStock,
            'new_stock' => $this->stock,
            'reference_id' => $reference ? $reference->id : null,
            'reference_type' => $reference ? get_class($reference) : null,
        ]);
    }
}
