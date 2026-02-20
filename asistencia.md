# Módulo de Control de Asistencia

## 1. Descripción General
Permite a los usuarios y empleados registrar su jornada laboral (entrada y salida) directamente desde el sistema. Proporciona a la administración un control detallado sobre la puntualidad y las horas trabajadas.

## 2. Características Principales
- **Marcaje Rápido**: Widget integrado en el Dashboard principal para marcar entrada/salida con un solo clic.
- **Historial Personal**: Cada usuario puede consultar sus propios registros históricos.
- **Notas de Jornada**: Posibilidad de añadir comentarios al marcar la salida (ej: "Salida al médico", "Horas extra").
- **Estado en Tiempo Real**: Visualización inmediata de si el usuario está actualmente "En Turno" o "Fuera de Turno".
- **Seguridad**: Registro automático de la IP desde donde se realiza la marca.

## 3. Arquitectura Técnica

### Base de Datos (`attendances`)
- Registra `clock_in` y `clock_out`.
- Relaciona el registro con `user_id` y `branch_id`.
- Campo `status` para futuras implementaciones de reglas de negocio (Tardanzas, Ausencias).

### Backend
- **Controller**: `App\Http\Controllers\Tenant\AttendanceController`
    - `status()`: Endpoint ligero para verificar el estado actual del usuario.
    - `store()`: Registra la entrada, validando que no exista un turno abierto.
    - `update()`: Cierra el turno actual buscando el registro activo.

### Frontend
- **Widget de Dashboard**: Componente reactivo en JS (`admin.js`) que cambia su interfaz según el estado del usuario.
- **Vista Principal**: `resources/views/tenant/attendance/index.blade.php` con tabla de historial paginada y filtros por fecha.
- **Integración**: Enlace automático en el menú lateral bajo el módulo "RRHH".

## 4. Flujo de Uso
1. **Inicio de Jornada**: El usuario ingresa al Dashboard y ve el widget "Control Asistencia" con el botón "MARCAR ENTRADA".
2. **Confirmación**: Al hacer clic, confirma la acción y el sistema registra la hora actual.
3. **Durante el Turno**: El widget muestra el tiempo transcurrido o la hora de entrada y el botón cambia a "MARCAR SALIDA".
4. **Fin de Jornada**: El usuario marca su salida, pudiendo añadir una nota opcional.
5. **Consulta**: En la sección "Control Asistencia" del menú, puede ver sus registros pasados.

---
*Documento Técnico del Sistema de Ventas - Febrero 2026*
