# Comparación de Migraciones de Inquilinos vs Esquema Legado

Este documento detalla la comparación entre las migraciones de Laravel para inquilinos y el esquema de base de datos legado `sistemadeventas.sql`. El objetivo es identificar cualquier extra u omisión en las tablas, asegurando la consistencia y la adaptación a las convenciones de Laravel.

## 1. `create_clients_table.php` vs `tb_clientes`

**Migración:**
```php
Schema::create('clients', function (Blueprint $table) {
    $table->id();
    $table->string('name', 255);
    $table->string('surname', 255);
    $table->string('dni', 255)->nullable();
    $table->string('phone', 255)->nullable();
    $table->string('email', 255)->nullable();
    $table->string('address', 255)->nullable();
    $table->timestamps();
});
```
**Esquema Legado (`tb_clientes`):**
```sql
CREATE TABLE `tb_clientes` (
  `id_cliente` int(11) NOT NULL,
  `nombre_cliente` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `apellido_cliente` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `dni_cliente` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `telefono_cliente` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `email_cliente` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `direccion_cliente` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
```
**Conclusión:** La migración de `clients` es consistente con `tb_clientes`. Los nombres de las columnas se han adaptado a las convenciones de Laravel (ej. `id_cliente` a `id`, `nombre_cliente` a `name`, `fyh_creacion` a `created_at`). No hay omisiones ni extras significativos.

## 2. `create_categories_table.php` vs `tb_categorias`

