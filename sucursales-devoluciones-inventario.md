# Documentación Técnica: Sucursales, Devoluciones y Gestión de Inventario

Este documento detalla la implementación reciente de los módulos críticos para el control de existencias y gestión operativa del sistema.

---

## 1. Gestión de Inventario y Kardex

El corazón del sistema de productos ha sido refactorizado para pasar de un simple contador de stock a un **sistema transaccional auditoriable (Kardex)**.

### ¿Cómo funciona?

En lugar de simplemente sumar o restar el número en la columna `stock`, el sistema ahora registra cada cambio en una tabla histórica llamada `stock_movements`.

#### Características Principales:
*   **Centralización**: Se implementaron métodos auxiliares (`addStock`, `removeStock`) en el modelo `Product`. Cualquier parte del sistema (Ventas, Compras, Ajustes) debe usar estos métodos para garantizar que el historial se guarde.
*   **Trazabilidad**: Cada movimiento guarda:
    *   Tipo: `input` (Entrada) o `output` (Salida).
    *   Cantidad anterior y nueva.
    *   Usuario responsable.
    *   Motivo y Descripción.
    *   Referencia polimórfica (ID de la Venta, Compra o Nota de Crédito asociada).
*   **Ajustes Manuales**: Se creó una interfaz para que los administradores realicen correcciones rápidas (por ejemplo: "Producto Dañado", "Conteo Incorrecto"), las cuales quedan registradas oficialmente.

#### Componentes Técnicos y Código:

##### Modelo `StockMovement` (app/Models/Tenant/StockMovement.php)
Es el registro central de todos los cambios de inventario. Utiliza relaciones polimórficas (`morphTo`) para vincularse dinámicamente con `Sale`, `Purchase` o `CreditNote`.

```php
public function reference()
{
    return $this->morphTo(); // Permite vincularse a cualquier modelo (Venta, Compra, NC)
}
```

##### Métodos en `Product` (app/Models/Tenant/Product.php)
Para evitar código repetitivo y errores humanos, la lógica de actualización de stock se encapsuló en el propio modelo del producto.

*   `addStock($quantity, $reason, $description, $reference)`: Incrementa el stock y crea un registro de entrada.
*   `removeStock(...)`: Decrementa el stock y crea un registro de salida.

Ejemplo de uso en un controlador:
```php
$product->removeStock(5, 'Venta', 'Venta #123', $saleModel);
```

##### Controlador `InventoryController` (app/Http/Controllers/Tenant/InventoryController.php)
Maneja tres acciones principales:
1.  `index`: Carga la lista de productos con semáforos de stock (usando Grid.js).
2.  `kardex`: Muestra el historial completo de movimientos de un producto específico.
3.  `adjust`: Endpoint para realizar ajustes manuales (entradas/salidas arbitrarias) validados mediante una transacción de base de datos (`DB::transaction`).

---

## 2. Notas de Crédito (Devoluciones)

Se implementó un módulo completo para gestionar devoluciones de clientes de manera formal y contable.

### Flujo de Trabajo:
1.  **Origen**: Una nota de crédito siempre nace de una **Venta** existente.
2.  **Creación**: 
    *   El usuario selecciona "Procesar Devolución" desde una venta.
    *   El sistema carga los items vendidos.
    *   El usuario selecciona qué items y qué cantidad devuelve.
    *   Se especifica un motivo (con soporte para campo condicional "Otro").
3.  **Impacto en Inventario**: 
    *   Al guardar la NC, el sistema **automáticamente reingresa los productos al stock** (Movimiento: "Entrada por Devolución") utilizando el método `addStock`.
4.  **Anulación**:
    *   Si una Nota de Crédito se creó por error, se puede "Anular".
    *   Al anularla, el sistema **revierte el stock** (lo vuelve a sacar), asumiendo que la devolución no fue válida.

### Código Relevante:

*   **`CreditNoteController@store`**:
    *   Valida que la cantidad devuelta no exceda lo vendido originalmente.
    *   Crea la cabecera `CreditNote` y los detalles `CreditNoteItem`.
    *   Invoca `$product->addStock(...)` para reingresar la mercancía devuelta.
