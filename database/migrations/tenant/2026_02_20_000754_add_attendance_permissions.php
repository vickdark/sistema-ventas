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
                'nombre' => 'Control Asistencia',
                'slug' => 'attendance.index',
                'descripcion' => 'Permite ver el historial personal de asistencia',
                'module' => 'RRHH',
                'icon' => 'fa-solid fa-user-clock',
                'is_menu' => true,
                'order' => 90
            ],
            [
                'nombre' => 'Marcar Entrada/Salida',
                'slug' => 'attendance.clock-in',
                'descripcion' => 'Permite realizar las marcas de entrada y salida',
                'module' => 'RRHH',
                'is_menu' => false,
            ],
            [
                'nombre' => 'Ver Estado Asistencia',
                'slug' => 'attendance.status',
                'descripcion' => 'Permite consultar el estado actual (en turno/fuera)',
                'module' => 'RRHH',
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
        $slugs = ['attendance.index', 'attendance.clock-in', 'attendance.status'];
        Permission::whereIn('slug', $slugs)->delete();
    }
};
