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
                'nombre' => 'Ver Cotizaciones',
                'slug' => 'quotes.index',
                'descripcion' => 'Permite ver la lista de cotizaciones',
                'module' => 'Ventas',
                'icon' => 'fa-solid fa-file-invoice',
                'is_menu' => true,
                'order' => 23
            ],
            [
                'nombre' => 'Crear Cotización',
                'slug' => 'quotes.create',
                'descripcion' => 'Permite crear nuevas cotizaciones',
                'module' => 'Ventas',
                'is_menu' => false,
            ],
            [
                'nombre' => 'Ver Detalle Cotización',
                'slug' => 'quotes.show',
                'descripcion' => 'Permite ver los detalles de una cotización',
                'module' => 'Ventas',
                'is_menu' => false,
            ],
            [
                'nombre' => 'Convertir Cotización a Venta',
                'slug' => 'quotes.convert',
                'descripcion' => 'Permite convertir una cotización en una venta real',
                'module' => 'Ventas',
                'is_menu' => false,
            ],
            [
                'nombre' => 'Eliminar Cotización',
                'slug' => 'quotes.destroy',
                'descripcion' => 'Permite eliminar cotizaciones',
                'module' => 'Ventas',
                'is_menu' => false,
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(['slug' => $permissionData['slug']], $permissionData);
        }

        // Asignar al administrador y vendedor
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $permissionIds = Permission::where('slug', 'like', 'quotes.%')->pluck('id');
            $adminRole->permissions()->attach($permissionIds);
        }

        $vendedorRole = Role::where('slug', 'vendedor')->first();
        if ($vendedorRole) {
            $vendedorPermissionIds = Permission::whereIn('slug', ['quotes.index', 'quotes.create', 'quotes.show', 'quotes.convert'])->pluck('id');
            $vendedorRole->permissions()->attach($vendedorPermissionIds);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::where('slug', 'like', 'quotes.%')->delete();
    }
};
