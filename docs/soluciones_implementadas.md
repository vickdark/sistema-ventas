# Documentación de Soluciones Implementadas en Sistema de Ventas

Este documento detalla los problemas encontrados y las soluciones aplicadas durante la configuración y depuración del sistema de ventas.

---

## 1. Configuración del Entorno de Producción (`.env`)

### Problema
La aplicación necesitaba ser configurada para un entorno de producción, lo que implicaba ajustar variables clave como `APP_ENV`, `APP_DEBUG`, `LOG_LEVEL` y la configuración de sesión para un comportamiento adecuado en un servidor en vivo.

### Solución
Se realizaron los siguientes ajustes en el archivo `c:\Users\victo\Herd\sistema-ventas\.env`:

*   **`APP_ENV=production`**: Establece el entorno de la aplicación a producción.
*   **`APP_DEBUG=false`**: Deshabilita el modo de depuración para evitar la exposición de información sensible en producción.
*   **`LOG_LEVEL=error`**: Configura el nivel de registro para solo capturar errores, reduciendo el ruido en los logs.
*   **`SESSION_DOMAIN=.sistema-ventas.mambacode.dev`**: Define el dominio de la sesión para que las cookies sean válidas en todos los subdominios (`.mambacode.dev`). Esto es crucial para la persistencia de la sesión en entornos multi-tenant.
*   **`SESSION_SECURE_COOKIE=true`**: Asegura que las cookies de sesión solo se envíen a través de conexiones HTTPS, mejorando la seguridad.

**Acciones Adicionales:**
*   Se ejecutaron los comandos `php artisan config:clear` y `php artisan route:clear` para limpiar las cachés de configuración y rutas, asegurando que los nuevos valores del `.env` fueran cargados correctamente.

---

## 2. Error 404 en Subdominios de Inquilinos

### Problema
Al acceder a subdominios de inquilinos (ej. `https://mambatest.sistema-ventas.mambacode.dev/`), la aplicación devolvía un error 404, a pesar de que la creación de inquilinos y sus bases de datos funcionaba.

### Solución
Se identificaron y corrigieron dos problemas principales:

1.  **Bloqueo de Rutas en `tenant.php`**:
    *   Se encontró un bloque `if (!in_array(request()->getHost(), config('tenancy.central_domains', [])))` en `c:\Users\victo\Herd\sistema-ventas\routes\tenant.php`. Este condicional estaba impidiendo que las rutas específicas de los inquilinos se registraran correctamente si la solicitud no provenía de un dominio central, lo cual era un comportamiento incorrecto para las rutas de inquilinos.
    *   **Corrección:** Se eliminó este bloque condicional para permitir que las rutas de inquilinos se cargaran siempre que la aplicación estuviera en el contexto de un inquilino.

2.  **Nombre de Ruta Duplicado**:
    *   Se detectó un error de "Unable to prepare route [password] for serialization" debido a un nombre de ruta duplicado (`tenant.password.update.ajax`).
    *   **Corrección:** Se renombró la ruta duplicada a `tenant.profile.password.update` en `c:\Users\victo\Herd\sistema-ventas\routes\tenant.php` para resolver el conflicto.

**Acciones Adicionales:**
*   Se ejecutaron `php artisan config:clear` y `php artisan route:clear` después de los cambios para asegurar que las rutas se recompilaran sin errores.

---

## 3. Problemas de Carga de Imágenes para Inquilinos

### Problema
Las imágenes almacenadas en el sistema de archivos de Laravel no se mostraban correctamente en los subdominios de los inquilinos.

### Solución
El usuario ejecutó el siguiente comando:

```bash
php artisan storage:link
```

Este comando crea un enlace simbólico desde `public/storage` a `storage/app/public`, permitiendo que los archivos almacenados en el disco `public` de Laravel sean accesibles a través de la web.

---

## 4. Login de Inquilinos Refresca sin Autenticar

### Problema
Al intentar iniciar sesión en el panel de un inquilino (`https://mambatest.sistema-ventas.mambacode.dev/login`), la página se refrescaba sin autenticar al usuario, incluso con credenciales válidas. Esto indicaba un problema con la persistencia de la sesión o la validación.

### Solución
Se abordó el problema ajustando la configuración de sesión y añadiendo herramientas de depuración:

