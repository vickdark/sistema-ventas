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
        // Crear permisos iniciales (Sincronizados con SyncPermissions.php)
        $permissions = [
            // Dashboard
            ['nombre' => 'Tablero Principal', 'slug' => 'dashboard', 'is_menu' => 1, 'icon' => 'fa-solid fa-gauge-high', 'module' => 'Tablero', 'order' => 1, 'descripcion' => 'Acceso al tablero principal de estadísticas'],
            ['nombre' => 'Dashboard Admin', 'slug' => 'dashboard.admin', 'is_menu' => 0, 'icon' => 'fa-solid fa-gauge-high', 'module' => 'Tablero', 'order' => 1, 'descripcion' => 'Vista de panel principal personalizada para el rol admin'],
            ['nombre' => 'Dashboard Vendedor', 'slug' => 'dashboard.vendedor', 'is_menu' => 0, 'icon' => 'fa-solid fa-gauge-high', 'module' => 'Tablero', 'order' => 1, 'descripcion' => 'Vista de panel principal personalizada para el rol vendedor'],

            // Ventas (Order: 10-16)
            // Sales (10)
            ['nombre' => 'Ventas', 'slug' => 'sales.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 10, 'descripcion' => 'Permite Ver Venta en el sistema'],
            ['nombre' => 'Crear Venta', 'slug' => 'sales.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 10, 'descripcion' => 'Permite Crear Venta en el sistema'],
            ['nombre' => 'Guardar Venta', 'slug' => 'sales.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 10, 'descripcion' => 'Permite Guardar Venta en el sistema'],
            ['nombre' => 'Ver Detalle Venta', 'slug' => 'sales.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 10, 'descripcion' => 'Permite Ver Detalle Venta en el sistema'],
            ['nombre' => 'Editar Venta', 'slug' => 'sales.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 10, 'descripcion' => 'Permite Editar Venta en el sistema'],
            ['nombre' => 'Actualizar Venta', 'slug' => 'sales.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 10, 'descripcion' => 'Permite Actualizar Venta en el sistema'],
            ['nombre' => 'Eliminar Venta', 'slug' => 'sales.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 10, 'descripcion' => 'Permite Eliminar Venta en el sistema'],
            ['nombre' => 'Ticket Venta', 'slug' => 'sales.ticket', 'is_menu' => 0, 'icon' => 'fa-solid fa-cash-register', 'module' => 'Ventas', 'order' => 10, 'descripcion' => 'Permite Ticket Venta en el sistema'],

            // Cotizaciones (12)
            ['nombre' => 'Cotizaciones', 'slug' => 'quotes.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-file-invoice', 'module' => 'Ventas', 'order' => 12, 'descripcion' => 'Permite Ver Cotización en el sistema'],
            ['nombre' => 'Crear Cotización', 'slug' => 'quotes.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-invoice', 'module' => 'Ventas', 'order' => 12, 'descripcion' => 'Permite Crear Cotización en el sistema'],
            ['nombre' => 'Guardar Cotización', 'slug' => 'quotes.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-invoice', 'module' => 'Ventas', 'order' => 12, 'descripcion' => 'Permite Guardar Cotización en el sistema'],
            ['nombre' => 'Convertir Cotización', 'slug' => 'quotes.convert', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-invoice', 'module' => 'Ventas', 'order' => 12, 'descripcion' => 'Permite Convertir Cotización en el sistema'],
            ['nombre' => 'Ver Detalle Cotización', 'slug' => 'quotes.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-invoice', 'module' => 'Ventas', 'order' => 12, 'descripcion' => 'Permite Ver Detalle Cotización en el sistema'],
            ['nombre' => 'Editar Cotización', 'slug' => 'quotes.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-invoice', 'module' => 'Ventas', 'order' => 12, 'descripcion' => 'Permite Editar Cotización en el sistema'],
            ['nombre' => 'Actualizar Cotización', 'slug' => 'quotes.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-invoice', 'module' => 'Ventas', 'order' => 12, 'descripcion' => 'Permite Actualizar Cotización en el sistema'],
            ['nombre' => 'Eliminar Cotización', 'slug' => 'quotes.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-invoice', 'module' => 'Ventas', 'order' => 12, 'descripcion' => 'Permite Eliminar Cotización en el sistema'],

            // Clients (13)
            ['nombre' => 'Clientes', 'slug' => 'clients.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 13, 'descripcion' => 'Permite Ver Cliente en el sistema'],
            ['nombre' => 'Crear Cliente', 'slug' => 'clients.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 13, 'descripcion' => 'Permite Crear Cliente en el sistema'],
            ['nombre' => 'Guardar Cliente', 'slug' => 'clients.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 13, 'descripcion' => 'Permite Guardar Cliente en el sistema'],
            ['nombre' => 'Ver Detalle Cliente', 'slug' => 'clients.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 13, 'descripcion' => 'Permite Ver Detalle Cliente en el sistema'],
            ['nombre' => 'Editar Cliente', 'slug' => 'clients.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 13, 'descripcion' => 'Permite Editar Cliente en el sistema'],
            ['nombre' => 'Actualizar Cliente', 'slug' => 'clients.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 13, 'descripcion' => 'Permite Actualizar Cliente en el sistema'],
            ['nombre' => 'Eliminar Cliente', 'slug' => 'clients.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-address-book', 'module' => 'Ventas', 'order' => 13, 'descripcion' => 'Permite Eliminar Cliente en el sistema'],

            // Abonos (14)
            ['nombre' => 'Abonos', 'slug' => 'abonos.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 14, 'descripcion' => 'Permite Ver Abono en el sistema'],
            ['nombre' => 'Crear Abono', 'slug' => 'abonos.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 14, 'descripcion' => 'Permite Crear Abono en el sistema'],
            ['nombre' => 'Guardar Abono', 'slug' => 'abonos.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 14, 'descripcion' => 'Permite Guardar Abono en el sistema'],
            ['nombre' => 'Ver Detalle Abono', 'slug' => 'abonos.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 14, 'descripcion' => 'Permite Ver Detalle Abono en el sistema'],
            ['nombre' => 'Editar Abono', 'slug' => 'abonos.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 14, 'descripcion' => 'Permite Editar Abono en el sistema'],
            ['nombre' => 'Actualizar Abono', 'slug' => 'abonos.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 14, 'descripcion' => 'Permite Actualizar Abono en el sistema'],
            ['nombre' => 'Eliminar Abono', 'slug' => 'abonos.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 14, 'descripcion' => 'Permite Eliminar Abono en el sistema'],
            ['nombre' => 'Pending-sales Abono', 'slug' => 'abonos.pending-sales', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 14, 'descripcion' => 'Permite Pending-sales Abono en el sistema'],
            ['nombre' => 'Debt-summary Abono', 'slug' => 'abonos.debt-summary', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 14, 'descripcion' => 'Permite Debt-summary Abono en el sistema'],
            ['nombre' => 'Client-history Abono', 'slug' => 'abonos.client-history', 'is_menu' => 0, 'icon' => 'fa-solid fa-hand-holding-dollar', 'module' => 'Ventas', 'order' => 14, 'descripcion' => 'Permite Client-history Abono en el sistema'],

            // Credit Notes (15)
            ['nombre' => 'Notas de Crédito', 'slug' => 'credit-notes.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-file-invoice-dollar', 'module' => 'Ventas', 'order' => 15, 'descripcion' => 'Permite Ver Notas de Crédito y Devoluciones'],
            ['nombre' => 'Crear Nota de Crédito', 'slug' => 'credit-notes.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-invoice-dollar', 'module' => 'Ventas', 'order' => 15, 'descripcion' => 'Permite Crear Nota de Crédito'],
            ['nombre' => 'Guardar Nota de Crédito', 'slug' => 'credit-notes.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-invoice-dollar', 'module' => 'Ventas', 'order' => 15, 'descripcion' => 'Permite Guardar Nota de Crédito'],
            ['nombre' => 'Ver Detalle Nota de Crédito', 'slug' => 'credit-notes.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-invoice-dollar', 'module' => 'Ventas', 'order' => 15, 'descripcion' => 'Permite Ver Detalle Nota de Crédito'],
            ['nombre' => 'Anular Nota de Crédito', 'slug' => 'credit-notes.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-invoice-dollar', 'module' => 'Ventas', 'order' => 15, 'descripcion' => 'Permite Anular Nota de Crédito'],

            // Cash Registers (16)
            ['nombre' => 'Cajas', 'slug' => 'cash-registers.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-vault', 'module' => 'Ventas', 'order' => 16, 'descripcion' => 'Permite Ver Caja en el sistema'],
            ['nombre' => 'Crear Caja', 'slug' => 'cash-registers.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Ventas', 'order' => 16, 'descripcion' => 'Permite Crear Caja en el sistema'],
            ['nombre' => 'Guardar Caja', 'slug' => 'cash-registers.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Ventas', 'order' => 16, 'descripcion' => 'Permite Guardar Caja en el sistema'],
            ['nombre' => 'Ver Detalle Caja', 'slug' => 'cash-registers.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Ventas', 'order' => 16, 'descripcion' => 'Permite Ver Detalle Caja en el sistema'],
            ['nombre' => 'Editar Caja', 'slug' => 'cash-registers.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Ventas', 'order' => 16, 'descripcion' => 'Permite Editar Caja en el sistema'],
            ['nombre' => 'Actualizar Caja', 'slug' => 'cash-registers.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Ventas', 'order' => 16, 'descripcion' => 'Permite Actualizar Caja en el sistema'],
            ['nombre' => 'Eliminar Caja', 'slug' => 'cash-registers.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Ventas', 'order' => 16, 'descripcion' => 'Permite Eliminar Caja en el sistema'],
            ['nombre' => 'Cierre Caja', 'slug' => 'cash-registers.close-form', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Ventas', 'order' => 16, 'descripcion' => 'Permite Cierre Caja en el sistema'],
            ['nombre' => 'Cerrar Caja', 'slug' => 'cash-registers.close', 'is_menu' => 0, 'icon' => 'fa-solid fa-vault', 'module' => 'Ventas', 'order' => 16, 'descripcion' => 'Permite Cerrar Caja en el sistema'],

            // Inventario (Order: 20-23)
            // Products (20)
            ['nombre' => 'Productos', 'slug' => 'products.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 20, 'descripcion' => 'Permite Ver Producto en el sistema'],
            ['nombre' => 'Crear Producto', 'slug' => 'products.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 20, 'descripcion' => 'Permite Crear Producto en el sistema'],
            ['nombre' => 'Guardar Producto', 'slug' => 'products.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 20, 'descripcion' => 'Permite Guardar Producto en el sistema'],
            ['nombre' => 'Ver Detalle Producto', 'slug' => 'products.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 20, 'descripcion' => 'Permite Ver Detalle Producto en el sistema'],
            ['nombre' => 'Editar Producto', 'slug' => 'products.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 20, 'descripcion' => 'Permite Editar Producto en el sistema'],
            ['nombre' => 'Actualizar Producto', 'slug' => 'products.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 20, 'descripcion' => 'Permite Actualizar Producto en el sistema'],
            ['nombre' => 'Eliminar Producto', 'slug' => 'products.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-box', 'module' => 'Inventario', 'order' => 20, 'descripcion' => 'Permite Eliminar Producto en el sistema'],

            // Categories (21)
            ['nombre' => 'Categorías', 'slug' => 'categories.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 21, 'descripcion' => 'Permite Ver Categoría en el sistema'],
            ['nombre' => 'Crear Categoría', 'slug' => 'categories.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 21, 'descripcion' => 'Permite Crear Categoría en el sistema'],
            ['nombre' => 'Guardar Categoría', 'slug' => 'categories.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 21, 'descripcion' => 'Permite Guardar Categoría en el sistema'],
            ['nombre' => 'Ver Detalle Categoría', 'slug' => 'categories.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 21, 'descripcion' => 'Permite Ver Detalle Categoría en el sistema'],
            ['nombre' => 'Editar Categoría', 'slug' => 'categories.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 21, 'descripcion' => 'Permite Editar Categoría en el sistema'],
            ['nombre' => 'Actualizar Categoría', 'slug' => 'categories.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 21, 'descripcion' => 'Permite Actualizar Categoría en el sistema'],
            ['nombre' => 'Eliminar Categoría', 'slug' => 'categories.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Inventario', 'order' => 21, 'descripcion' => 'Permite Eliminar Categoría en el sistema'],

            // Inventory (22)
            ['nombre' => 'Inventario', 'slug' => 'inventory.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-boxes-stacked', 'module' => 'Inventario', 'order' => 22, 'descripcion' => 'Permite Ver Inventario en el sistema'],
            ['nombre' => 'Kardex Inventario', 'slug' => 'inventory.kardex', 'is_menu' => 0, 'icon' => 'fa-solid fa-boxes-stacked', 'module' => 'Inventario', 'order' => 22, 'descripcion' => 'Permite Ver Kardex en el sistema'],
            ['nombre' => 'Ajustar Inventario', 'slug' => 'inventory.adjust', 'is_menu' => 0, 'icon' => 'fa-solid fa-boxes-stacked', 'module' => 'Inventario', 'order' => 22, 'descripcion' => 'Permite Ajustar Inventario en el sistema'],

            // Stock Transfers (23)
            ['nombre' => 'Traslados de Stock', 'slug' => 'stock-transfers.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-truck-ramp-box', 'module' => 'Inventario', 'order' => 23, 'descripcion' => 'Permite Ver Traslado de Stock en el sistema'],
            ['nombre' => 'Crear Traslado', 'slug' => 'stock-transfers.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck-ramp-box', 'module' => 'Inventario', 'order' => 23, 'descripcion' => 'Permite Crear Traslado de Stock en el sistema'],
            ['nombre' => 'Guardar Traslado', 'slug' => 'stock-transfers.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck-ramp-box', 'module' => 'Inventario', 'order' => 23, 'descripcion' => 'Permite Guardar Traslado de Stock en el sistema'],
            ['nombre' => 'Ver Detalle Traslado', 'slug' => 'stock-transfers.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck-ramp-box', 'module' => 'Inventario', 'order' => 23, 'descripcion' => 'Permite Ver Detalle Traslado de Stock en el sistema'],
            ['nombre' => 'Recibir Traslado', 'slug' => 'stock-transfers.receive', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck-ramp-box', 'module' => 'Inventario', 'order' => 23, 'descripcion' => 'Permite Recibir Traslado de Stock en el sistema'],

            // Compras (Order: 30-32)
            // Purchases (30)
            ['nombre' => 'Compras', 'slug' => 'purchases.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Compras', 'order' => 30, 'descripcion' => 'Permite Ver Compra en el sistema'],
            ['nombre' => 'Crear Compra', 'slug' => 'purchases.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Compras', 'order' => 30, 'descripcion' => 'Permite Crear Compra en el sistema'],
            ['nombre' => 'Guardar Compra', 'slug' => 'purchases.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Compras', 'order' => 30, 'descripcion' => 'Permite Guardar Compra en el sistema'],
            ['nombre' => 'Ver Detalle Compra', 'slug' => 'purchases.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Compras', 'order' => 30, 'descripcion' => 'Permite Ver Detalle Compra en el sistema'],
            ['nombre' => 'Editar Compra', 'slug' => 'purchases.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Compras', 'order' => 30, 'descripcion' => 'Permite Editar Compra en el sistema'],
            ['nombre' => 'Actualizar Compra', 'slug' => 'purchases.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Compras', 'order' => 30, 'descripcion' => 'Permite Actualizar Compra en el sistema'],
            ['nombre' => 'Eliminar Compra', 'slug' => 'purchases.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Compras', 'order' => 30, 'descripcion' => 'Permite Eliminar Compra en el sistema'],
            ['nombre' => 'Voucher Compra', 'slug' => 'purchases.voucher', 'is_menu' => 0, 'icon' => 'fa-solid fa-cart-shopping', 'module' => 'Compras', 'order' => 30, 'descripcion' => 'Permite Voucher Compra en el sistema'],

            // Suppliers (31)
            ['nombre' => 'Proveedores', 'slug' => 'suppliers.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-truck', 'module' => 'Compras', 'order' => 31, 'descripcion' => 'Permite Ver Proveedor en el sistema'],
            ['nombre' => 'Crear Proveedor', 'slug' => 'suppliers.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Compras', 'order' => 31, 'descripcion' => 'Permite Crear Proveedor en el sistema'],
            ['nombre' => 'Guardar Proveedor', 'slug' => 'suppliers.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Compras', 'order' => 31, 'descripcion' => 'Permite Guardar Proveedor en el sistema'],
            ['nombre' => 'Ver Detalle Proveedor', 'slug' => 'suppliers.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Compras', 'order' => 31, 'descripcion' => 'Permite Ver Detalle Proveedor en el sistema'],
            ['nombre' => 'Editar Proveedor', 'slug' => 'suppliers.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Compras', 'order' => 31, 'descripcion' => 'Permite Editar Proveedor en el sistema'],
            ['nombre' => 'Actualizar Proveedor', 'slug' => 'suppliers.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Compras', 'order' => 31, 'descripcion' => 'Permite Actualizar Proveedor en el sistema'],
            ['nombre' => 'Eliminar Proveedor', 'slug' => 'suppliers.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-truck', 'module' => 'Compras', 'order' => 31, 'descripcion' => 'Permite Eliminar Proveedor en el sistema'],

            // Supplier Payments (32)
            ['nombre' => 'Cuentas por Pagar', 'slug' => 'supplier-payments.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-money-bill-transfer', 'module' => 'Compras', 'order' => 32, 'descripcion' => 'Permite Ver Cuentas por Pagar en el sistema'],
            ['nombre' => 'Crear Abono a Proveedor', 'slug' => 'supplier-payments.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-transfer', 'module' => 'Compras', 'order' => 32, 'descripcion' => 'Permite Crear Abono a Proveedor en el sistema'],
            ['nombre' => 'Guardar Abono a Proveedor', 'slug' => 'supplier-payments.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-transfer', 'module' => 'Compras', 'order' => 32, 'descripcion' => 'Permite Guardar Abono a Proveedor en el sistema'],
            ['nombre' => 'Ver Detalle Abono a Proveedor', 'slug' => 'supplier-payments.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-transfer', 'module' => 'Compras', 'order' => 32, 'descripcion' => 'Permite Ver Detalle Abono a Proveedor en el sistema'],
            ['nombre' => 'Editar Abono a Proveedor', 'slug' => 'supplier-payments.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-transfer', 'module' => 'Compras', 'order' => 32, 'descripcion' => 'Permite Editar Abono a Proveedor en el sistema'],
            ['nombre' => 'Actualizar Abono a Proveedor', 'slug' => 'supplier-payments.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-transfer', 'module' => 'Compras', 'order' => 32, 'descripcion' => 'Permite Actualizar Abono a Proveedor en el sistema'],
            ['nombre' => 'Eliminar Abono a Proveedor', 'slug' => 'supplier-payments.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-transfer', 'module' => 'Compras', 'order' => 32, 'descripcion' => 'Permite Eliminar Abono a Proveedor en el sistema'],

            // Contabilidad / Gastos (Order: 50)
            ['nombre' => 'Gastos', 'slug' => 'expenses.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-money-bill-wave', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Ver Gasto en el sistema'],
            ['nombre' => 'Crear Gasto', 'slug' => 'expenses.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-wave', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Crear Gasto en el sistema'],
            ['nombre' => 'Guardar Gasto', 'slug' => 'expenses.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-wave', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Guardar Gasto en el sistema'],
            ['nombre' => 'Ver Detalle Gasto', 'slug' => 'expenses.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-wave', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Ver Detalle Gasto en el sistema'],
            ['nombre' => 'Editar Gasto', 'slug' => 'expenses.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-wave', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Editar Gasto en el sistema'],
            ['nombre' => 'Actualizar Gasto', 'slug' => 'expenses.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-wave', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Actualizar Gasto en el sistema'],
            ['nombre' => 'Eliminar Gasto', 'slug' => 'expenses.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-money-bill-wave', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Eliminar Gasto en el sistema'],

            ['nombre' => 'Categorías de Gastos', 'slug' => 'expense-categories.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-tags', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Ver Categoría de Gasto en el sistema'],
            ['nombre' => 'Crear Categoría Gasto', 'slug' => 'expense-categories.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Crear Categoría de Gasto en el sistema'],
            ['nombre' => 'Guardar Categoría Gasto', 'slug' => 'expense-categories.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Guardar Categoría de Gasto en el sistema'],
            ['nombre' => 'Ver Detalle Categoría Gasto', 'slug' => 'expense-categories.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Ver Detalle Categoría de Gasto en el sistema'],
            ['nombre' => 'Editar Categoría Gasto', 'slug' => 'expense-categories.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Editar Categoría de Gasto en el sistema'],
            ['nombre' => 'Actualizar Categoría Gasto', 'slug' => 'expense-categories.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Actualizar Categoría de Gasto en el sistema'],
            ['nombre' => 'Eliminar Categoría Gasto', 'slug' => 'expense-categories.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-tags', 'module' => 'Contabilidad', 'order' => 50, 'descripcion' => 'Permite Eliminar Categoría de Gasto en el sistema'],

            // Tablero / Reportes (Order: 2)
            ['nombre' => 'Reportes', 'slug' => 'reports.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Tablero', 'order' => 2, 'descripcion' => 'Permite Ver Reporte en el sistema'],
            ['nombre' => 'Crear Reporte', 'slug' => 'reports.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Tablero', 'order' => 2, 'descripcion' => 'Permite Crear Reporte en el sistema'],
            ['nombre' => 'Guardar Reporte', 'slug' => 'reports.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Tablero', 'order' => 2, 'descripcion' => 'Permite Guardar Reporte en el sistema'],
            ['nombre' => 'Ver Detalle Reporte', 'slug' => 'reports.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Tablero', 'order' => 2, 'descripcion' => 'Permite Ver Detalle Reporte en el sistema'],
            ['nombre' => 'Editar Reporte', 'slug' => 'reports.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Tablero', 'order' => 2, 'descripcion' => 'Permite Editar Reporte en el sistema'],
            ['nombre' => 'Actualizar Reporte', 'slug' => 'reports.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Tablero', 'order' => 2, 'descripcion' => 'Permite Actualizar Reporte en el sistema'],
            ['nombre' => 'Eliminar Reporte', 'slug' => 'reports.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-chart-pie', 'module' => 'Tablero', 'order' => 2, 'descripcion' => 'Permite Eliminar Reporte en el sistema'],

            // Configuración (Order: 100-110)
            // Users (100)
            ['nombre' => 'Usuarios', 'slug' => 'usuarios.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Ver Usuario en el sistema'],
            ['nombre' => 'Crear Usuario', 'slug' => 'usuarios.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Crear Usuario en el sistema'],
            ['nombre' => 'Guardar Usuario', 'slug' => 'usuarios.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Guardar Usuario en el sistema'],
            ['nombre' => 'Ver Detalle Usuario', 'slug' => 'usuarios.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Ver Detalle Usuario en el sistema'],
            ['nombre' => 'Editar Usuario', 'slug' => 'usuarios.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Editar Usuario en el sistema'],
            ['nombre' => 'Actualizar Usuario', 'slug' => 'usuarios.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Actualizar Usuario en el sistema'],
            ['nombre' => 'Eliminar Usuario', 'slug' => 'usuarios.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-users', 'module' => 'Configuración', 'order' => 100, 'descripcion' => 'Permite Eliminar Usuario en el sistema'],

            // Roles (101)
            ['nombre' => 'Roles', 'slug' => 'roles.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Ver Rol en el sistema'],
            ['nombre' => 'Crear Rol', 'slug' => 'roles.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Crear Rol en el sistema'],
            ['nombre' => 'Guardar Rol', 'slug' => 'roles.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Guardar Rol en el sistema'],
            ['nombre' => 'Ver Detalle Rol', 'slug' => 'roles.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Ver Detalle Rol en el sistema'],
            ['nombre' => 'Editar Rol', 'slug' => 'roles.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Editar Rol en el sistema'],
            ['nombre' => 'Actualizar Rol', 'slug' => 'roles.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Actualizar Rol en el sistema'],
            ['nombre' => 'Eliminar Rol', 'slug' => 'roles.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Eliminar Rol en el sistema'],
            ['nombre' => 'Edit_permissions Rol', 'slug' => 'roles.edit_permissions', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Edit_permissions Rol en el sistema'],
            ['nombre' => 'Update_permissions Rol', 'slug' => 'roles.update_permissions', 'is_menu' => 0, 'icon' => 'fa-solid fa-user-shield', 'module' => 'Configuración', 'order' => 101, 'descripcion' => 'Permite Update_permissions Rol en el sistema'],

            // Branches (102)
            ['nombre' => 'Sucursales', 'slug' => 'branches.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-building', 'module' => 'Configuración', 'order' => 102, 'descripcion' => 'Permite Ver Sucursal en el sistema'],
            ['nombre' => 'Crear Sucursal', 'slug' => 'branches.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-building', 'module' => 'Configuración', 'order' => 102, 'descripcion' => 'Permite Crear Sucursal en el sistema'],
            ['nombre' => 'Guardar Sucursal', 'slug' => 'branches.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-building', 'module' => 'Configuración', 'order' => 102, 'descripcion' => 'Permite Guardar Sucursal en el sistema'],
            ['nombre' => 'Ver Detalle Sucursal', 'slug' => 'branches.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-building', 'module' => 'Configuración', 'order' => 102, 'descripcion' => 'Permite Ver Detalle Sucursal en el sistema'],
            ['nombre' => 'Editar Sucursal', 'slug' => 'branches.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-building', 'module' => 'Configuración', 'order' => 102, 'descripcion' => 'Permite Editar Sucursal en el sistema'],
            ['nombre' => 'Actualizar Sucursal', 'slug' => 'branches.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-building', 'module' => 'Configuración', 'order' => 102, 'descripcion' => 'Permite Actualizar Sucursal en el sistema'],
            ['nombre' => 'Eliminar Sucursal', 'slug' => 'branches.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-building', 'module' => 'Configuración', 'order' => 102, 'descripcion' => 'Permite Eliminar Sucursal en el sistema'],

            // Permissions (103)
            ['nombre' => 'Permisos', 'slug' => 'permissions.index', 'is_menu' => 0, 'icon' => 'fa-solid fa-key', 'module' => 'Configuración', 'order' => 103, 'descripcion' => 'Permite ver la lista de permisos'],
            ['nombre' => 'Crear Permisos', 'slug' => 'permissions.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-key', 'module' => 'Configuración', 'order' => 103, 'descripcion' => 'Permite crear nuevos permisos'],
            ['nombre' => 'Editar Permisos', 'slug' => 'permissions.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-key', 'module' => 'Configuración', 'order' => 103, 'descripcion' => 'Permite editar permisos existentes'],
            ['nombre' => 'Eliminar Permisos', 'slug' => 'permissions.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-key', 'module' => 'Configuración', 'order' => 103, 'descripcion' => 'Permite eliminar permisos'],
            ['nombre' => 'Sincronizar Permiso', 'slug' => 'permissions.sync', 'is_menu' => 0, 'icon' => 'fa-solid fa-key', 'module' => 'Configuración', 'order' => 103, 'descripcion' => 'Permite Sincronizar Permiso en el sistema'],

            // Config (104)
            ['nombre' => 'Actualizar configurations', 'slug' => 'configurations.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-circle-dot', 'module' => 'General', 'order' => 50, 'descripcion' => 'Permite Actualizar configurations en el sistema'],
            ['nombre' => 'Low-stock notifications', 'slug' => 'notifications.low-stock', 'is_menu' => 0, 'icon' => 'fa-solid fa-circle-dot', 'module' => 'General', 'order' => 50, 'descripcion' => 'Permite Low-stock notifications en el sistema'],

            // Logs (105)
            ['nombre' => 'Logs de Actividad', 'slug' => 'activity-logs.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-clock-rotate-left', 'module' => 'Configuración', 'order' => 105, 'descripcion' => 'Permite Ver Logs de Actividad'],
            ['nombre' => 'Detalle Log', 'slug' => 'activity-logs.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-clock-rotate-left', 'module' => 'Configuración', 'order' => 105, 'descripcion' => 'Permite Ver Detalle Log de Actividad'],

            // Import (106)
            ['nombre' => 'Importación Masiva', 'slug' => 'import.index', 'is_menu' => 1, 'icon' => 'fa-solid fa-file-import', 'module' => 'Configuración', 'order' => 106, 'descripcion' => 'Permite Ver Importación en el sistema'],
            ['nombre' => 'Template Importación', 'slug' => 'import.template', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Configuración', 'order' => 106, 'descripcion' => 'Permite Template Importación en el sistema'],
            ['nombre' => 'Categories Importación', 'slug' => 'import.categories', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Configuración', 'order' => 106, 'descripcion' => 'Permite Categories Importación en el sistema'],
            ['nombre' => 'Clients Importación', 'slug' => 'import.clients', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Configuración', 'order' => 106, 'descripcion' => 'Permite Clients Importación en el sistema'],
            ['nombre' => 'Suppliers Importación', 'slug' => 'import.suppliers', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Configuración', 'order' => 106, 'descripcion' => 'Permite Suppliers Importación en el sistema'],
            ['nombre' => 'Products Importación', 'slug' => 'import.products', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Configuración', 'order' => 106, 'descripcion' => 'Permite Products Importación en el sistema'],
            ['nombre' => 'Purchases Importación', 'slug' => 'import.purchases', 'is_menu' => 0, 'icon' => 'fa-solid fa-file-import', 'module' => 'Configuración', 'order' => 106, 'descripcion' => 'Permite Purchases Importación en el sistema'],

            // Ecommerce Settings (110)
            ['nombre' => 'Configuración Tienda', 'slug' => 'tenant.ecommerce-settings.edit', 'is_menu' => 1, 'icon' => 'fa-solid fa-store', 'module' => 'Configuración', 'order' => 110, 'descripcion' => 'Permite configurar la tienda en línea'],
            ['nombre' => 'Actualizar Configuración Tienda', 'slug' => 'tenant.ecommerce-settings.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-store', 'module' => 'Configuración', 'order' => 110, 'descripcion' => 'Permite actualizar la configuración de la tienda en línea'],

            // Attendance (50 - Configuración)
            ['nombre' => 'Control de Asistencia', 'slug' => 'attendance.index', 'descripcion' => 'Permite ver el registro de asistencias', 'module' => 'Configuración', 'icon' => 'fa-solid fa-calendar-check', 'is_menu' => true, 'order' => 50],
            ['nombre' => 'Crear Asistencia', 'slug' => 'attendance.create', 'is_menu' => 0, 'icon' => 'fa-solid fa-calendar-check', 'module' => 'Configuración', 'order' => 50, 'descripcion' => 'Permite Crear Asistencia en el sistema'],
            ['nombre' => 'Guardar Asistencia', 'slug' => 'attendance.store', 'is_menu' => 0, 'icon' => 'fa-solid fa-calendar-check', 'module' => 'Configuración', 'order' => 50, 'descripcion' => 'Permite Guardar Asistencia en el sistema'],
            ['nombre' => 'Ver Detalle Asistencia', 'slug' => 'attendance.show', 'is_menu' => 0, 'icon' => 'fa-solid fa-calendar-check', 'module' => 'Configuración', 'order' => 50, 'descripcion' => 'Permite Ver Detalle Asistencia en el sistema'],
            ['nombre' => 'Editar Asistencia', 'slug' => 'attendance.edit', 'is_menu' => 0, 'icon' => 'fa-solid fa-calendar-check', 'module' => 'Configuración', 'order' => 50, 'descripcion' => 'Permite Editar Asistencia en el sistema'],
            ['nombre' => 'Actualizar Asistencia', 'slug' => 'attendance.update', 'is_menu' => 0, 'icon' => 'fa-solid fa-calendar-check', 'module' => 'Configuración', 'order' => 50, 'descripcion' => 'Permite Actualizar Asistencia en el sistema'],
            ['nombre' => 'Eliminar Asistencia', 'slug' => 'attendance.destroy', 'is_menu' => 0, 'icon' => 'fa-solid fa-calendar-check', 'module' => 'Configuración', 'order' => 50, 'descripcion' => 'Permite Eliminar Asistencia en el sistema'],
            ['nombre' => 'Marcar Asistencia', 'slug' => 'attendance.clock-in', 'is_menu' => 0, 'icon' => 'fa-solid fa-calendar-check', 'module' => 'Configuración', 'order' => 50, 'descripcion' => 'Permite Marcar Asistencia en el sistema'],
            ['nombre' => 'Marcar Salida', 'slug' => 'attendance.clock-out', 'is_menu' => 0, 'icon' => 'fa-solid fa-calendar-check', 'module' => 'Configuración', 'order' => 50, 'descripcion' => 'Permite Marcar Salida en el sistema'],
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
                      ->orWhere('slug', 'like', 'abonos.%')
                      ->orWhere('slug', 'like', 'credit-notes.%')
                      ->orWhere('slug', 'like', 'inventory.%')
                      ->orWhere('slug', 'like', 'products.index')
                      ->orWhere('slug', 'like', 'products.show');
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
                'password' => 'admin123456789',
            ]
        );

        $this->command->info("Usuario administrador ({$adminEmail}) configurado correctamente.");
        $this->command->info('Sembrado completado con éxito.');
    }
}
