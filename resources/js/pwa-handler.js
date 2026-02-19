import Swal from 'sweetalert2';
import { 
    getPendingSales, 
    markSaleAsSynced, 
    deleteSyncedSales,
    queuePendingSync,
    getPendingSyncs,
    markSyncAsDone
} from './modules/OfflineDB';

export async function initPWA() {
    // Evitar doble inicialización si el script se carga varias veces
    if (window.PWA_INITIALIZED) return;
    window.PWA_INITIALIZED = true;

    // Registro del Service Worker
    if ('serviceWorker' in navigator) {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js', { 
                scope: '/',
                updateViaCache: 'none'
            });

            console.log('Service Worker registrado con éxito:', registration.scope);

            // Forzar actualización si hay un nuevo worker esperando
            if (registration.waiting) {
                console.log('Nuevo Service Worker esperando actualización.');
            }

            registration.addEventListener('updatefound', () => {
                const newWorker = registration.installing;
                if (newWorker) {
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            showUpdateNotification(newWorker);
                        }
                    });
                }
            });

        } catch (error) {
            console.error('Fallo al registrar SW:', error);
        }

        // Forzar recarga cuando el nuevo SW toma el control
        let refreshing = false;
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (refreshing) return;
            refreshing = true;
            console.log('Nuevo Service Worker activado, recargando...');
            window.location.reload();
        });
    }

    // Eventos Online/Offline con feedback visual claro
    setupConnectivityListeners();

    // Interceptores
    setupFormInterceptors();
    setupLoginInterceptor();

    // Guardar usuario para uso offline
    if (window.TenantConfig && window.TenantConfig.user) {
        localStorage.setItem('offline_user', JSON.stringify(window.TenantConfig.user));
    }

    // Verificación de vencimiento
    checkExpirationStatus();

    // Estrategia de precarga: Solo si no se ha hecho recientemente
    handleAutoPreload();

    // Exponer funciones globales
    window.downloadOfflineMode = () => warmCache(true);
    window.verifyOfflineCache = verifyOfflineCache;
}

function showUpdateNotification(worker) {
    Swal.fire({
        title: 'Actualización disponible',
        text: 'Hay una nueva versión del sistema. ¿Deseas actualizar ahora para trabajar offline con lo último?',
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Más tarde',
        confirmButtonColor: '#4e73df'
    }).then((result) => {
        if (result.isConfirmed) {
            worker.postMessage({ type: 'SKIP_WAITING' });
        }
    });
}

function setupConnectivityListeners() {
    window.addEventListener('online', () => {
        document.body.classList.remove('is-offline');
        Notify.success('Conexión restaurada. Sincronizando datos...');
        syncPendingData();
    });

    window.addEventListener('offline', () => {
        document.body.classList.add('is-offline');
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Sin conexión: Modo Lectura',
            text: 'Puedes navegar por las secciones que ya visitaste o descargaste anteriormente.',
            showConfirmButton: false,
            timer: 7000,
            background: '#1a1a1a',
            color: '#fff'
        });
    });
}

async function handleAutoPreload() {
    if (!navigator.onLine) return;

    const lastPreload = localStorage.getItem('last_pwa_preload');
    const now = Date.now();
    const oneDay = 24 * 60 * 60 * 1000;

    // Solo precargar automáticamente una vez al día para no saturar
    if (!lastPreload || (now - parseInt(lastPreload)) > oneDay) {
        console.log('Iniciando precarga automática de rutas...');
        setTimeout(() => {
            warmCache(false).then(() => {
                localStorage.setItem('last_pwa_preload', Date.now().toString());
            });
        }, 5000); // Esperar 5s después del load
    }
}

