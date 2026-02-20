# Módulo de Cotizaciones (Quotes)

## 1. Descripción General
El módulo de cotizaciones permite a los usuarios generar presupuestos para clientes sin afectar el inventario de forma inmediata. Estas cotizaciones pueden ser posteriormente convertidas en ventas directas, facilitando el proceso comercial de preventa.

## 2. Características Principales
- **Interfaz tipo POS**: Diseño moderno optimizado para pantalla completa (sin scroll global) que hereda la experiencia del Punto de Venta (`pos-page`).
- **Catálogo Visual de Productos**: Navegación fluida de productos mediante **Swiper.js**, organizado por categorías y con soporte de imágenes.
- **Gestión de Clientes Avanzada**: 
    - Búsqueda inteligente mediante **TomSelect**.
    - Creación de clientes en tiempo real mediante el modal de **Cliente Rápido**.
- **Conversión Directa**: Funcionalidad para transformar una cotización PENDIENTE en una VENTA oficial con un solo clic, automatizando la descarga de inventario.
- **Parámetros de Cotización**: Control de fechas de vencimiento (validez) y notas comerciales personalizadas.

## 3. Arquitectura Técnica

### Backend (Laravel / Tenancy)
- **Controller**: `App\Http\Controllers\Tenant\QuoteController`
    - `index()`: Retorna la vista y datos JSON para el listado con soporte para búsqueda y paginación (Grid.js).
    - `create()`: Carga los datos necesarios para la interfaz POS (Sucursales, Productos con Categorías, Clientes).
    - `store()`: Procesa la creación de la cotización usando transacciones de base de datos (`DB::transaction`).
    - `convert()`: Ejecuta la validación de stock y transforma la cotización en una venta (`Sale` y `SaleItem`).
- **Models**:
    - `Quote`: Almacena metadatos (nro, cliente, total, estado, expiración).
    - `QuoteItem`: Detalle de los productos cotizados con sus precios en el momento de la oferta.

### Frontend (JavaScript Dinámico)
- **Directorios**: `resources/js/pages/tenants/quotes/`
- **Inicializador**: `create.js` (Gestionado por `PageLoader.js`).
- **Componentes Reutilizados**:
    - `CustomerManager`: Gestiona la lógica de selección y creación de clientes, compartida con el POS de ventas.
    - **Swiper Modules**: Implementa el grid de productos dinámico con soporte táctil.
- **Estilos**: Dependencia de `resources/css/pages/tenants/sales/pos/` para garantizar consistencia visual absoluta.

## 4. Funcionamiento del Frontend (`create.js`)
1. **Inicialización**: Se activa la clase `sidebar-mini` y `pos-page` para maximizar el área de trabajo.
2. **Swiper Initialization**: Configura el grid de productos (filas/columnas) basado en el tamaño de pantalla del usuario.
3. **Cart Logic**: Mantiene un estado interno (`cart[]`) que sincroniza el resumen de cotización, calcula totales y subtotales en tiempo real.
4. **Persistence**: Comunicación vía `Fetch API` con el servidor enviando un payload estructurado de items y metadatos del cliente.

## 5. Flujo de Trabajo (Workflow)
1. **Apertura**: El usuario accede a "Nueva Cotización".
2. **Selección**: Se filtran productos por categoría o nombre y se añaden al panel derecho.
3. **Identificación**: Se selecciona el cliente. Si es nuevo, se pulsa el botón **(+)** para abrir el formulario rápido.
4. **Finalización**: Se genera la cotización. Esta aparece en el listado general con estado **PENDIENTE**.
5. **Cierre**: Cuando el cliente acepta, se presiona "Convertir en Venta". El sistema crea automáticamente la venta y descuenta el stock del inventario.

---
*Documento Técnico del Sistema de Ventas - Refinado en Febrero 2026*
