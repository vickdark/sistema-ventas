# Documentación de Implementación PWA y Soporte Offline

Este documento detalla la arquitectura y configuración implementada para convertir el sistema de ventas en una Aplicación Web Progresiva (PWA) con capacidades avanzadas de funcionamiento sin conexión (offline).

## 1. Arquitectura PWA
Se ha utilizado el plugin `vite-plugin-pwa` integrado con Vite y Laravel para gestionar el ciclo de vida de la aplicación y la persistencia de datos.

### Componentes Clave:
- **Service Worker**: Gestiona la interceptación de red y el almacenamiento en caché de activos (JS, CSS, Imágenes).
- **IndexedDB**: Base de datos local en el navegador utilizada para almacenar ventas cuando no hay conexión.
- **LocalStorage**: Utilizado para datos de configuración rápida y validación de licencias offline.

## 2. Configuración de Vite (`vite.config.js`)
El archivo [vite.config.js](file:///c:/Users/victo/Herd/sistema-ventas/vite.config.js) ha sido configurado para:
- **Estrategia de Caché**: 
  - `globPatterns`: Pre-cachea todos los recursos estáticos esenciales.
  - `runtimeCaching`: 
    - **Logos y Fotos**: Estrategia `CacheFirst` para carga instantánea de marcas de inquilinos.
    - **Storage**: Estrategia `StaleWhileRevalidate` para recursos subidos por inquilinos.
    - **Exclusiones**: El panel central (`/central`) está configurado como `NetworkOnly` para garantizar que la administración global siempre requiera internet.

## 3. Manejo de Datos Offline
### Almacenamiento de Ventas ([OfflineDB.js](file:///c:/Users/victo/Herd/sistema-ventas/resources/js/modules/OfflineDB.js))
Se implementó un módulo basado en la librería `idb` para manejar IndexedDB:
- `saveOfflineSale(saleData)`: Guarda una venta localmente si el servidor no responde.
- `getPendingSales()`: Recupera las ventas que aún no han sido sincronizadas.

### Sincronización ([pwa-handler.js](file:///c:/Users/victo/Herd/sistema-ventas/resources/js/pwa-handler.js))
El sistema escucha el evento `online` del navegador:
1. Detecta la recuperación de conexión.
2. Recupera todas las ventas de IndexedDB.
3. Las envía una a una al servidor mediante el endpoint de ventas.
4. Muestra notificaciones de éxito o error al usuario.

## 4. Verificación de Licencia Offline
Para cumplir con el requisito de seguridad, la fecha de vencimiento del inquilino se guarda en `window.TenantConfig` (inyectado en el header) y se persiste en caché:
- Al iniciar la aplicación, se verifica la fecha actual contra la fecha de vencimiento guardada.
- Si el sistema está offline y la fecha ha pasado, el acceso se bloquea automáticamente.

## 5. Instrucciones para que funcione correctamente
Para que la implementación esté 100% operativa, sigue estos pasos:

### A. Generar los archivos PWA
Cada vez que realices cambios en la lógica de JavaScript o CSS, debes ejecutar:
```bash
npm run build
```
Esto regenerará el archivo `sw.js` (Service Worker) en la carpeta `public/build`.

### B. Iconos Obligatorios
Asegúrate de tener estos archivos en `public/img/`:
- `logo-pwa-192.png` (192x192 px)
- `logo-pwa-512.png` (512x512 px)
Estos son necesarios para que el navegador permita la instalación de la App.

### C. SSL (Requisito de Navegadores)
Las PWA solo funcionan sobre **HTTPS** (o localhost). Asegúrate de que tu servidor de producción tenga un certificado SSL válido.

### D. Pruebas de Funcionamiento
1. Abre el sistema en el navegador.
2. Abre las herramientas de desarrollador (F12) -> pestaña **Application** -> **Service Workers**.
3. Activa el modo **Offline**.
4. Intenta realizar una venta. Verás que se guarda localmente.
5. Desactiva el modo **Offline**. Verás cómo el sistema sincroniza automáticamente la venta pendiente.

---
*Documentación generada el 11 de febrero de 2026.*
