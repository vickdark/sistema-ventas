# Sistema de Cierre Automático de Cajas

Este documento detalla el funcionamiento del sistema de cierre automático implementado para las cajas registradoras del sistema multitenant.

## Objetivo
Permitir que el sistema cierre automáticamente las cajas abiertas cuando alcanzan una hora programada de cierre, calculando los totales de ventas y abonos del turno sin intervención manual.

## Componentes Técnicos

### 1. Comando de Consola: `app:auto-close-cash-registers`
Ubicación: `app/Console/Commands/AutoCloseCashRegisters.php`

**Funcionamiento:**
- Itera sobre todos los clientes (tenants) registrados.
- Inicializa el contexto de cada tenant para acceder a su base de datos específica.
- Busca cajas con estado `abierta` cuya `scheduled_closing_time` sea menor o igual a la hora actual del servidor.
- **Cálculo de Totales:**
    - Suma todas las ventas de tipo `CONTADO` y `TRANSFERENCIA` desde la apertura.
    - Suma todos los abonos (pagos de créditos) realizados durante el turno.
- **Ejecución del Cierre:**
    - Actualiza el estado a `cerrada`.
    - Establece el `final_amount` como el monto esperado (Monto Inicial + Ventas + Abonos).
    - Añade la marca `[CIERRE AUTOMÁTICO]` en las observaciones con la fecha y hora exacta.

### 2. Programación (Scheduling)
Ubicación: `routes/console.php`

El comando se ha registrado en el programador de tareas de Laravel:
```php
Schedule::command('app:auto-close-cash-registers')->everyMinute();
```
Esto asegura que el sistema verifique las horas de cierre cada minuto.

## Configuración Requerida en el Servidor

Para que el sistema de cierre automático funcione, el servidor debe ejecutar el "scheduler" de Laravel de forma continua.

### En Windows (Local/Herd/Laragon):
Se debe crear una tarea programada o ejecutar en una terminal abierta:
```powershell
php artisan schedule:work
```

### En Producción (Linux/Ubuntu):
Se debe añadir la siguiente línea al Crontab del servidor (`crontab -e`):
```bash
* * * * * cd /ruta-a-tu-proyecto && php artisan schedule:run >> /dev/null 2>&1
```

## Flujo del Usuario
1. El administrador configura la hora de cierre predeterminada en **Configuración**.
2. Al abrir una caja, esta hereda la hora programada (o se define una manualmente).
3. Si la caja no se cierra manualmente antes de la hora, el sistema la cerrará automáticamente en la siguiente revisión del scheduler.
4. El cierre automático asume que el dinero en caja coincide exactamente con lo registrado en el sistema (ventas directas + abonos).

---
*Documentación generada el 10 de Febrero de 2026.*
