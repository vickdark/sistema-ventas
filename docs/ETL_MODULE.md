# Módulo ETL - Importación Masiva

## Descripción
Sistema completo de importación masiva (ETL - Extract, Transform, Load) para los 4 módulos principales del sistema de ventas.

## Características Implementadas

### 1. Interfaz de Usuario (`resources/views/tenant/import/index.blade.php`)
- **4 Cards Interactivas**: Una por cada módulo (Categorías, Clientes, Proveedores, Productos)
- **Diseño Moderno**: Con iconos, colores distintivos y efectos hover
- **Modal de Importación**: Formulario unificado para subir archivos
- **Barra de Progreso**: Feedback visual durante la importación
- **Instrucciones Claras**: Guía paso a paso para el usuario
- **Optimización de UI**: 
    - Fix de dropdowns cortados por contenedores con `overflow: hidden`.
    - Uso de `data-bs-boundary="viewport"` para asegurar visibilidad de menús.
    - Manejo dinámico de `z-index` mediante JavaScript al abrir/cerrar menús.

### 2. Controlador y Procesamiento (`app/Http/Controllers/Tenant/ImportController.php`)
- **Generación de Plantillas**: Descarga automática de CSV/Excel con headers y ejemplo.
- **Parsing de Archivos**: Soporte para CSV y Excel.
- **Validación Robusta**: Validación campo por campo con mensajes de error.
- **Detección de Duplicados**: Omite automáticamente registros existentes.
- **Importación de Imágenes (Productos)**:
    - Soporte para URLs de imágenes externas.
    - Descarga automática y almacenamiento local en `storage/app/public/products`.
    - Timeout de 5 segundos por imagen para evitar bloqueos.
- **Procesamiento Híbrido (Síncrono/Asíncrono)**:
    - **Síncrono**: Archivos < 50 filas se procesan en tiempo real (timeout extendido a 300s).
    - **Asíncrono (Jobs)**: Archivos > 50 filas se delegan a `ImportProductsJob` para ejecutarse en segundo plano.
- **Reportes Detallados**: Resumen de creados, duplicados y errores con notificaciones de sesión.

### 3. Rutas (`routes/tenant.php`)
```php
GET  /import                      - Interfaz principal
GET  /import/template/{module}    - Descarga plantilla
POST /import/categories           - Importar categorías
POST /import/clients              - Importar clientes
POST /import/suppliers            - Importar proveedores
POST /import/products             - Importar productos
POST /import/purchases            - Importar compras
```

### 4. Background Jobs (`app/Jobs/Tenant/ImportProductsJob.php`)
- **Multitenencia**: El Job implementa `TenantAwareJob` para asegurar que los datos se guarden en la base de datos correcta del inquilino.
- **Eficiencia**: Permite procesar miles de registros sin bloquear la interfaz del usuario.
- **Requisito**: Requiere tener activo el worker de Laravel (`php artisan queue:work`).

## Plantillas CSV / Excel

### Categorías
```csv
nombre
Electrónica
```

### Clientes
```csv
nombre,nit_ci,telefono,email
Juan Pérez,12345678,555-1234,juan@example.com
```

### Proveedores
```csv
nombre,empresa,telefono,telefono_secundario,email,direccion
Carlos López,Distribuidora XYZ,555-5678,555-9012,carlos@xyz.com,Av. Principal 123
```

### Productos
```csv
codigo,nombre,categoria,proveedor,precio_compra,precio_venta,stock,stock_minimo,stock_maximo,fecha_entrada,imagen_url,descripcion
PROD001,Laptop HP,Electrónica,Distribuidora XYZ,500.00,750.00,10,5,50,2026-02-10,https://ejemplo.com/foto.jpg,Laptop empresarial
```
*Nota: La columna `categoria` y `proveedor` pueden ser nombres (se crean/buscan automáticamente).*

### Compras
```csv
codigo_producto,nombre_empresa_proveedor,cantidad,precio_unitario,numero_compra,comprobante,fecha_compra
PROD001,Distribuidora XYZ,10,500.00,COMP-001,FAC-12345,2026-02-10
```

## Validaciones y Lógica Especial

### Productos e Imágenes
- ✅ **Descarga de Imágenes**: Si la columna `imagen_url` contiene un enlace válido (http/https), el sistema lo descarga automáticamente.
- ✅ **Aislamiento por Tenant**: Las imágenes se guardan siguiendo la estructura del sistema de archivos del inquilino.
- ✅ **Categorías y Proveedores**: El sistema busca por nombre. Si la categoría no existe, la crea. Si el proveedor no existe, marca error (debe estar pre-registrado).

### Compras
- ✅ **Producto**: Búsqueda por `codigo` (Error si no existe).
- ✅ **Proveedor**: Búsqueda por `empresa` (Error si no existe).
- ✅ **Cantidad**: Debe ser número entero >= 1.
- ✅ **Stock Máximo**: La cantidad a comprar + stock actual NO puede superar el stock máximo del producto.
- ✅ **Duplicados**: Validación por `numero_compra`.
- ✅ **Actualización de Stock**: Incremento automático tras compra exitosa.

## Configuración Técnica e Infraestructura

Para que el procesamiento en segundo plano (Jobs) funcione correctamente, se deben realizar las siguientes configuraciones tanto en entorno local como en producción:

### 1. Configuración del Driver de Colas (.env)
Para este sistema, utilizaremos la base de datos para gestionar las colas, evitando la necesidad de instalar servicios adicionales como Redis. Cambia el driver en tu archivo `.env`:

```dotenv
# Configuración de Colas (Jobs)
# Para ejecutar en segundo plano, usa 'database'. Para ejecutar inmediato (bloqueante), usa 'sync'.
QUEUE_CONNECTION=database
```

> **Nota:** Si necesitas que las importaciones sean instantáneas y no tienes el worker activo, puedes cambiar temporalmente a `sync`, pero los archivos grandes podrían dar error de timeout.

### 2. Preparación de la Base de Datos
El sistema ya incluye las migraciones necesarias para las tablas de trabajos (`jobs`) tanto en la base de datos central como en la de los inquilinos. Solo asegúrate de haber ejecutado las migraciones:

```powershell
# Migraciones centrales
php artisan migrate

# Migraciones de inquilinos
php artisan tenants:migrate
```

### 3. Automatización en el Servidor (Producción)
Para no tener que ejecutar el comando manualmente, puedes usar **Cronicle** o **Supervisor**.

#### Opción A: Usando Cronicle (Recomendado si ya lo tienes)
Si usas Cronicle para gestionar tus tareas, puedes configurar el worker de la siguiente manera:

1. **Crear un nuevo Evento** en Cronicle.
2. **Tipo de Evento**: Shell Script.
3. **Comando**:
   ```bash
   cd /ruta-a-tu-proyecto && php artisan queue:work --stop-when-empty
   ```
4. **Frecuencia**: Configúralo para que se ejecute **cada minuto** (`* * * * *`).
   - *Nota*: El flag `--stop-when-empty` asegura que el proceso se cierre cuando termine de procesar los trabajos pendientes, y Cronicle lo volverá a levantar al minuto siguiente. Es la forma más segura y eficiente si no usas un monitor de procesos persistente.

#### Opción B: Usando Supervisor (Monitor persistente)
Si prefieres un proceso que esté "siempre encendido":
1. Crea un archivo en `/etc/supervisor/conf.d/laravel-worker.conf`:
```ini
[program:laravel-worker]
command=php /var/www/sistema-ventas/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
```
2. Actualiza Supervisor: `sudo supervisorctl update`.

### 4. Consideraciones de Multitenencia
Este sistema utiliza `stancl/tenancy`. Los Jobs están configurados para ser "Tenant Aware" (reconocen al inquilino). 
- Al ejecutar el worker (ya sea por Cronicle o Supervisor), el sistema automáticamente cambiará el contexto de la base de datos según el Job que esté procesando.
- **Importante:** Las migraciones de la base de datos central deben estar al día ya que la tabla `jobs` reside allí.

### 5. Permisos de Almacenamiento
El sistema descarga imágenes y procesa archivos temporales. Asegúrate de que las carpetas tengan permisos de escritura:

- `storage/app/public/products` (Imágenes de productos)
- `storage/app/temp_imports` (Archivos temporales de importación en segundo plano)

Si las imágenes no se ven, ejecuta:
```powershell
php artisan storage:link
```

---

## Solución de Problemas (FAQ)

### Error: "Allowed memory size of ... exhausted"
Si recibes este error al importar archivos muy grandes (más de 10,000 filas):
1. El sistema ya intenta aumentar el límite a 512MB dinámicamente.
2. Si el error persiste, edita tu `php.ini` y aumenta `memory_limit = 512M`.
3. Asegúrate de estar usando la opción de "segundo plano" (activada automáticamente para >50 filas) para que el servidor web no se bloquee.

### Error: "Maximum execution time exceeded"
Para archivos con muchas imágenes externas:
1. El sistema procesa estas importaciones en segundo plano mediante Jobs.
2. Asegúrate de que el comando `php artisan queue:work` esté ejecutándose.
3. El tiempo límite para jobs de productos es de 10 minutos (600s).

---

## Flujo de Uso

1. **Usuario accede** a "Importación Masiva" desde el menú
2. **Descarga plantilla** del módulo deseado
3. **Completa el archivo** con sus datos
4. **Sube el archivo** mediante el modal
5. **Revisa el resumen**: 
   - X registros creados
   - Y duplicados omitidos
   - Z errores encontrados
6. **Sistema recarga** automáticamente para mostrar los nuevos datos

## Respuesta JSON
```json
{
  "status": "success",
  "created": 15,
  "duplicates": 3,
  "errors": 1,
  "error_messages": [
    "Fila 5: El email no es válido"
  ]
}
```

## Características Técnicas

- **UTF-8 BOM**: Plantillas compatibles con Excel
- **Skip Duplicates**: Opción para omitir o intentar crear duplicados
- **Validación por Fila**: No detiene el proceso si una fila falla
- **Mensajes Específicos**: Indica exactamente qué fila tiene error
- **Límite de Archivo**: 10MB máximo
- **Formatos Soportados**: CSV, XLSX, XLS

## Próximas Mejoras Sugeridas

1. **PhpSpreadsheet**: Para mejor soporte de Excel
2. **Importación Asíncrona**: Para archivos muy grandes (jobs/queues)
3. **Preview de Datos**: Mostrar vista previa antes de importar
4. **Historial de Importaciones**: Log de todas las importaciones
5. **Exportación**: Permitir exportar datos existentes
6. **Validación Avanzada**: Reglas personalizadas por tenant

## Archivos Creados/Modificados

### Creados
- `resources/views/tenant/import/index.blade.php`
- `app/Http/Controllers/Tenant/ImportController.php`

### Modificados
- `routes/tenant.php` - Rutas ETL
- `resources/views/partials/aside.blade.php` - Enlace en menú
