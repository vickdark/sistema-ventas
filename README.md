# 🚀 Sistema de Ventas Multitenancy (Laravel 12)

Este proyecto es un **Sistema de Ventas completo con arquitectura Multitenancy (SaaS)** utilizando `stancl/tenancy`. Permite gestionar múltiples clientes (inquilinos) donde cada uno tiene su propia base de datos aislada.

---

## 🏗️ Estado del Proyecto

Actualmente, el sistema cuenta con la base de multitenencia configurada y los módulos base de seguridad en cada inquilino:

*   **Multitenancy:** Aislamiento de base de datos por cliente (Single-database per tenant).
*   **Gestión de Usuarios:** CRUD completo de usuarios por inquilino.
*   **Roles y Permisos:** Sistema dinámico de permisos con sincronización automática de rutas.
*   **Módulos de Ventas:** En desarrollo (Próximamente: Inventario, Clientes, Facturación).

---

## 🏗️ Arquitectura Multitenancy

El sistema utiliza una arquitectura de **Base de Datos por Inquilino (Single-Database Tenancy)**:

1.  **Aplicación Central (`central`)**:
    *   Gestionada por el **Dueño del SaaS** (Super Admin).
    *   Base de datos propia (`usuariosmultitenancy`).
    *   Se encarga de crear, editar y gestionar a los inquilinos (Tenants).
    *   Dominio principal: `laravel-multitenancy.test`
    *   Rutas protegidas bajo middleware `central_domain`.

2.  **Aplicaciones Inquilino (`tenant`)**:
    *   Utilizada por los **Clientes** de la plataforma.
    *   Cada cliente tiene **su propia base de datos** (ej: `usuariosmultitenancy_empresa_a`).
    *   Subdominios dinámicos: `empresa-a.laravel-multitenancy.test`.
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

## 📂 Estructura de Rutas (Importante)

*   **`routes/web.php`**: **SOLO** rutas del dominio central (Login Owner, Gestión de Tenants).
*   **`routes/tenant.php`**: Rutas de la aplicación del cliente (Dashboard, Usuarios, Roles). Estas rutas se cargan automáticamente cuando se detecta un subdominio válido.
