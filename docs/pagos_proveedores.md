# Módulo de Pagos a Proveedores (Supplier Payments)

## 1. Descripción General
Permite gestionar las cuentas por pagar derivadas de las compras a crédito realizadas a proveedores. Centraliza el control de deudas, abonos y estados de pago para mantener una relación financiera sana con los abastecedores.

## 2. Características Principales
- **Control de Saldos Pendientes**: Listado dinámico de compras que aún no han sido liquidadas, ordenadas por fecha de vencimiento.
- **Registro de Abonos**: Permite realizar pagos parciales o totales a una factura/compra específica.
- **Conciliación Automática**: El sistema actualiza el estado de la compra (`PAGADO`, `PARCIAL`, `PENDIENTE`) y el saldo restante de forma automática tras cada abono.
- **Historial de Pagos**: Cada compra mantiene un registro detallado de quién hizo el pago, cuándo y mediante qué método (Efectivo, Transferencia, etc.).

## 3. Arquitectura Técnica

### Backend
- **Controller**: `App\Http\Controllers\Tenant\SupplierPaymentController`
    - `index()`: Filtra compras cuyo `payment_status` no sea `PAGADO`.
    - `store()`: Utiliza `lockForUpdate()` para evitar colisiones de datos al actualizar el saldo de la compra durante procesos concurrentes.
- **Models**:
    - `SupplierPayment`: Almacena el monto del abono, método de pago y referencia.
    - `Purchase`: El modelo principal que se actualiza (campos `pending_amount` y `payment_status`).

### Frontend
- **Vista**: `resources/views/tenant/supplier_payments/index.blade.php`
- **Lógica JS**: `resources/js/pages/tenants/supplier_payments/index.js`
    - Utiliza `CrudManager` y `DataGrid` para el listado de deudas.
    - Implementa un modal dinámico para registrar el abono sin recargar la página.
    - Feedback instantáneo mediante notificaciones (`Notify.success`).

## 4. Proceso de Pago
1. **Identificación**: El usuario busca la compra o el proveedor en el listado de deudas.
2. **Registro**: Se abre el formulario de abono, se ingresa el monto y el método de pago.
3. **Validación**: El sistema verifica que el monto no exceda la deuda actual.
4. **Actualización**: Al confirmar, el saldo se descuenta y, si llega a cero, la compra se marca como completada.

---
*Documento Técnico del Sistema de Ventas - Febrero 2026*
