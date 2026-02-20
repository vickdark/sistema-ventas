<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tenant\Permission;
use App\Models\Tenant\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            [
                'nombre' => 'Ver Cuentas por Pagar',
                'slug' => 'supplier-payments.index',
                'descripcion' => 'Permite ver la lista de deudas pendientes con proveedores',
                'module' => 'Finanzas',
                'icon' => 'fa-solid fa-file-invoice-dollar',
                'is_menu' => true,
                'order' => 85
            ],
            [
                'nombre' => 'Registrar Abono Proveedor',
                'slug' => 'supplier-payments.store',
                'descripcion' => 'Permite registrar pagos a deudas de proveedores',
                'module' => 'Finanzas',
                'is_menu' => false,
            ],
            [
                'nombre' => 'Ver Detalle de Deuda',
                'slug' => 'supplier-payments.show',
                'descripcion' => 'Permite ver el historial de pagos de una compra a crédito',
                'module' => 'Finanzas',
                'is_menu' => false,
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(['slug' => $permissionData['slug']], $permissionData);
        }

        // Asignar al administrador y contador/supervisor
        $roles = Role::whereIn('slug', ['admin', 'supervisor'])->get();
        foreach ($roles as $role) {
            $permissionIds = Permission::where('slug', 'like', 'supplier-payments.%')->pluck('id');
            $role->permissions()->syncWithoutDetaching($permissionIds);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('slug', 'like', 'supplier-payments.%')->delete();
    }
};
