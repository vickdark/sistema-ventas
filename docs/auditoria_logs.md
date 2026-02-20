# Módulo de Auditoría y Logs (Activity Logs)

## 1. Descripción General
Es el núcleo de trazabilidad del sistema. Registra automáticamente las acciones críticas realizadas por los usuarios, permitiendo saber "quién hizo qué, en qué modelo y cuándo". Es fundamental para la seguridad y la resolución de problemas (troubleshooting).

## 2. Características Principales
- **Registro Automático**: Captura eventos de creación, actualización y eliminación de registros en los modelos principales.
- **Trazabilidad de Usuarios**: Asocia cada acción a un usuario específico o al sistema (en procesos automáticos).
- **Categorización Visual**: Utiliza badges de colores para identificar rápidamente el tipo de acción (Verde: Creación, Azul: Edición, Rojo: Eliminación).
- **Detalle de Cambios**: Capacidad de mostrar la descripción de la acción realizada.

## 3. Arquitectura Técnica

### Backend
- **Controller**: `App\Http\Controllers\Tenant\ActivityLogController`
    - `index()`: Provee los datos filtrables para la tabla de auditoría.
    - `formatAction()`: Formatea los tipos de acción en badges HTML para una mejor lectura en el frontend.
- **Model**: `App\Models\Tenant\ActivityLog`
    - Atributos: `user_id`, `action`, `model_type`, `model_id`, `description`, `ip_address`, `user_agent`.
- **Implementación (Traits)**: El sistema utiliza un Trait o Eventos de Eloquent para disparar el registro de logs cada vez que un modelo cambia.

### Frontend
- **Vista**: `resources/views/tenant/activity_logs/index.blade.php`
- **Lógica JS**: `resources/js/pages/tenants/activity_logs/index.js`
    - Integrado con `CrudManager` para un filtrado rápido por acción o usuario.
    - Renderizado eficiente mediante `DataGrid`.

## 4. Tipos de Eventos Registrados
- **CREATED**: Cuando se registra un nuevo usuario, producto, venta, etc.
- **UPDATED**: Cambios en configuraciones o datos existentes.
- **DELETED**: Eliminación de registros (soporte para auditoría forense).
- **LOGIN/LOGOUT**: Seguimiento de sesiones de usuario.

---
*Documento Técnico del Sistema de Ventas - Febrero 2026*