*   **Vista `create.blade.php`**:
    *   Utiliza **Alpine.js** (`x-data`, `x-show`) para mostrar/ocultar dinámicamente el campo de "Otro motivo" sin recargar la página.

---

## 3. Dashboard Administrativo

El Dashboard principal (`resources/views/tenant/dashboards/admin.blade.php`) se actualizó para ser un centro de control de inventario.

### Implementación en Código:
*   **Consultas Directas Optimizadas**: En lugar de cargar todos los modelos, se usan agregados de SQL para rendimiento.
    *   *Bajo Stock*: `Product::whereColumn('stock', '<=', 'min_stock')->count()`
    *   *Valoración*: `Product::sum(DB::raw('stock * purchase_price'))`
*   **Diseño Visual**: Se utilizaron tarjetas de Bootstrap con bordes de color semántico (Rojo para alertas, Azul para información financiera).

---

## 4. Importación Masiva (Jobs)

El sistema de importación (`app/Jobs/Tenant/ImportPurchasesJob.php`) fue actualizado para integrarse con el nuevo motor de inventario.

*   **Procesamiento en Segundo Plano**: Se utilizan **Queues (Colas)** de Laravel para procesar archivos grandes sin bloquear al usuario.
*   **Integración**: Dentro del Job, al crear cada compra, se llama a `$product->addStock(...)`. Esto asegura que incluso las cargas masivas generen historial de Kardex verificable.

---

## 5. Gestión de Gastos Operativos

Se implementó un módulo para el control de egresos, permitiendo a los administradores registrar cualquier salida de dinero que no sea una compra de productos (gastos administrativos, servicios, etc.).

### Estructura y Funcionamiento:

*   **Categorización**: Los gastos se agrupan en categorías (`ExpenseCategory`) con colores distintivos para facilitar su identificación visual en reportes.
*   **Asociación de Sucursal**: Para garantizar la precisión en los cortes de caja y balances por sede, cada gasto guarda obligatoriamente el `branch_id` de la sucursal desde donde se registró.
*   **Seguridad**: Cada registro guarda el `user_id` del responsable, permitiendo auditorías de egresos.

### Componentes Técnicos:

#### Modelo `Expense` (app/Models/Tenant/Expense.php)
Define las relaciones clave para la trazabilidad financiera:
```php
public function category() { return $this->belongsTo(ExpenseCategory::class); }
public function branch() { return $this->belongsTo(Branch::class); }
public function user() { return $this->belongsTo(Usuario::class); }
```

#### Controlador `ExpenseController` (app/Http/Controllers/Tenant/ExpenseController.php)
Gestiona el flujo de datos:
*   **Filtros Avanzados**: Permite buscar gastos por concepto, referencia o nombre de categoría.
*   **Automatización**: En el método `store`, el sistema asigna automáticamente el `branch_id` del usuario autenticado, evitando errores de digitación por parte del operador.

#### Frontend (resources/js/pages/tenants/expenses/)
*   **`CrudManager`**: Se utiliza una clase base `CrudManager` para estandarizar las operaciones de lectura y borrado, manteniendo una interfaz consistente con otros módulos.
*   **Grid.js**: Proporciona tablas rápidas con búsqueda del lado del servidor.

---

## Resumen de Tecnologías Usadas

1.  **Laravel Eloquent & Relationships**: Para vincular Productos, Ventas, Movimientos, Sucursales y Gastos.
2.  **Polimorfismo (MorphTo)**: Para que una sola tabla de movimientos sirva para todo tipo de operaciones.
3.  **DB Transactions**: Para asegurar integridad de datos (si falla el inventario, no se crea la venta).
4.  **Alpine.js**: Para interactividad ligera en el frontend (modales, campos condicionales).
5.  **Grid.js & CrudManager**: Para tablas de datos rápidas, asíncronas y gestión de CRUDs estandarizada.
6.  **Laravel Jobs**: Para procesamiento asíncrono de importaciones masivas.
7.  **Middleware Personalizado (`SetActiveBranch`)**: Para garantizar que todas las transacciones (incluyendo gastos) queden registradas en la sede correcta.
