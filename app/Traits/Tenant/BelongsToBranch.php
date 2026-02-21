<?php

namespace App\Traits\Tenant;

use App\Models\Tenant\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;

trait BelongsToBranch
{
    protected static function bootBelongsToBranch()
    {
        static::creating(function ($model) {
            if (!isset($model->branch_id) && !array_key_exists('branch_id', $model->getAttributes())) {
                if (Session::has('active_branch_id')) {
                    $model->branch_id = Session::get('active_branch_id');
                } else {
                    // Si no hay sesión (ej: Jobs, Seeders, comandos), usar la sucursal principal
                    $mainBranchId = Branch::where('is_main', true)->value('id') ?? Branch::value('id');
                    if ($mainBranchId) {
                        $model->branch_id = $mainBranchId;
                    }
                }
            }
        });

        // Aplicar scope global para filtrar por sucursal
        static::addGlobalScope('branch', function (Builder $builder) {
            // Si el usuario es administrador, no aplicar el filtro
            // Usamos auth()->hasUser() para evitar ciclos infinitos al cargar el usuario
            if (auth()->hasUser() && method_exists(auth()->user(), 'isAdmin') && auth()->user()->isAdmin()) {
                return;
            }

            if (Session::has('active_branch_id')) {
                $builder->where(function (Builder $query) {
                    $query->where('branch_id', Session::get('active_branch_id'))
                          ->orWhereNull('branch_id');
                });
            }
        });
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
