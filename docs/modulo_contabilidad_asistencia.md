# Documentación de Nuevos Módulos: Contabilidad y Asistencia

Este documento detalla la implementación reciente de los módulos de Contabilidad y Control de Asistencia en el sistema de ventas. Estos cambios incluyen nuevas estructuras de base de datos, lógica de negocio y actualizaciones en la gestión de permisos y menús.

## 1. Módulo de Contabilidad

El módulo de contabilidad ha sido diseñado para ser robusto, flexible y completamente integrado con las operaciones del sistema (Ventas, Compras, Gastos).

### 1.1 Estructura de Base de Datos

El núcleo contable se basa en tres tablas principales:

#### a) Catálogo de Cuentas (`accounts`)
Esta tabla almacena el Plan de Cuentas de la empresa.
- **Jerarquía**: Utiliza `parent_id` para crear una estructura de árbol (Ej: Activo -> Activo Corriente -> Caja).
- **Tipos**: Clasificación en `asset` (activo), `liability` (pasivo), `equity` (patrimonio), `revenue` (ingresos), `expense` (gastos).
- **Movimiento**: El campo `is_movement` indica si la cuenta puede recibir asientos directos (true) o si es solo una cuenta agrupadora (false).
- **Código**: Campo `code` único para la nomenclatura contable (ej: 1.1.01).

#### b) Cabecera de Asientos (`journal_entries`)
Representa la transacción contable o "partida".
- **Polimorfismo**: Utiliza `reference_type` y `reference_id` para vincular automáticamente un asiento con su origen (Venta, Compra, Gasto). Esto permite trazabilidad total: desde el asiento contable se puede llegar a la factura de venta original.
- **Datos de Auditoría**: Registra `user_id` (quién creó el asiento) y `branch_id` (sucursal).
- **Estado**: Control de estados `draft` (borrador), `posted` (publicado/mayorizado), `void` (anulado).

#### c) Detalle de Asientos (`journal_entry_details`)
Contiene los movimientos individuales de cada asiento (el "Debe" y el "Haber").
- **Doble Partida**: Cada registro guarda `debit` (débito) o `credit` (crédito).
- **Integridad**: Relaciona directamente con la tabla `accounts`.

### 1.2 Flujo de Trabajo y Lógica

El sistema permite dos tipos de operaciones contables:

1.  **Asientos Manuales**: A través de la interfaz de "Libro Diario", los contadores pueden crear asientos de ajuste, apertura o cierre manualmente.
2.  **Asientos Automáticos (Integración)**: Aunque la estructura está lista, el diseño permite que al crear una Venta o Compra, se genere automáticamente el `JournalEntry` correspondiente usando las referencias polimórficas.

### 1.3 Ubicación en el Sistema
El módulo se encuentra bajo la sección **Contabilidad** en el menú principal, separado de Finanzas para mayor claridad profesional.
- **Rutas**: `/accounting` (Plan de cuentas), `/journal-entries` (Libro diario).

---

## 2. Módulo de Asistencia (RRHH)

Este módulo gestiona el control de tiempo de los empleados de manera sencilla y directa.

### 2.1 Funcionalidad Principal
El sistema registra el flujo de entrada y salida del personal:
- **Entrada (Clock In)**: Registra la hora exacta de inicio de jornada.
- **Salida (Clock Out)**: Cierra la jornada laboral.

### 2.2 Estructura de Datos (`attendances`)
- **Registro Diario**: Se agrupa por `date` y `user_id`.
- **Ubicación**: Se guarda la `branch_id` (sucursal) y la `ip_address` para validar desde dónde se realizó el marcaje.
- **Estados**: Calcula automáticamente si el empleado está `present`, `late` (tarde), o si hubo `early_leave` (salida anticipada).

### 2.3 Ubicación
Se ha ubicado dentro del grupo **Configuración** en el menú principal, facilitando el acceso administrativo para revisión de logs.

---

## 3. Actualización de Menús y Permisos

Para soportar estos nuevos módulos y mejorar la organización, se refactorizó la estructura del menú principal (`SyncPermissions.php` y `DatabaseSeeder.php`).

### 3.1 Nueva Organización del Menú
Se han redefinido los grupos para una navegación más lógica:

1.  **Tablero**: Dashboard y Reportes.
2.  **Ventas**: Ventas, POS, Cotizaciones, Clientes, Abonos, Cajas.
3.  **Contabilidad**: Contabilidad General, Libro Diario, Gastos.
4.  **Inventario**: Productos, Categorías, Traslados.
5.  **Compras**: Proveedores, Compras.
6.  **Configuración**: Usuarios, Roles, Asistencia, Logs, Importaciones.

### 3.2 Sincronización
Se utiliza el comando `php artisan permissions:sync` para actualizar automáticamente la tabla de permisos basándose en esta nueva estructura definida en el código, asegurando que los menús en producción siempre coincidan con la definición del desarrollo.