1.  **Configuración de Sesión en `.env`**:
    *   Se confirmó y ajustó `SESSION_DOMAIN=.sistema-ventas.mambacode.dev` y `SESSION_SECURE_COOKIE=true` en `c:\Users\victo\Herd\sistema-ventas\.env`. Estos ajustes son vitales para que las cookies de sesión sean compartidas y seguras a través de los subdominios.

2.  **Depuración de Autenticación**:
    *   Se añadió una línea de registro (`Log::info`) en el método `authenticate` de `c:\Users\victo\Herd\sistema-ventas\app\Http\Requests\Auth\LoginRequest.php` para rastrear el flujo de autenticación y verificar si `Auth::attempt` se ejecutaba y qué resultado devolvía.

**Acciones Adicionales:**
*   Se limpiaron las cachés de configuración y rutas después de los cambios.

---

## 5. Configuración de Apache para `AllowOverride All`

### Problema
El servidor Apache no estaba procesando correctamente los archivos `.htaccess` de Laravel, lo que es fundamental para el enrutamiento de la aplicación. Esto se manifestaba en errores 404 o en la imposibilidad de acceder a las rutas de la aplicación.

### Solución
Se modificó la configuración global de Apache para permitir el uso de archivos `.htaccess`:

1.  **Edición de `apache2.conf`**:
    *   Se instruyó al usuario para que editara el archivo `/etc/apache2/apache2.conf`.
    *   Dentro de la directiva `<Directory /var/www/>`, se cambió `AllowOverride None` a `AllowOverride All`. Esto habilita el procesamiento de archivos `.htaccess` en el directorio `/var/www/` y sus subdirectorios.

    ```apache
    <Directory /var/www/>
            Options Indexes FollowSymLinks
            AllowOverride All  # <-- CAMBIADO DE 'None' A 'All'
            Require all granted
    </Directory>
    ```

2.  **Reiniciar Apache**:
    *   Después de guardar los cambios, se ejecutó `sudo systemctl restart apache2` para aplicar la nueva configuración.

---

## 6. Depuración de Rutas del Dominio Central con Apache LogLevel

### Problema
El dominio central (`https://sistema-ventas.mambacode.dev/`) continuaba mostrando un error 404, y los logs de Apache no proporcionaban suficiente información para diagnosticar por qué las solicitudes no llegaban a la aplicación Laravel.

### Solución
Se implementaron medidas de depuración avanzadas en Apache y Laravel:

1.  **Ajuste de `LogLevel` en Virtual Host**:
    *   Inicialmente, se añadió `LogLevel debug` a la configuración del Virtual Host en `/etc/apache2/sites-available/sistema-ventas.conf` para obtener más detalles en los logs de errores de Apache.
    *   Posteriormente, para un rastreo más granular del módulo `mod_rewrite` (esencial para Laravel), se cambió a `LogLevel alert rewrite:trace8` en el mismo archivo. Esto proporciona un nivel de detalle muy alto sobre cómo Apache procesa las reglas de reescritura.

    ```apache
    <VirtualHost *:443>
        ServerName sistema-ventas.mambacode.dev
        # ... otras configuraciones ...
        LogLevel alert rewrite:trace8 # <-- CAMBIADO PARA DEPURACIÓN DE REWRITE
        ErrorLog ${APACHE_LOG_DIR}/sistema-ventas_error.log
        CustomLog ${APACHE_LOG_DIR}/sistema-ventas_access.log combined
    </VirtualHost>
    ```

2.  **Registro en `WelcomeController`**:
    *   Para confirmar si las solicitudes estaban llegando a la aplicación Laravel, se añadió una línea de registro en el controlador que maneja la ruta raíz del dominio central:

    ```php
    // c:\Users\victo\Herd\sistema-ventas\app\Http\Controllers\WelcomeController.php
    use Illuminate\Support\Facades\Log;

    class WelcomeController extends Controller
    {
        public function __invoke(): View
        {
            $quote = Inspiring::quote();
            Log::info('WelcomeController ha sido alcanzado en el dominio central.'); // <-- LÍNEA AÑADIDA
            $cleanQuote = preg_replace('/<[^>]*>/', '', $quote);
            return view('welcome', ['quote' => $cleanQuote]);
        }
    }
    ```

3.  **Reiniciar Apache**:
    *   Es crucial reiniciar Apache (`sudo systemctl restart apache2`) después de cada cambio en la configuración del Virtual Host para que los nuevos niveles de `LogLevel` surtan efecto.
