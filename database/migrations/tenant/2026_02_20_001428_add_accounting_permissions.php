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
                'nombre' => 'Ver Contabilidad',
                'slug' => 'accounting.index',
                'descripcion' => 'Permite ver el dashboard contable y reportes',
                'module' => 'Contabilidad',
                'icon' => 'fa-solid fa-calculator',
                'is_menu' => true,
                'order' => 100
            ],
            [
                'nombre' => 'Gestionar Plan de Cuentas',
                'slug' => 'accounts.index',
                'descripcion' => 'Permite crear y editar cuentas contables',
                'module' => 'Contabilidad',
                'is_menu' => true,
                'order' => 101,
            ],
            [
                'nombre' => 'Ver Libro Diario',
                'slug' => 'journal-entries.index',
                'descripcion' => 'Permite ver los asientos contables',
                'module' => 'Contabilidad',
                'is_menu' => true,
                'order' => 102,
            ],
            [
                'nombre' => 'Crear Asientos Manuales',
                'slug' => 'journal-entries.create',
                'descripcion' => 'Permite registrar asientos contables manualmente',
                'module' => 'Contabilidad',
                'is_menu' => false,
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(['slug' => $permissionData['slug']], $permissionData);
        }

        // Asignar al administrador automáticamente
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $permissionSlugs = collect($permissions)->pluck('slug')->toArray();
            $permissionIds = Permission::whereIn('slug', $permissionSlugs)->pluck('id');
            $adminRole->permissions()->syncWithoutDetaching($permissionIds);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $slugs = ['accounting.index', 'accounts.index', 'journal-entries.index', 'journal-entries.create'];
        Permission::whereIn('slug', $slugs)->delete();
    }
};
