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
                'nombre' => 'Ver Traslados',
                'slug' => 'stock-transfers.index',
                'descripcion' => 'Permite ver la lista de traslados entre sucursales',
                'module' => 'Inventario',
                'icon' => 'fa-solid fa-truck-ramp-box',
                'is_menu' => true,
                'order' => 55
            ],
            [
                'nombre' => 'Crear Traslado',
                'slug' => 'stock-transfers.create',
                'descripcion' => 'Permite realizar envíos de stock a otras sucursales',
                'module' => 'Inventario',
                'is_menu' => false,
            ],
            [
                'nombre' => 'Ver Detalle Traslado',
                'slug' => 'stock-transfers.show',
                'descripcion' => 'Permite ver los detalles de un traslado',
                'module' => 'Inventario',
                'is_menu' => false,
            ],
            [
                'nombre' => 'Recibir Traslado',
                'slug' => 'stock-transfers.receive',
                'descripcion' => 'Permite confirmar la recepción de stock en la sucursal de destino',
                'module' => 'Inventario',
                'is_menu' => false,
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(['slug' => $permissionData['slug']], $permissionData);
        }

        // Asignar al administrador y supervisor
        $roles = Role::whereIn('slug', ['admin', 'supervisor'])->get();
        foreach ($roles as $role) {
            $permissionIds = Permission::where('slug', 'like', 'stock-transfers.%')->pluck('id');
            $role->permissions()->syncWithoutDetaching($permissionIds);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('slug', 'like', 'stock-transfers.%')->delete();
    }
};
