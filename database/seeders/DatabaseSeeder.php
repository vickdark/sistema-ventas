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
            ['nombre' => 'Dashboard', 'slug' => 'dashboard', 'descripcion' => 'Ver panel de control', 'module' => 'Dashboard', 'icon' => 'fa-solid fa-gauge-high', 'is_menu' => true, 'order' => 1],

            ['nombre' => 'Ver Roles', 'slug' => 'roles.index', 'descripcion' => 'Permite ver la lista de roles', 'module' => 'Seguridad', 'icon' => 'fa-solid fa-shield-halved', 'is_menu' => true, 'order' => 2],
            ['nombre' => 'Crear Roles', 'slug' => 'roles.create', 'descripcion' => 'Permite crear nuevos roles', 'module' => 'Seguridad', 'is_menu' => false],
            ['nombre' => 'Editar Roles', 'slug' => 'roles.edit', 'descripcion' => 'Permite editar roles existentes', 'module' => 'Seguridad', 'is_menu' => false],
            ['nombre' => 'Eliminar Roles', 'slug' => 'roles.destroy', 'descripcion' => 'Permite eliminar roles', 'module' => 'Seguridad', 'is_menu' => false],
            
            ['nombre' => 'Ver Permisos', 'slug' => 'permissions.index', 'descripcion' => 'Permite ver la lista de permisos', 'module' => 'Seguridad', 'icon' => 'fa-solid fa-key', 'is_menu' => false, 'order' => 3],
            ['nombre' => 'Crear Permisos', 'slug' => 'permissions.create', 'descripcion' => 'Permite crear nuevos permisos', 'module' => 'Seguridad', 'is_menu' => false],
            ['nombre' => 'Editar Permisos', 'slug' => 'permissions.edit', 'descripcion' => 'Permite editar permisos existentes', 'module' => 'Seguridad', 'is_menu' => false],
            ['nombre' => 'Eliminar Permisos', 'slug' => 'permissions.destroy', 'descripcion' => 'Permite eliminar permisos', 'module' => 'Seguridad', 'is_menu' => false],
            ['nombre' => 'Sincronizar Permisos', 'slug' => 'permissions.sync', 'descripcion' => 'Permite sincronizar permisos con las rutas', 'module' => 'Seguridad', 'is_menu' => false],

            ['nombre' => 'Productos', 'slug' => 'products.index', 'descripcion' => 'Gestión de productos', 'module' => 'Inventario', 'icon' => 'fa-solid fa-box', 'is_menu' => true, 'order' => 4],
            ['nombre' => 'Compras', 'slug' => 'purchases.index', 'descripcion' => 'Gestión de compras', 'module' => 'Inventario', 'icon' => 'fa-solid fa-cart-shopping', 'is_menu' => true, 'order' => 5],
            ['nombre' => 'Proveedores', 'slug' => 'suppliers.index', 'descripcion' => 'Gestión de proveedores', 'module' => 'Inventario', 'icon' => 'fa-solid fa-truck', 'is_menu' => true, 'order' => 6],
            ['nombre' => 'Categorías', 'slug' => 'categories.index', 'descripcion' => 'Gestión de categorías', 'module' => 'Inventario', 'icon' => 'fa-solid fa-tags', 'is_menu' => true, 'order' => 7],

            ['nombre' => 'Caja', 'slug' => 'cash-registers.index', 'descripcion' => 'Gestión de caja', 'module' => 'Caja', 'icon' => 'fa-solid fa-cash-register', 'is_menu' => true, 'order' => 8],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(['slug' => $permission['slug']], $permission);
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
