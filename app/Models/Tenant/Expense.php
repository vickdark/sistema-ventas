<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tenant\Usuario;
use App\Models\Tenant\Branch;

class Expense extends Model
{
    use HasFactory, \App\Traits\Tenant\BelongsToBranch;

    protected $fillable = [
        'expense_category_id',
        'user_id',
        'branch_id',
        'name',
        'amount',
        'date',
        'description',
        'reference',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }

    public function user()
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }
}
