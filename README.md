# 🚀 Sistema de Ventas Multitenancy (Laravel 12)

Este proyecto es un **Sistema de Ventas completo con arquitectura Multitenancy (SaaS)** utilizando `stancl/tenancy`. Permite gestionar múltiples clientes (inquilinos) donde cada uno tiene su propia base de datos aislada.

---

## 🏗️ Estado del Proyecto

Actualmente, el sistema cuenta con la base de multitenencia configurada y los módulos base de seguridad en cada inquilino:

*   **Multitenancy:** Aislamiento de base de datos por cliente (Single-database per tenant).
*   **Gestión de Usuarios:** CRUD completo de usuarios por inquilino.
*   **Roles y Permisos:** Sistema dinámico de permisos con sincronización automática de rutas.
*   **Módulos de Ventas:** En desarrollo (Inventario, Clientes, Facturación).
*   **Módulo ETL (Importación):** Sistema robusto para carga masiva de datos (CSV/Excel) con soporte para imágenes y procesamiento en segundo plano. [Ver Documentación ETL](ETL_MODULE.md).

---

## 🏗️ Arquitectura Multitenancy

El sistema utiliza una arquitectura de **Base de Datos por Inquilino (Single-Database Tenancy)**:

1.  **Aplicación Central (`central`)**:
    *   Gestionada por el **Dueño del SaaS** (Super Admin).
    *   Base de datos propia (`usuariosmultitenancy`).
    *   Se encarga de crear, editar y gestionar a los inquilinos (Tenants).
    *   Dominio principal: `sistema-ventas.test`
    *   Rutas protegidas bajo middleware `central_domain`.

2.  **Aplicaciones Inquilino (`tenant`)**:
    *   Utilizada por los **Clientes** de la plataforma.
    *   Cada cliente tiene **su propia base de datos** (ej: `usuariosmultitenancy_empresa_a`).
    *   Subdominios dinámicos: `empresa-a.sistema-ventas.test`.
    *   Datos 100% aislados: Un cliente nunca puede ver los datos de otro.

---

## 💎 Características Principales

*   **Aislamiento Total:** Separación estricta de datos y usuarios entre empresas.
*   **Gestión Centralizada:** Panel de administración para el dueño del SaaS (crear empresas, suspender acceso).
*   **Autenticación Dual:** 
    *   `Guard: owner`: Para el administrador central.
    *   `Guard: web`: Para los usuarios de cada empresa.
*   **Rutas Inteligentes:** Detección automática del contexto (Central vs Inquilino).
*   **UI Dinámica:** El sidebar y el navbar se adaptan según si estás en el panel central o en el de una empresa.

---

## 🛠️ Stack Tecnológico