export async function warmCache(manualTrigger = false) {
    if (!navigator.onLine) {
        if (manualTrigger) {
            Swal.fire({
                icon: 'error',
                title: 'Sin conexión',
                text: 'Necesitas estar conectado a internet para descargar el modo offline.'
            });
        }
        return;
    }

    if (manualTrigger) {
        Swal.fire({
            title: 'Preparando Modo Offline',
            html: 'Descargando recursos del sistema...<br><b>Esto puede tardar unos segundos.</b>',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }
    
    // Lista completa de módulos disponibles para el inquilino
    const modules = [
        'sales',
        'clients',
        'products',
        'purchases',
        'categories',
        'suppliers',
        'abonos',
        'cash-registers',
        'usuarios',
        'roles',
        'reports',
        'import'
    ];

    const routes = [
        '/dashboard',
        '/dashboard/admin',
        '/login',
        '/offline.html'
    ];

    // Generar rutas de listado y creación para cada módulo
    modules.forEach(module => {
        routes.push(`/${module}`); // Index (Listado)
        // Módulos que tienen vista de creación
        if (!['reports', 'import'].includes(module)) {
            routes.push(`/${module}/create`); // Formulario de Creación
        }
    });

    console.log(`Precargando ${routes.length} rutas para modo offline...`);
            
            // Ejecutar secuencialmente para evitar saturar el servidor local y evitar errores de red
            const batchSize = 1;
            let completed = 0;
            let failedRoutes = [];

            // Evitar cachear la misma ruta varias veces por sesión
            const currentCache = window.CACHE_REGISTERED_PATHS || new Set();
            window.CACHE_REGISTERED_PATHS = currentCache;

            // Abrir caché explícitamente para garantizar guardado
            const cache = await caches.open('pages-cache');

            for (const url of routes) {
                // Si ya se cacheó en este proceso, saltar
                if (currentCache.has(url)) {
                    completed++;
                    continue;
                }
                // Verificar conexión en cada iteración
                if (!navigator.onLine) {
                    console.log('🛑 Conexión perdida, deteniendo precarga.');
                    break;
                }

                try {
                    // Pequeño delay para no saturar
                    await new Promise(r => setTimeout(r, 200));
                    
                    // Fetch directo con credenciales para asegurar acceso a rutas protegidas
                    // IMPORTANTE: Sin X-Requested-With para que Laravel devuelva HTML (no JSON)
                    const response = await fetch(url, { 
                        method: 'GET',
                        credentials: 'include', // Enviar cookies de sesión
                        headers: { 
                            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                        }
                    });
                    
                    // Si nos redirige al login, es que la sesión no es válida o expiró
                    if (response.redirected && response.url.includes('login') && !url.includes('login')) {
                        console.warn(`⚠️ Ruta ${url} redirigió al login. No se cacheará.`);
                        continue;
                    }

                    if (response.ok) {
                        await cache.put(url, response.clone());
                        currentCache.add(url);
                        console.log(`✅ [Modo Offline] Cacheado: ${url}`);
                    } else {
                        throw new Error(`Status ${response.status}`);
                    }
                    
                } catch (err) {
                    console.error(`❌ Error precargando ruta: ${url}`, err);
                    failedRoutes.push(url);
                    
                    // Si es un error de red (offline), detenemos todo el proceso
                    if (err.name === 'TypeError' && err.message === 'Failed to fetch') {
                        console.warn('🛑 Error de red crítico detectado. Deteniendo precarga.');
                        break;
                    }
                } finally {
                    completed++;
                    if (manualTrigger) {
                        const percent = Math.round((completed / routes.length) * 100);
                        if (Swal.getHtmlContainer()) {
                            const b = Swal.getHtmlContainer().querySelector('b');
                            if (b) b.textContent = `Progreso: ${percent}%`;
                        }
                    }
                }
            }

            if (manualTrigger) {
        if (failedRoutes.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Descarga incompleta',
                html: `Se descargaron la mayoría de los recursos, pero <b>${failedRoutes.length} rutas</b> fallaron.<br>
                       Revisa la consola (F12) para más detalles.<br>
                       <small>Puedes intentar descargar de nuevo.</small>`,
                confirmButtonText: 'Entendido'
            });
        } else {
            Swal.fire({
                icon: 'success',
                title: '¡Listo!',
                text: 'El modo offline ha sido descargado exitosamente (100% verificado).',
                timer: 3000,
                showConfirmButton: false,
                footer: '<a href="#" onclick="window.verifyOfflineCache(); return false;">Verificar qué se guardó</a>'
            });
        }
    }
}

