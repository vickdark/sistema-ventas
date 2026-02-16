<?php

namespace Database\Seeders;

use App\Models\Tenant\Usuario;
use App\Models\Tenant\Role;
use App\Models\Tenant\Permission;
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
            ['nombre' => 'Tablero Principal', 'slug' => 'dashboard', 'is_menu' => 1, 'icon' => 'fa-solid fa-gauge-high', 'module' => 'Tablero', 'order' => 1, 'descripcion' => 'Acceso al tablero principal de estadísticas'],
            ['nombre' => 'Roles', 'slug' => 'roles.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Ver Rol en el sistema'],
            ['nombre' => 'Crear Rol', 'slug' => 'roles.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Crear Rol en el sistema'],
            ['nombre' => 'Editar Rol', 'slug' => 'roles.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Editar Rol en el sistema'],
            ['nombre' => 'Eliminar Rol', 'slug' => 'roles.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Eliminar Rol en el sistema'],
            ['nombre' => 'Ver Permisos', 'slug' => 'permissions.index', 'is_menu' => 0, 'icon' => 'fa-solid fa-key', 'module' => 'Seguridad', 'order' => 3, 'descripcion' => 'Permite ver la lista de permisos'],
            ['nombre' => 'Crear Permisos', 'slug' => 'permissions.create', 'is_menu' => 0, 'icon' => null, 'module' => 'Seguridad', 'order' => 0, 'descripcion' => 'Permite crear nuevos permisos'],
            ['nombre' => 'Editar Permisos', 'slug' => 'permissions.edit', 'is_menu' => 0, 'icon' => null, 'module' => 'Seguridad', 'order' => 0, 'descripcion' => 'Permite editar permisos existentes'],
            ['nombre' => 'Eliminar Permisos', 'slug' => 'permissions.destroy', 'is_menu' => 0, 'icon' => null, 'module' => 'Seguridad', 'order' => 0, 'descripcion' => 'Permite eliminar permisos'],
            ['nombre' => 'Sincronizar Permiso', 'slug' => 'permissions.sync', 'is_menu' => 0, 'icon' => 'fa-solid fa-key', 'module' => 'Configuración', 'order' => 102, 'descripcion' => 'Permite Sincronizar Permiso en el sistema'],
            ['nombre' => 'Productos', 'slug' => 'products.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 10, 'descripcion' => 'Permite Ver Producto en el sistema'],
            ['nombre' => 'Compras', 'slug' => 'purchases.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Inventario', 'order' => 12, 'descripcion' => 'Permite Ver Compra en el sistema'],
            ['nombre' => 'Ventas', 'slug' => 'sales.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 20, 'descripcion' => 'Permite Ver Venta en el sistema'],
            ['nombre' => 'Proveedores', 'slug' => 'suppliers.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-truck', 'module' => 'Inventario', 'order' => 13, 'descripcion' => 'Permite Ver Proveedor en el sistema'],
            ['nombre' => 'Categorías', 'slug' => 'categories.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 11, 'descripcion' => 'Permite Ver Categoría en el sistema'],
            ['nombre' => 'Cajas', 'slug' => 'cash-registers.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-vault', 'module' => 'Caja', 'order' => 30, 'descripcion' => 'Permite Ver Caja en el sistema'],
            ['nombre' => 'Dashboard Admin', 'slug' => 'dashboard.admin', 'is_menu' => 0, 'icon' => 'fa-solid fa-circle-dot', 'module' => 'General', 'order' => 50, 'descripcion' => 'Vista de panel principal personalizada para el rol admin'],
            ['nombre' => 'Dashboard Vendedor', 'slug' => 'dashboard.vendedor', 'is_menu' => 0, 'icon' => 'fa-solid fa-circle-dot', 'module' => 'General', 'order' => 50, 'descripcion' => 'Vista de panel principal personalizada para el rol vendedor'],
            ['nombre' => 'Usuarios', 'slug' => 'usuarios.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Ver Usuario en el sistema'],
            ['nombre' => 'Crear Usuario', 'slug' => 'usuarios.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Crear Usuario en el sistema'],
            ['nombre' => 'Guardar Usuario', 'slug' => 'usuarios.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Guardar Usuario en el sistema'],
            ['nombre' => 'Ver Detalle Usuario', 'slug' => 'usuarios.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Ver Detalle Usuario en el sistema'],
            ['nombre' => 'Editar Usuario', 'slug' => 'usuarios.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Editar Usuario en el sistema'],
            ['nombre' => 'Actualizar Usuario', 'slug' => 'usuarios.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Actualizar Usuario en el sistema'],
            ['nombre' => 'Eliminar Usuario', 'slug' => 'usuarios.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Eliminar Usuario en el sistema'],
            ['nombre' => 'Guardar Rol', 'slug' => 'roles.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Guardar Rol en el sistema'],
            ['nombre' => 'Ver Detalle Rol', 'slug' => 'roles.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Ver Detalle Rol en el sistema'],
            ['nombre' => 'Actualizar Rol', 'slug' => 'roles.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Actualizar Rol en el sistema'],
            ['nombre' => 'Clientes', 'slug' => 'clients.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 22, 'descripcion' => 'Permite Ver Cliente en el sistema'],
            ['nombre' => 'Crear Cliente', 'slug' => 'clients.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 22, 'descripcion' => 'Permite Crear Cliente en el sistema'],
            ['nombre' => 'Guardar Cliente', 'slug' => 'clients.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 22, 'descripcion' => 'Permite Guardar Cliente en el sistema'],
            ['nombre' => 'Ver Detalle Cliente', 'slug' => 'clients.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 22, 'descripcion' => 'Permite Ver Detalle Cliente en el sistema'],
            ['nombre' => 'Editar Cliente', 'slug' => 'clients.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 22, 'descripcion' => 'Permite Editar Cliente en el sistema'],
            ['nombre' => 'Actualizar Cliente', 'slug' => 'clients.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 22, 'descripcion' => 'Permite Actualizar Cliente en el sistema'],
            ['nombre' => 'Eliminar Cliente', 'slug' => 'clients.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 22, 'descripcion' => 'Permite Eliminar Cliente en el sistema'],
            ['nombre' => 'Crear Producto', 'slug' => 'products.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 10, 'descripcion' => 'Permite Crear Producto en el sistema'],
            ['nombre' => 'Guardar Producto', 'slug' => 'products.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 10, 'descripcion' => 'Permite Guardar Producto en el sistema'],
            ['nombre' => 'Ver Detalle Producto', 'slug' => 'products.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 10, 'descripcion' => 'Permite Ver Detalle Producto en el sistema'],
            ['nombre' => 'Editar Producto', 'slug' => 'products.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 10, 'descripcion' => 'Permite Editar Producto en el sistema'],
            ['nombre' => 'Actualizar Producto', 'slug' => 'products.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 10, 'descripcion' => 'Permite Actualizar Producto en el sistema'],
            ['nombre' => 'Eliminar Producto', 'slug' => 'products.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 10, 'descripcion' => 'Permite Eliminar Producto en el sistema'],
            ['nombre' => 'Crear Categoría', 'slug' => 'categories.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 11, 'descripcion' => 'Permite Crear Categoría en el sistema'],
            ['nombre' => 'Guardar Categoría', 'slug' => 'categories.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 11, 'descripcion' => 'Permite Guardar Categoría en el sistema'],
            ['nombre' => 'Ver Detalle Categoría', 'slug' => 'categories.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 11, 'descripcion' => 'Permite Ver Detalle Categoría en el sistema'],
            ['nombre' => 'Editar Categoría', 'slug' => 'categories.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 11, 'descripcion' => 'Permite Editar Categoría en el sistema'],
            ['nombre' => 'Actualizar Categoría', 'slug' => 'categories.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 11, 'descripcion' => 'Permite Actualizar Categoría en el sistema'],
            ['nombre' => 'Eliminar Categoría', 'slug' => 'categories.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 11, 'descripcion' => 'Permite Eliminar Categoría en el sistema'],
            ['nombre' => 'Crear Compra', 'slug' => 'purchases.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Inventario', 'order' => 12, 'descripcion' => 'Permite Crear Compra en el sistema'],
            ['nombre' => 'Guardar Compra', 'slug' => 'purchases.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Inventario', 'order' => 12, 'descripcion' => 'Permite Guardar Compra en el sistema'],
            ['nombre' => 'Ver Detalle Compra', 'slug' => 'purchases.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Inventario', 'order' => 12, 'descripcion' => 'Permite Ver Detalle Compra en el sistema'],
            ['nombre' => 'Editar Compra', 'slug' => 'purchases.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Inventario', 'order' => 12, 'descripcion' => 'Permite Editar Compra en el sistema'],
            ['nombre' => 'Actualizar Compra', 'slug' => 'purchases.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Inventario', 'order' => 12, 'descripcion' => 'Permite Actualizar Compra en el sistema'],
            ['nombre' => 'Eliminar Compra', 'slug' => 'purchases.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Inventario', 'order' => 12, 'descripcion' => 'Permite Eliminar Compra en el sistema'],
            ['nombre' => 'Crear Proveedor', 'slug' => 'suppliers.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Inventario', 'order' => 13, 'descripcion' => 'Permite Crear Proveedor en el sistema'],
            ['nombre' => 'Guardar Proveedor', 'slug' => 'suppliers.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Inventario', 'order' => 13, 'descripcion' => 'Permite Guardar Proveedor en el sistema'],
            ['nombre' => 'Ver Detalle Proveedor', 'slug' => 'suppliers.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Inventario', 'order' => 13, 'descripcion' => 'Permite Ver Detalle Proveedor en el sistema'],
            ['nombre' => 'Editar Proveedor', 'slug' => 'suppliers.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Inventario', 'order' => 13, 'descripcion' => 'Permite Editar Proveedor en el sistema'],
            ['nombre' => 'Actualizar Proveedor', 'slug' => 'suppliers.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Inventario', 'order' => 13, 'descripcion' => 'Permite Actualizar Proveedor en el sistema'],
            ['nombre' => 'Eliminar Proveedor', 'slug' => 'suppliers.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Inventario', 'order' => 13, 'descripcion' => 'Permite Eliminar Proveedor en el sistema'],
            ['nombre' => 'Crear Venta', 'slug' => 'sales.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 20, 'descripcion' => 'Permite Crear Venta en el sistema'],
            ['nombre' => 'Guardar Venta', 'slug' => 'sales.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 20, 'descripcion' => 'Permite Guardar Venta en el sistema'],
            ['nombre' => 'Ver Detalle Venta', 'slug' => 'sales.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 20, 'descripcion' => 'Permite Ver Detalle Venta en el sistema'],
            ['nombre' => 'Editar Venta', 'slug' => 'sales.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 20, 'descripcion' => 'Permite Editar Venta en el sistema'],
            ['nombre' => 'Actualizar Venta', 'slug' => 'sales.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 20, 'descripcion' => 'Permite Actualizar Venta en el sistema'],
            ['nombre' => 'Eliminar Venta', 'slug' => 'sales.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 20, 'descripcion' => 'Permite Eliminar Venta en el sistema'],
            ['nombre' => 'Crear Caja', 'slug' => 'cash-registers.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Caja', 'order' => 30, 'descripcion' => 'Permite Crear Caja en el sistema'],
            ['nombre' => 'Guardar Caja', 'slug' => 'cash-registers.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Caja', 'order' => 30, 'descripcion' => 'Permite Guardar Caja en el sistema'],
            ['nombre' => 'Ver Detalle Caja', 'slug' => 'cash-registers.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Caja', 'order' => 30, 'descripcion' => 'Permite Ver Detalle Caja en el sistema'],
            ['nombre' => 'Editar Caja', 'slug' => 'cash-registers.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Caja', 'order' => 30, 'descripcion' => 'Permite Editar Caja en el sistema'],
            ['nombre' => 'Actualizar Caja', 'slug' => 'cash-registers.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Caja', 'order' => 30, 'descripcion' => 'Permite Actualizar Caja en el sistema'],
            ['nombre' => 'Eliminar Caja', 'slug' => 'cash-registers.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Caja', 'order' => 30, 'descripcion' => 'Permite Eliminar Caja en el sistema'],
            ['nombre' => 'Cierre Caja', 'slug' => 'cash-registers.close-form', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Caja', 'order' => 30, 'descripcion' => 'Permite Cierre Caja en el sistema'],
            ['nombre' => 'Cerrar Caja', 'slug' => 'cash-registers.close', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Caja', 'order' => 30, 'descripcion' => 'Permite Cerrar Caja en el sistema'],
            ['nombre' => 'Actualizar configurations', 'slug' => 'configurations.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-circle-dot', 'module' => 'General', 'order' => 50, 'descripcion' => 'Permite Actualizar configurations en el sistema'],
            ['nombre' => 'Edit_permissions Rol', 'slug' => 'roles.edit_permissions', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Edit_permissions Rol en el sistema'],
            ['nombre' => 'Update_permissions Rol', 'slug' => 'roles.update_permissions', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Update_permissions Rol en el sistema'],
            ['nombre' => 'Abonos', 'slug' => 'abonos.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 21, 'descripcion' => 'Permite Ver Abono en el sistema'],
            ['nombre' => 'Crear Abono', 'slug' => 'abonos.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 21, 'descripcion' => 'Permite Crear Abono en el sistema'],
            ['nombre' => 'Guardar Abono', 'slug' => 'abonos.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 21, 'descripcion' => 'Permite Guardar Abono en el sistema'],
            ['nombre' => 'Ver Detalle Abono', 'slug' => 'abonos.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 21, 'descripcion' => 'Permite Ver Detalle Abono en el sistema'],
            ['nombre' => 'Editar Abono', 'slug' => 'abonos.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 21, 'descripcion' => 'Permite Editar Abono en el sistema'],
            ['nombre' => 'Actualizar Abono', 'slug' => 'abonos.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 21, 'descripcion' => 'Permite Actualizar Abono en el sistema'],
            ['nombre' => 'Eliminar Abono', 'slug' => 'abonos.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 21, 'descripcion' => 'Permite Eliminar Abono en el sistema'],
            ['nombre' => 'Pending-sales Abono', 'slug' => 'abonos.pending-sales', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 21, 'descripcion' => 'Permite Pending-sales Abono en el sistema'],
            ['nombre' => 'Debt-summary Abono', 'slug' => 'abonos.debt-summary', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 21, 'descripcion' => 'Permite Debt-summary Abono en el sistema'],
            ['nombre' => 'Client-history Abono', 'slug' => 'abonos.client-history', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 21, 'descripcion' => 'Permite Client-history Abono en el sistema'],
            ['nombre' => 'Reportes', 'slug' => 'reports.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Análisis', 'order' => 40, 'descripcion' => 'Permite Ver Reporte en el sistema'],
            ['nombre' => 'Crear Reporte', 'slug' => 'reports.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Análisis', 'order' => 40, 'descripcion' => 'Permite Crear Reporte en el sistema'],
            ['nombre' => 'Guardar Reporte', 'slug' => 'reports.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Análisis', 'order' => 40, 'descripcion' => 'Permite Guardar Reporte en el sistema'],
            ['nombre' => 'Ver Detalle Reporte', 'slug' => 'reports.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Análisis', 'order' => 40, 'descripcion' => 'Permite Ver Detalle Reporte en el sistema'],
            ['nombre' => 'Editar Reporte', 'slug' => 'reports.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Análisis', 'order' => 40, 'descripcion' => 'Permite Editar Reporte en el sistema'],
            ['nombre' => 'Actualizar Reporte', 'slug' => 'reports.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Análisis', 'order' => 40, 'descripcion' => 'Permite Actualizar Reporte en el sistema'],
            ['nombre' => 'Eliminar Reporte', 'slug' => 'reports.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Análisis', 'order' => 40, 'descripcion' => 'Permite Eliminar Reporte en el sistema'],
            ['nombre' => 'Voucher Compra', 'slug' => 'purchases.voucher', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Inventario', 'order' => 12, 'descripcion' => 'Permite Voucher Compra en el sistema'],
            ['nombre' => 'Ticket Venta', 'slug' => 'sales.ticket', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 20, 'descripcion' => 'Permite Ticket Venta en el sistema'],
            ['nombre' => 'Low-stock notifications', 'slug' => 'notifications.low-stock', 'is_menu' => 0, 'icon' => 'fa-solid fa-circle-dot', 'module' => 'General', 'order' => 50, 'descripcion' => 'Permite Low-stock notifications en el sistema'],
            ['nombre' => 'Importación Masiva', 'slug' => 'import.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-file-import', 'module' => 'Herramientas', 'order' => 5, 'descripcion' => 'Permite Ver Importación en el sistema'],
            ['nombre' => 'Template Importación', 'slug' => 'import.template', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Herramientas', 'order' => 5, 'descripcion' => 'Permite Template Importación en el sistema'],
            ['nombre' => 'Categories Importación', 'slug' => 'import.categories', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Herramientas', 'order' => 5, 'descripcion' => 'Permite Categories Importación en el sistema'],
            ['nombre' => 'Clients Importación', 'slug' => 'import.clients', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Herramientas', 'order' => 5, 'descripcion' => 'Permite Clients Importación en el sistema'],
            ['nombre' => 'Suppliers Importación', 'slug' => 'import.suppliers', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Herramientas', 'order' => 5, 'descripcion' => 'Permite Suppliers Importación en el sistema'],
            ['nombre' => 'Products Importación', 'slug' => 'import.products', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Herramientas', 'order' => 5, 'descripcion' => 'Permite Products Importación en el sistema'],
            ['nombre' => 'Purchases Importación', 'slug' => 'import.purchases', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Herramientas', 'order' => 5, 'descripcion' => 'Permite Purchases Importación en el sistema'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::updateOrCreate(['slug' => $permissionData['slug']], $permissionData);
        }

        $this->command->info('Permisos creados correctamente.');

        // 2. Crear o actualizar roles iniciales
        $roles = [
            [
                'nombre' => 'Administrador',
                'slug' => 'admin',
                'descripcion' => 'Acceso total al sistema, configuración y gestión de seguridad.'
            ],
            [
                'nombre' => 'Supervisor',
                'slug' => 'supervisor',
                'descripcion' => 'Acceso a gestión operativa, inventarios y reportes detallados.'
            ],
            [
                'nombre' => 'Vendedor',
                'slug' => 'vendedor',
                'descripcion' => 'Acceso limitado a ventas, clientes y consultas básicas.'
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(['slug' => $roleData['slug']], $roleData);
        }

        // 3. Obtener el rol de administrador
        $adminRole = Role::where('slug', 'admin')->first();

        // 4. Asignar TODOS los permisos manuales al rol de administrador
        if ($adminRole) {
            $allPermissions = Permission::all();
            $adminRole->permissions()->sync($allPermissions->pluck('id'));
            $this->command->info("Se han sincronizado {$allPermissions->count()} permisos al rol de Administrador.");
        }

        // 4.1 Asignar permisos al rol de Vendedor
        $vendedorRole = Role::where('slug', 'vendedor')->first();
        if ($vendedorRole) {
            $vendedorPermissions = Permission::where(function($query) {
                $query->where('slug', 'dashboard')
                      ->orWhere('slug', 'dashboard.vendedor')
                      ->orWhere('slug', 'like', 'sales.%')
                      ->orWhere('slug', 'like', 'clients.%')
                      ->orWhere('slug', 'like', 'cash-registers.%')
                      ->orWhere('slug', 'like', 'abonos.%');
            })->get();

            $vendedorRole->permissions()->sync($vendedorPermissions->pluck('id'));
            $this->command->info("Se han sincronizado {$vendedorPermissions->count()} permisos al rol de Vendedor.");
        }

        // 5. Asegurar la existencia del usuario administrador y su vinculación
        $adminEmail = 'victormanjarres3mayo@gmail.com';
        $adminUser = Usuario::updateOrCreate(
            ['email' => $adminEmail],
            [
                'role_id'  => $adminRole->id,
                'name'     => 'Administrador',
                'password' => Hash::make('admin123456789'),
            ]
        );

        $this->command->info("Usuario administrador ({$adminEmail}) configurado correctamente.");
        $this->command->info('Sembrado completado con éxito.');
    }
}
