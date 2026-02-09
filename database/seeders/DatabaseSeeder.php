<?php

namespace Database\Seeders;

use App\Models\Usuarios\Usuario;
use App\Models\Roles\Role;
use App\Models\Roles\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear permisos iniciales
        $permissions = [
            ['nombre' => 'Ver Roles', 'slug' => 'roles.index', 'descripcion' => 'Permite ver la lista de roles'],
            ['nombre' => 'Crear Roles', 'slug' => 'roles.create', 'descripcion' => 'Permite crear nuevos roles'],
            ['nombre' => 'Editar Roles', 'slug' => 'roles.edit', 'descripcion' => 'Permite editar roles existentes'],
            ['nombre' => 'Eliminar Roles', 'slug' => 'roles.destroy', 'descripcion' => 'Permite eliminar roles'],
            
            ['nombre' => 'Ver Permisos', 'slug' => 'permissions.index', 'descripcion' => 'Permite ver la lista de permisos'],
            ['nombre' => 'Crear Permisos', 'slug' => 'permissions.create', 'descripcion' => 'Permite crear nuevos permisos'],
            ['nombre' => 'Editar Permisos', 'slug' => 'permissions.edit', 'descripcion' => 'Permite editar permisos existentes'],
            ['nombre' => 'Eliminar Permisos', 'slug' => 'permissions.destroy', 'descripcion' => 'Permite eliminar permisos'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['slug' => $permission['slug']], $permission);
        }

        // Crear roles iniciales
        $roles = [
            ['nombre' => 'Administrador', 'slug' => 'admin', 'descripcion' => 'Acceso total al sistema'],
            ['nombre' => 'Supervisor', 'slug' => 'supervisor', 'descripcion' => 'Acceso a gestión básica'],
            ['nombre' => 'Vendedor', 'slug' => 'vendedor', 'descripcion' => 'Acceso solo a ventas'],
        ];

        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(['slug' => $roleData['slug']], $roleData);

        }

        // Obtener el rol de administrador
        $adminRole = Role::where('slug', 'admin')->first();

        // Asignar todos los permisos al rol de administrador
        if ($adminRole) {
            $allPermissions = Permission::pluck('id');
            $adminRole->permissions()->sync($allPermissions);
        }

        // Crear el usuario administrador único
        Usuario::firstOrCreate(
            ['email' => 'victormanjarres3mayo@gmail.com'],
            [
                'role_id'  => $adminRole->id,
                'name'     => 'Administrador',
                'password' => Hash::make('admin123456789'),
            ]
        );
    }
}