// Función de depuración expuesta
window.verifyOfflineCache = async () => {
    const cache = await caches.open('pages-cache');
    const keys = await cache.keys();
    const urls = keys.map(k => k.url.replace(window.location.origin, ''));
    console.log('Rutas en caché:', urls);
    Swal.fire({
        title: 'Contenido del Caché',
        html: `<div style="text-align: left; max-height: 300px; overflow-y: auto;">
                <strong>Total archivos: ${keys.length}</strong><br>
                <ul>${urls.map(u => `<li>${u}</li>`).join('')}</ul>
               </div>`,
        width: '600px'
    });
};

function parseTenantDate(dateStr) {
    if (!dateStr) return null;
    // YYYY-MM-DD -> local date (avoid UTC parsing pitfall)
    const ymd = /^(\d{4})-(\d{2})-(\d{2})$/;
    const dmySlash = /^(\d{2})\/(\d{2})\/(\d{4})$/;
    const dmyDash = /^(\d{2})-(\d{2})-(\d{4})$/;
    let m;
    if ((m = dateStr.match(ymd))) {
        return new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3]));
    }
    if ((m = dateStr.match(dmySlash))) {
        return new Date(Number(m[3]), Number(m[2]) - 1, Number(m[1]));
    }
    if ((m = dateStr.match(dmyDash))) {
        return new Date(Number(m[3]), Number(m[2]) - 1, Number(m[1]));
    }
    // Fallback: let browser parse (might include time)
    return new Date(dateStr);
}

function setupLoginInterceptor() {
    const loginForm = document.querySelector('form[action*="login"]');
    if (!loginForm) return;

    loginForm.addEventListener('submit', (e) => {
        if (navigator.onLine) return;

        e.preventDefault();
        
        const formData = new FormData(loginForm);
        const email = formData.get('email');
        const offlineUser = JSON.parse(localStorage.getItem('offline_user'));

        if (offlineUser && offlineUser.email === email) {
            Swal.fire({
                icon: 'success',
                title: 'Bienvenido (Offline)',
                text: `Iniciando sesión como ${offlineUser.name}`,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = '/dashboard';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error de inicio de sesión',
                text: 'No se encontraron credenciales guardadas para este usuario en este dispositivo.',
            });
        }
    });
}

function setupFormInterceptors() {
    document.addEventListener('submit', async (e) => {
        if (navigator.onLine) return;

        const form = e.target;
        // Solo interceptar si estamos en una ruta de inquilino (no central)
        if (!window.TenantConfig || !window.TenantConfig.isOfflineSupported) return;

        // Evitar interceptar el POS (ya tiene su propia lógica)
        if (form.id === 'pos-form') return;
        
        // Evitar interceptar login (ya tiene su propio interceptor)
        if (form.action.includes('login')) return;

        e.preventDefault();

        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        for (const [key, value] of formData.entries()) {
            // No podemos guardar archivos offline fácilmente
            if (value instanceof File && value.size > 0) {
                console.warn(`Archivo ignorado en modo offline: ${key}`);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'Archivos no soportados offline',
                    text: 'Las imágenes se ignorarán al guardar sin conexión.',
                    showConfirmButton: false,
                    timer: 4000
                });
                continue;
            }
            params.append(key, value);
        }

        const syncData = {
            url: form.action,
            method: (formData.get('_method') || form.method || 'POST').toUpperCase(),
            data: params.toString(), // Guardamos como string de URL
            module: document.title.split('-')[0].trim() || 'Módulo'
        };

        try {
            await queuePendingSync(syncData);
            
            Swal.fire({
                icon: 'success',
                title: 'Guardado localmente',
                text: 'Los cambios se sincronizarán cuando recuperes la conexión.',
                confirmButtonColor: '#4e73df'
            }).then(() => {
                // Intentar volver atrás o a la lista
                const backBtn = document.querySelector('a[href*="index"]');
                if (backBtn) {
                    window.location.href = backBtn.href;
                } else {
                    window.history.back();
                }
            });
        } catch (error) {
            console.error('Error al guardar offline:', error);
            Swal.fire('Error', 'No se pudo guardar la información localmente.', 'error');
        }
    });
}

