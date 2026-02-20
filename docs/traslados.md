# Módulo de Traslados entre Sucursales (Stock Transfers)

## 1. Descripción General
Este módulo gestiona el movimiento físico de mercancía entre diferentes sucursales/bodegas de la empresa. Asegura la trazabilidad del inventario desde que sale de la sucursal de origen hasta que es recibido y confirmado en la sucursal de destino.

## 2. Características Principales
- **Interfaz Consistente**: Utiliza el mismo layout de pantalla completa y grid de productos que el POS de Ventas.
- **Validación de Stock**: Impide el envío de productos si la sucursal de origen no cuenta con existencias suficientes.
- **Flujo de Dos Pasos**: 
    1. **Envío**: El stock se descuenta del origen y el traslado queda en estado `ENVIADO`.
    2. **Recepción**: El encargado del destino debe confirmar la llegada para que el stock se sume a su inventario (estado `RECIBIDO`).
- **Sincronización de Productos**: Si un producto no existe en la sucursal de destino, el sistema lo crea automáticamente replicando los datos base (código, nombre, precio) pero con stock inicial cero.

## 3. Arquitectura Técnica

### Backend
- **Controller**: `App\Http\Controllers\Tenant\StockTransferController`
    - `store()`: Descuenta stock de origen y crea el registro de traslado centralizando los items.
    - `receive()`: Suma el stock en destino. Utiliza `withoutGlobalScope('branch')` para poder interactuar con productos de otras sucursales.
- **Models**:
    - `StockTransfer`: Registra origen, destino, usuario, estado y fechas.
    - `StockTransferItem`: Detalle de productos y cantidades en tránsito.

### Frontend
- **Vista**: `resources/views/tenant/stock_transfers/create.blade.php`
- **Lógica JS**: `resources/js/pages/tenants/stock_transfers/create.js`
    - Implementa **Swiper.js** para la selección táctil de productos.
    - Maneja filtros por categoría.
    - Controla que el usuario no agregue más cantidad de la disponible físicamente en la sucursal activa.

## 4. Proceso de Traslado
1. **Configuración**: El usuario selecciona la sucursal destino.
2. **Selección**: Agrega productos al panel de envío (panel derecho).
3. **Despacho**: Al confirmar, el stock desaparece del inventario actual.
4. **Recepción**: El usuario debe cambiar de sucursal (sesión) a la de destino, entrar al listado de traslados y pulsar "Recibir". Solo entonces el stock se incrementa en la nueva ubicación.

---
*Documento Técnico del Sistema de Ventas - Febrero 2026*
