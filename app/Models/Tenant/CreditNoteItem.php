<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class CreditNoteItem extends Model
{
    protected $fillable = [
        'credit_note_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'restock',
    ];

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