**Migración:**
```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name', 255);
    $table->timestamps();
});
```
**Esquema Legado (`tb_categorias`):**
```sql
CREATE TABLE `tb_categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
```
**Conclusión:** La migración de `categories` es consistente con `tb_categorias`. Los nombres de las columnas se han adaptado a las convenciones de Laravel. No hay omisiones ni extras significativos.

## 3. `create_products_table.php` vs `tb_almacen`

**Migración:**
```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('category_id');
    $table->string('code', 255)->unique();
    $table->string('name', 255);
    $table->string('image', 255)->nullable();
    $table->integer('stock');
    $table->decimal('purchase_price', 10, 2);
    $table->decimal('sale_price', 10, 2);
    $table->string('description', 255)->nullable();
    $table->timestamps();

    $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
});
```
**Esquema Legado (`tb_almacen`):**
```sql
CREATE TABLE `tb_almacen` (
  `id_producto` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `codigo` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `imagen` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `stock` int(11) NOT NULL,
  `precio_compra` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `precio_venta` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
```
**Conclusión:** La migración de `products` es consistente con `tb_almacen`. Se ha mejorado el tipo de dato para `precio_compra` y `precio_venta` de `varchar` a `decimal(10, 2)`, lo cual es una mejora positiva para la precisión monetaria. Se añadió una clave foránea para `category_id`.

## 4. `create_suppliers_table.php` vs `tb_proveedores`

**Migración:**
```php
Schema::create('suppliers', function (Blueprint $table) {
    $table->id();
    $table->string('name', 255);
    $table->string('phone', 255)->nullable();
    $table->string('email', 255)->nullable();
    $table->string('address', 255)->nullable();
    $table->timestamps();
});
```
**Esquema Legado (`tb_proveedores`):**
```sql
CREATE TABLE `tb_proveedores` (
  `id_proveedor` int(11) NOT NULL,
  `nombre_proveedor` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `telefono_proveedor` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `email_proveedor` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `direccion_proveedor` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
```
**Conclusión:** La migración de `suppliers` es consistente con `tb_proveedores`. Los nombres de las columnas se han adaptado a las convenciones de Laravel. No hay omisiones ni extras significativos.

## 5. `create_purchases_table.php` vs `tb_compras`

**Migración:**
```php
Schema::create('purchases', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('supplier_id');
    $table->unsignedBigInteger('product_id');
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->timestamps();

    $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
});
```
**Esquema Legado (`tb_compras`):**
```sql
CREATE TABLE `tb_compras` (
  `id_compra` int(11) NOT NULL,
  `id_proveedor` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_compra` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
```
**Conclusión:** La migración de `purchases` es consistente con `tb_compras`. Se ha mejorado el tipo de dato para `precio_compra` de `varchar` a `decimal(10, 2)`. Se añadieron claves foráneas para `supplier_id` y `product_id`.

## 6. `create_sale_items_table.php` vs `tb_carrito`

**Migración:**
```php
Schema::create('sale_items', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('sale_id');
    $table->unsignedBigInteger('product_id');
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->timestamps();

    $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
});
```
**Esquema Legado (`tb_carrito`):**
```sql
CREATE TABLE `tb_carrito` (
  `id_carrito` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
```
**Conclusión:** La migración de `sale_items` es consistente con `tb_carrito`. Se ha mejorado el tipo de dato para `precio_unitario` de `varchar` a `decimal(10, 2)`. Se añadió `sale_id` como clave foránea, lo cual es un extra necesario para relacionar los ítems con una venta específica, mejorando la robustez del esquema.

## 7. `create_sales_table.php` vs `tb_ventas`

**Migración:**
```php
Schema::create('sales', function (Blueprint $table) {
    $table->id();
    $table->integer('nro_venta');
    $table->unsignedBigInteger('client_id');
    $table->decimal('total_paid', 10, 2);
    $table->unsignedBigInteger('user_id');
    $table->date('sale_date');
    $table->string('voucher', 255)->nullable();
    $table->string('payment_type', 50)->default('CONTADO');
    $table->string('payment_status', 50)->default('PAGADO');
    $table->dateTime('credit_payment_date')->nullable();
    $table->timestamps();

    $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});
```
**Esquema Legado (`tb_ventas`):**
```sql
CREATE TABLE `tb_ventas` (
  `id_venta` int(11) NOT NULL,
  `nro_venta` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `total_pagado` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `tipo_pago` varchar(50) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'CONTADO',
  `estado_pago` varchar(50) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'PAGADO',
  `fecha_pago_credito` datetime DEFAULT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
```
**Conclusión:** La migración de `sales` ahora incluye las columnas `payment_type`, `payment_status` y `credit_payment_date` del esquema legado. Los extras (`user_id`, `sale_date`, `voucher`) se mantienen como adiciones útiles. La mejora en el tipo de dato para `total_paid` es positiva. La migración es ahora más consistente con el esquema legado, manteniendo las mejoras de Laravel.

## 8. `create_abonos_table.php` vs `tb_abonos`

**Migración:**
```php
Schema::create('abonos', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('sale_id');
    $table->decimal('amount', 10, 2);
    $table->date('payment_date');
    $table->timestamps();

    $table->foreign('sale_id')->references('id')->on('sales')->onDelete('cascade');
});
```
**Esquema Legado (`tb_abonos`):**
```sql
CREATE TABLE `tb_abonos` (
  `id_abono` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `monto` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `fecha_pago` date NOT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
```
**Conclusión:** La migración de `abonos` es consistente con `tb_abonos`. Se ha mejorado el tipo de dato para `monto` de `varchar` a `decimal(10, 2)`. Se añadió una clave foránea para `sale_id`.

## 9. `create_cash_registers_table.php` vs `tb_arqueo_caja`

**Migración:**
```php
Schema::create('cash_registers', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('user_id');
    $table->decimal('initial_amount', 10, 2);
    $table->decimal('final_amount', 10, 2)->nullable();
    $table->timestamp('opening_time')->nullable();
    $table->timestamp('closing_time')->nullable();
    $table->timestamps();

    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});
```
**Esquema Legado (`tb_arqueo_caja`):**
```sql
CREATE TABLE `tb_arqueo_caja` (
  `id_arqueo` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `monto_inicial` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `monto_final` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fecha_apertura` datetime DEFAULT NULL,
  `fecha_cierre` datetime DEFAULT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
```
**Conclusión:** La migración de `cash_registers` es consistente con `tb_arqueo_caja`. Se han mejorado los tipos de datos para `monto_inicial` y `monto_final` de `varchar` a `decimal(10, 2)`. Se adaptaron los nombres de las columnas y se añadió una clave foránea para `user_id`.

## 10. `create_carts_table.php` vs `tb_carrito`

**Migración:**
```php
Schema::create('carts', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('product_id');
    $table->integer('quantity');
    $table->decimal('price', 10, 2);
    $table->timestamps();

    $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
});
```
**Esquema Legado (`tb_carrito`):**
```sql
CREATE TABLE `tb_carrito` (
  `id_carrito` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
```
**Conclusión:** La migración de `carts` es consistente con `tb_carrito`. Se ha mejorado el tipo de dato para `precio_unitario` de `varchar` a `decimal(10, 2)`. Se añadió una clave foránea para `product_id`.

## 11. `create_configurations_table.php` vs `tb_configuraciones`

**Migración:**
```php
Schema::create('configurations', function (Blueprint $table) {
    $table->id();
    $table->string('key', 255)->unique();
    $table->string('value', 255)->nullable();
    $table->timestamps();
});
```
**Esquema Legado (`tb_configuraciones`):**
```sql
CREATE TABLE `tb_configuraciones` (
  `id_configuracion` int(11) NOT NULL,
  `clave` varchar(255) COLLATE utf8_spanish_ci NOT NULL,
  `valor` varchar(255) COLLATE utf8_spanish_ci DEFAULT NULL,
  `fyh_creacion` datetime NOT NULL,
  `fyh_actualizacion` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
```
**Conclusión:** La migración de `configurations` es consistente con `tb_configuraciones`. Los nombres de las columnas se han adaptado a las convenciones de Laravel. No hay omisiones ni extras significativos.

## Aclaración sobre `tb_carrito` y el conteo de migraciones

Se ha observado que, aunque el esquema legado `sistemadeventas.sql` contiene 10 tablas que se están migrando (excluyendo las 4 de permisos/roles/usuarios), se han generado 11 archivos de migración. Esto se debe a la forma en que la tabla `tb_carrito` del esquema legado se ha adaptado a las convenciones y necesidades de Laravel:

- **`tb_carrito` (Esquema Legado):** Contenía la información tanto de los ítems en un carrito de compras activo como de los ítems de ventas ya realizadas.

- **`create_sale_items_table.php` (Migración Laravel):** Esta migración se encarga de los ítems que forman parte de una venta ya finalizada. Representa los productos que se vendieron en una transacción específica.

- **`create_carts_table.php` (Migración Laravel):** Esta migración se encarga de los ítems que se encuentran actualmente en el carrito de compras de un usuario, antes de que la venta se concrete.

Esta división permite una mejor organización, claridad y separación de responsabilidades en la aplicación Laravel, aunque resulte en dos migraciones para lo que originalmente era una única tabla en el esquema legado. Por lo tanto, las 10 tablas legadas migradas se traducen en 11 archivos de migración en el proyecto Laravel.