*   **Framework:** Laravel 12
*   **Multitenancy:** [stancl/tenancy v3](https://tenancyforlaravel.com/)
*   **Frontend:** Blade + Bootstrap 5.3 + Alpine.js
*   **Base de Datos:** MySQL 8.4
*   **Entorno de Desarrollo:** [Laravel Herd](https://herd.laravel.com/)
*   **Gestor de DB:** [DBeaver](https://dbeaver.io/)
*   **Componentes UI:** Grid.js, SweetAlert2, Tom-Select, FontAwesome.

---

## 📦 Herramientas y Requisitos

Para el desarrollo de este sistema, se utilizan las siguientes herramientas:

1.  **Laravel Herd:** Para el servidor web y entorno PHP (optimizado para macOS/Windows).
2.  **MySQL 8.4:** Instalado como servicio local.
3.  **DBeaver:** Cliente universal para la gestión de las bases de datos (Central y Tenants).
4.  **Composer & Node.js:** Para la gestión de dependencias de PHP y Assets.

---

## 📦 Dependencias y Herramientas Adicionales

Más allá de la instalación base de Laravel, este proyecto integra:

*   **[stancl/tenancy](https://tenancyforlaravel.com/):** El motor principal de la multitenencia.
*   **[barryvdh/laravel-ide-helper](https://github.com/barryvdh/laravel-ide-helper):** (Dev) Para mejorar el autocompletado y soporte del IDE.
*   Servidor

## ⚙️ Instalación y Puesta en Marcha

### 1. Requisitos Previos
*   **Laravel Herd** activo (soporte de subdominios `*.test`).
*   **MySQL 8.4** ejecutándose como servicio.

### 2. Configuración de Entorno (`.env`)
Asegúrate de configurar la conexión central correctamente:

```dotenv
DB_CONNECTION=central
DB_HOST=127.0.0.1
DB_PORT=3378
DB_DATABASE=sistema_ventas
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Configuración de Dominios Locales
Para que los subdominios funcionen en tu máquina local, debes editar tu archivo **hosts** (`C:\Windows\System32\drivers\etc\hosts` en Windows o `/etc/hosts` en Mac/Linux):

```text
127.0.0.1 laravel-multitenancy.test
127.0.0.1 mambacode.laravel-multitenancy.test
127.0.0.1 empresa-b.laravel-multitenancy.test
```

### 4. Instalación
```bash
# Instalar dependencias
composer install
npm install

# Generar archivos de ayuda para el IDE
php artisan ide-helper:generate
php artisan ide-helper:models -N
php artisan ide-helper:meta

# Generar clave
php artisan key:generate

# Migrar base de datos central (tablas tenants y domains)
php artisan migrate

# Crear enlace simbólico para logos y archivos
php artisan storage:link

# Compilar assets
npm run build
```

---

## � Uso del Sistema

### 1. Panel Central (Dueño del SaaS)
*   **URL:** `http://laravel-multitenancy.test/central/login`
*   **Funcionalidad:** Aquí registras nuevas empresas (Inquilinos). Al crear una empresa, el sistema automáticamente:
    1.  Crea el registro del Tenant.
    2.  Crea la Base de Datos exclusiva para ese Tenant.
    3.  Ejecuta las migraciones de la estructura del Tenant en esa nueva DB.
    4.  Crea el Dominio asociado.

### 2. Panel del Inquilino (Cliente)
*   **URL:** `http://{id-empresa}.laravel-multitenancy.test` (ej. `http://mambacode.laravel-multitenancy.test`)
*   **Funcionalidad:** Panel de gestión propio de la empresa (Usuarios, Roles, Permisos, etc.).

---

## �🔧 Comandos Útiles de Artisan

### Gestión de Base de Datos Central

*   `php artisan migrate`: Ejecuta las migraciones pendientes para la base de datos **central**.
*   `php artisan db:seed --class=CentralAdminSeeder`: Ejecuta el seeder `CentralAdminSeeder` para la base de datos **central`.
*   `php artisan migrate:fresh`: Elimina todas las tablas de la base de datos **central** y ejecuta todas las migraciones centrales. Para sembrar los datos del administrador central, ejecuta `php artisan db:seed --class=CentralAdminSeeder` por separado.

### Gestión de Inquilinos (stancl/tenancy)
*   `php artisan tenants:migrate`: Ejecuta las migraciones en **todos** los inquilinos.
*   `php artisan tenants:rollback`: Revierte la última migración en todos los inquilinos.
*   `php artisan tenants:seed`: Ejecuta los seeders en todos los inquilinos.
*   `php artisan tenants:list`: Muestra una lista de todos los inquilinos configurados.

### Gestión de Permisos (Custom)
*   `php artisan permissions:sync`: Sincroniza automáticamente las rutas del sistema (tenant) con la tabla de permisos.
    *   `--clean`: Elimina permisos de rutas que ya no existen.

### Desarrollo e IDE
*   `php artisan ide-helper:generate`: Genera el archivo de autocompletado para clases de Laravel.
*   `php artisan ide-helper:models`: Genera anotaciones PHPDoc para los modelos (facilita el uso de Eloquent).
*   `php artisan ide-helper:meta`: Genera el archivo meta para PhpStorm/VSCode.

### Otros Comandos
*   **Crear un usuario administrador central:**
*   Usa `tinker`: `\App\Models\User::create(['name'=>'Admin', 'email'=>'admin@central.com', 'password'=>bcrypt('password'), 'role_id'=>1]);`
*   **Crear un inquilino manualmente (tinker):**
    ```php
    $t = App\Models\Tenant::create(['id' => 'foo']);
    $t->domains()->create(['domain' => 'foo.laravel-multitenancy.test']);
    ```

---

## 🧱 Arquitectura de Código (Central vs Tenant)

### 1. Capa Central (Owner / SaaS)

*   Modelos: `App\Models\Central\*` (ej: `Central\Tenant`, `Central\User`, `CentralPaymentNotification`).
*   Controladores: `App\Http\Controllers\Central\*` (gestión de tenants, métricas, configuración).
*   Vistas: `resources/views/central/*` (login central, dashboard central, gestión de empresas, métricas, settings).
*   Rutas: `routes/web.php` (agrupadas con middleware de dominio central).
*   Tenancy: sin `InitializeTenancyByDomain`, siempre sobre la conexión `central`.

### 2. Capa Tenant (Empresa / Cliente)

*   Modelos: `App\Models\Tenant\*` (ej: `Usuario`, `Role`, `Permission`, `Product`, `Sale`, `CashRegister`).
*   Controladores: `App\Http\Controllers\Tenant\*` (usuarios, roles, ventas, caja, reportes, import, etc.).
*   Vistas: `resources/views/tenant/*` (auth tenant, dashboards, módulos de negocio, ETL, etc.).
*   Rutas: `routes/tenant.php` (cargadas por `TenantRouteServiceProvider` con `InitializeTenancyByDomain`).
*   Middlewares clave:
    *   `InitializeTenancyByDomain` y `PreventAccessFromCentralDomains`.
    *   `auth:web` para proteger rutas de usuario.
    *   `App\Http\Middleware\Tenant\CheckPermission` para permisos por ruta.

### 3. Permisos y Menú Dinámico (Tenant)

*   Fuente de la verdad: nombres de rutas tenant (ej: `products.index`, `sales.store`, `roles.edit`).
*   Sincronización: comando `php artisan permissions:sync` ([SyncPermissions](app/Console/Commands/SyncPermissions.php)) recorre `Route::getRoutes()` y sincroniza con `App\Models\Tenant\Permission`.
*   Exclusiones automáticas: rutas de sistema y autenticación (`sanctum.*`, `ignition.*`, `livewire.*`, `verification.*`, `password.*`, `login`, `logout`, `register`, `profile.*`, `storage.*`, `central.*`, `stancl.*`) y rutas utilitarias como `tenant.payment-pending`, `tenant.payment-notification.send`, `tenant.profile.password.update`.
*   Regla práctica:
    *   Rutas de negocio (CRUDs, reportes, caja, importaciones) sí deben generar permisos.
    *   Rutas técnicas o de autenticación solo requieren estar autenticado, no permisos.

---

## 📂 Estructura de Rutas (Importante)

*   **`routes/web.php`**: **SOLO** rutas del dominio central (Login Owner, Gestión de Tenants).
*   **`routes/tenant.php`**: Rutas de la aplicación del cliente (Dashboard, Usuarios, Roles). Estas rutas se cargan automáticamente cuando se detecta un subdominio válido.

---

## 🗺️ Mapa de Flujo del Sistema (de punta a punta)

1.  Acceso al Panel Central (Owner)
    *   El dueño entra a `https://sistema-ventas.test/central/login`.
    *   Autenticación con el guard `owner` contra la base de datos central.
    *   Una vez dentro, gestiona Tenants, configuraciones y métricas desde `resources/views/central/*`.

2.  Creación de un Nuevo Tenant
    *   Desde el panel central se crea una empresa.
    *   Se guarda el registro en `App\Models\Central\Tenant`.
    *   Se crea la base de datos del tenant.
    *   Se ejecutan las migraciones de `database/migrations/tenant`.
    *   Se crea el dominio/subdominio asociado en la tabla `domains`.

3.  Acceso al Panel del Tenant
    *   El usuario de la empresa entra a `https://{empresa}.sistema-ventas.test`.
    *   El middleware `InitializeTenancyByDomain` detecta el dominio, resuelve el Tenant y configura la conexión `tenant`.
    *   Se aplican los middlewares de tenant (`PreventAccessFromCentralDomains`, `CheckTenantPaymentStatus`).
    *   Las rutas se leen desde `routes/tenant.php` y las vistas desde `resources/views/tenant/*`.

4.  Autenticación de Usuarios del Tenant
    *   El login del tenant usa el guard `web` y el modelo `App\Models\Tenant\Usuario`.
    *   Las vistas de auth se resuelven en `resources/views/tenant/auth/*`.
    *   Una vez autenticado, el usuario es enviado a `route('dashboard')`.

5.  Resolución del Dashboard
    *   `Tenant\DashboardController` recibe al usuario autenticado.
    *   Evalúa el `role` y sus `permissions` para decidir qué dashboard mostrar (`tenant.dashboards.admin`, `tenant.dashboards.vendedor`, etc.).
    *   Si no hay una vista específica, cae en `tenant.dashboards.generic`.

6.  Navegación por Módulos de Negocio
    *   El usuario navega por rutas definidas en `routes/tenant.php` (`products.*`, `sales.*`, `cash-registers.*`, etc.).
    *   Cada ruta:
        *   Pasa por `auth:web`.
        *   Pasa por `CheckPermission` que verifica si el rol del usuario tiene el permiso asociado al nombre de la ruta.
    *   Los controladores en `App\Http\Controllers\Tenant\*` usan modelos `App\Models\Tenant\*` y vistas `resources/views/tenant/*`.

7.  Permisos y Menú Lateral
    *   El comando `php artisan permissions:sync`:
        *   Recorre todas las rutas tenant.
        *   Crea/actualiza registros en `App\Models\Tenant\Permission`.
        *   Marca cuáles son de menú (`is_menu`) y su módulo/orden (`module`, `order`).
    *   El sidebar del tenant se construye leyendo `permissions` del rol del usuario y mostrando solo las entradas de menú permitidas.

8.  Validación de Pago del Tenant
    *   Antes de acceder a las rutas protegidas, `CheckTenantPaymentStatus` valida si el tenant está al día.
    *   Si hay deuda o suspensión, redirige a `tenant.payment-pending`.
    *   El endpoint `tenant.payment-notification.send` permite enviar comprobantes de pago, pero está excluido del sistema de permisos porque solo requiere estar autenticado.

9.  Procesos Especiales (Caja, ETL, Reportes)
    *   Módulo de Caja:
        *   Usa `CashRegister`, `Sale`, `Abono`, etc., solo en la base de datos del tenant.
    *   Módulo ETL:
        *   Vistas en `resources/views/tenant/import/*`.
        *   Controlador `Tenant\ImportController` procesa archivos y crea/actualiza datos del tenant.
    *   Reportes:
        *   `Tenant\ReportController` calcula métricas usando solo datos del tenant.

10. Métricas Centrales sobre los Tenants
    *   El panel central de métricas recorre todos los `Central\Tenant`.
    *   Para cada uno, ejecuta código “inside tenant” (`$tenant->run(...)`) para leer datos agregados (usuarios, ventas, tamaño de base de datos).
    *   Si un tenant no tiene DB o le faltan tablas, el código captura la excepción y muestra valores seguros en lugar de romper el panel central.
