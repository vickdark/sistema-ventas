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
                'nombre' => 'Ver Auditoría',
                'slug' => 'activity-logs.index',
                'descripcion' => 'Permite ver el registro de actividad del sistema',
                'module' => 'Seguridad',
                'icon' => 'fa-solid fa-clock-rotate-left',
                'is_menu' => true,
                'order' => 110
            ],
            [
                'nombre' => 'Ver Detalle de Auditoría',
                'slug' => 'activity-logs.show',
                'descripcion' => 'Permite ver los cambios detallados de un registro de actividad',
                'module' => 'Seguridad',
                'is_menu' => false,
                'order' => 110
            ],
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(['slug' => $permissionData['slug']], $permissionData);
        }

        // Asignar al administrador
        $adminRole = Role::where('slug', 'admin')->first();
        if ($adminRole) {
            $permissionIds = Permission::whereIn('slug', ['activity-logs.index', 'activity-logs.show'])->pluck('id');
            $adminRole->permissions()->attach($permissionIds);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Permission::whereIn('slug', ['activity-logs.index', 'activity-logs.show'])->delete();
    }
};
