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

### 2. Controlador (`app/Http/Controllers/Tenant/ImportController.php`)
- **Generación de Plantillas**: Descarga automática de CSV con headers y ejemplo
- **Parsing de Archivos**: Soporte para CSV (Excel próximamente)
- **Validación Robusta**: Validación campo por campo con mensajes de error
- **Detección de Duplicados**: Omite automáticamente registros existentes
- **Reportes Detallados**: Resumen de creados, duplicados y errores

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

### 4. Navegación
- Enlace en sidebar: "Importación Masiva" (sección Herramientas)
- Disponible solo para usuarios tenant (no owner)

## Plantillas CSV

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
codigo,nombre,categoria_id,precio_compra,precio_venta,stock,stock_minimo,stock_maximo,fecha_entrada,imagen,descripcion
PROD001,Laptop HP,1,500.00,750.00,10,5,50,2026-02-10,,Laptop empresarial
```

### Compras
```csv
codigo_producto,nombre_empresa_proveedor,cantidad,precio_unitario,numero_compra,comprobante,fecha_compra
PROD001,Distribuidora XYZ,10,500.00,COMP-001,FAC-12345,2026-02-10
```

## Validaciones

### Categorías
- ✅ Nombre requerido (max 255 caracteres)
- ✅ Duplicados por nombre

### Clientes
- ✅ Nombre, NIT/CI, Teléfono, Email requeridos
- ✅ Email válido
- ✅ Duplicados por NIT/CI

### Proveedores
- ✅ Nombre, Empresa, Teléfono, Dirección requeridos
- ✅ Email opcional pero válido
- ✅ Duplicados por teléfono principal

### Productos
- ✅ Todos los campos validados
- ✅ category_id debe existir en BD
- ✅ Precios y stocks numéricos >= 0
- ✅ Fecha válida
- ✅ Duplicados por código
- ✅ user_id asignado automáticamente

### Compras
- ✅ Producto: Búsqueda por `codigo` (Error si no existe)
- ✅ Proveedor: Búsqueda por `empresa` (Error si no existe)
- ✅ Cantidad: Debe ser número entero >= 1
- ✅ **Stock Máximo**: La cantidad a comprar + stock actual NO puede superar el stock máximo del producto
- ✅ Duplicados: Validación por `numero_compra`
- ✅ user_id asignado automáticamente
- ✅ Actualización automática del stock del producto tras compra exitosa

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