async function syncPendingData() {
    if (!navigator.onLine) return;
    
    // Sincronizar Ventas del POS
    const pendingSales = await getPendingSales();
    if (pendingSales.length > 0) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: 'Sincronizando ventas...',
            showConfirmButton: false,
            timer: 3000
        });

        for (const sale of pendingSales) {
            try {
                const response = await fetch(window.TenantConfig.routes.sales_store, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(sale.data)
                });

                if (response.ok) {
                    await markSaleAsSynced(sale.id);
                } else if (response.status === 422) {
                    console.error('Error de validación al sincronizar venta:', await response.json());
                    // Marcar como synced para evitar bucle, pero notificar (idealmente mover a 'failed')
                    await markSaleAsSynced(sale.id); 
                }
            } catch (error) {
                console.error('Error de red al sincronizar venta:', error);
            }
        }
        
        await deleteSyncedSales();
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Ventas sincronizadas',
            showConfirmButton: false,
            timer: 3000
        });
    }

    // Sincronizar Formularios Genéricos
    const pendingSyncs = await getPendingSyncs();
    if (pendingSyncs.length > 0) {
        console.log(`Sincronizando ${pendingSyncs.length} formularios pendientes...`);
        
        for (const sync of pendingSyncs) {
            try {
                const response = await fetch(sync.url, {
                    method: sync.method,
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': window.TenantConfig.csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: sync.data
                });

                if (response.ok) {
                    await markSyncAsDone(sync.id);
                    Swal.fire({
                        toast: true,
                        position: 'bottom-end',
                        icon: 'success',
                        title: `${sync.module} sincronizado`,
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    if (response.status === 419) {
                        // Token expirado, recargar
                        window.location.reload();
                        return;
                    }
                    if (response.status === 422) {
                        const errors = await response.json();
                        console.error('Error validación sync:', errors);
                        Swal.fire({
                            toast: true,
                            title: `Error al sincronizar ${sync.module}`,
                            text: 'Datos inválidos. Revisa la consola.',
                            icon: 'error'
                        });
                        // Marcar como hecho para no bloquear, o implementar 'failed' queue
                        await markSyncAsDone(sync.id);
                    }
                }
            } catch (error) {
                console.error('Error red sync:', error);
            }
        }
    }
}

function checkExpirationStatus() {
    if (!window.TenantConfig || !window.TenantConfig.expirationDate) return;

    const expirationDate = parseTenantDate(window.TenantConfig.expirationDate);
    if (!expirationDate) return;

    const today = new Date();
    const daysUntilExpiration = Math.ceil((expirationDate - today) / (1000 * 60 * 60 * 24));

    // Comentamos la notificación de aviso previo a petición del usuario
    /*
    if (daysUntilExpiration <= 5 && daysUntilExpiration >= 0) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            title: 'Suscripción por vencer',
            text: `Tu servicio vence en ${daysUntilExpiration} días.`,
            showConfirmButton: false,
            timer: 6000
        });
    } else 
    */
    if (daysUntilExpiration < 0) {
        Swal.fire({
            icon: 'error',
            title: 'Servicio Vencido',
            text: 'Tu suscripción ha expirado. Por favor realiza el pago para continuar.',
            confirmButtonText: 'Ir a Pagar',
            allowOutsideClick: false,
            allowEscapeKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir a página de pago si existe
                window.location.href = '/billing/portal'; 
            }
        });
    }
}
