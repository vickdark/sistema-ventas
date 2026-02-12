import { registerSW } from 'virtual:pwa-register';
import Swal from 'sweetalert2';
import { 
    getPendingSales, 
    markSaleAsSynced, 
    deleteSyncedSales,
    queuePendingSync,
    getPendingSyncs,
    markSyncAsDone
} from './modules/OfflineDB';

export function initPWA() {
    // Registro del Service Worker con auto-update
    const updateSW = registerSW({
        onNeedRefresh() {
            Swal.fire({
                title: 'Actualización disponible',
                text: 'Hay una nueva versión del sistema. ¿Deseas actualizar ahora?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Actualizar',
                cancelButtonText: 'Más tarde'
            }).then((result) => {
                if (result.isConfirmed) {
                    updateSW(true);
                }
            });
        },
        onOfflineReady() {
            console.log('El sistema está listo para trabajar sin conexión.');
        },
    });

    // Manejo de estado Online/Offline
    window.addEventListener('online', () => {
        document.body.classList.remove('is-offline');
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Conexión restaurada',
            showConfirmButton: false,
            timer: 3000
        });
        syncPendingData();
    });

    window.addEventListener('offline', () => {
        document.body.classList.add('is-offline');
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'warning',
            title: 'Trabajando sin conexión',
            text: 'El sistema guardará tus cambios localmente.',
            showConfirmButton: false,
            timer: 5000
        });
    });

    // Configurar interceptores de formularios
    setupFormInterceptors();

    // Verificación de Vencimiento Offline
    checkExpirationStatus();
}

function setupFormInterceptors() {
    document.addEventListener('submit', async (e) => {
        if (navigator.onLine) return;

        const form = e.target;
        // Solo interceptar si estamos en una ruta de inquilino (no central)
        if (!window.TenantConfig || !window.TenantConfig.isOfflineSupported) return;

        // Evitar interceptar el POS (ya tiene su propia lógica)
        if (form.id === 'pos-form') return;

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

function checkExpirationStatus() {
    const config = window.TenantConfig;
    if (!config || !config.expirationDate) return;

    if (navigator.onLine) {
        localStorage.setItem('offline_expiration_date', config.expirationDate);
        localStorage.setItem('offline_service_type', config.serviceType);
    }

    const localExpiration = localStorage.getItem('offline_expiration_date');
    if (localExpiration) {
        const expirationDate = new Date(localExpiration);
        const today = new Date();

        if (expirationDate < today) {
            showExpirationLock(localExpiration);
        }
    }
}

function showExpirationLock(date) {
    const formattedDate = new Date(date).toLocaleDateString();
    document.body.innerHTML = `
        <div style="height: 100vh; display: flex; align-items: center; justify-content: center; background: #f8f9fc; font-family: sans-serif; text-align: center; padding: 20px;">
            <div style="max-width: 500px; background: white; padding: 40px; border-radius: 15px; shadow: 0 10px 25px rgba(0,0,0,0.1);">
                <div style="color: #e74a3b; font-size: 60px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 style="color: #4e73df; margin-bottom: 10px;">Acceso Restringido</h1>
                <p style="color: #6e707e; font-size: 18px;">Tu ${window.TenantConfig.serviceType === 'subscription' ? 'suscripción' : 'licencia'} venció el <strong>${formattedDate}</strong>.</p>
                <p style="color: #858796;">Para seguir operando el sistema, por favor contacta al administrador para renovar tu acceso.</p>
                <div style="margin-top: 30px;">
                    <a href="javascript:location.reload()" style="text-decoration: none; background: #4e73df; color: white; padding: 12px 25px; border-radius: 25px; font-weight: bold;">Reintentar Conexión</a>
                </div>
            </div>
        </div>
    `;
}

async function syncPendingData() {
    const config = window.TenantConfig;
    if (!config) return;

    // 1. Sincronizar Ventas (Específico)
    await syncSales();

    // 2. Sincronizar Otros Módulos (Genérico)
    await syncGenericActions();
}

async function syncSales() {
    const pendingSales = await getPendingSales();
    if (pendingSales.length === 0) return;

    const config = window.TenantConfig;
    if (!config.routes || !config.routes.sales_store) return;

    console.log(`Sincronizando ${pendingSales.length} ventas...`);

    for (const sale of pendingSales) {
        try {
            const response = await fetch(config.routes.sales_store, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': config.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(sale)
            });

            if (response.ok) {
                await markSaleAsSynced(sale.id);
            }
        } catch (error) {
            console.error('Error sincronizando venta:', error);
            break; 
        }
    }
    await deleteSyncedSales();
}

async function syncGenericActions() {
    const pendingSyncs = await getPendingSyncs();
    if (pendingSyncs.length === 0) return;

    const config = window.TenantConfig;
    
    Swal.fire({
        title: 'Sincronizando cambios',
        text: `Procesando ${pendingSyncs.length} acciones pendientes...`,
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    let successCount = 0;

    for (const sync of pendingSyncs) {
        try {
            // Si el método es PUT/PATCH/DELETE, Laravel espera POST + _method
            const method = ['PUT', 'PATCH', 'DELETE'].includes(sync.method) ? 'POST' : sync.method;
            
            const response = await fetch(sync.url, {
                method: method,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': config.csrfToken,
                    'Accept': 'application/json'
                },
                body: sync.data // Ya es un string de URLSearchParams
            });

            if (response.ok) {
                await markSyncAsDone(sync.id);
                successCount++;
            } else {
                const error = await response.json().catch(() => ({ message: 'Error desconocido' }));
                console.error(`Error en sync ${sync.id}:`, error);
                
                // Si es un error de validación (422), podrías querer notificar al usuario
                if (response.status === 422) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación en sincronización',
                        text: `El registro de "${sync.module}" tiene datos inválidos y no pudo sincronizarse.`,
                        footer: 'Revisa los datos ingresados en modo offline.'
                    });
                    // Marcamos como fallido pero permitimos que continúe el resto
                    await markSyncAsDone(sync.id); 
                }
            }
        } catch (error) {
            console.error(`Fallo de red en sync ${sync.id}:`, error);
            break;
        }
    }

    Swal.close();

    if (successCount > 0) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: `Sincronizados ${successCount} cambios correctamente.`,
            showConfirmButton: false,
            timer: 3000
        });
        
        // Recargar si hay cambios sincronizados para ver los datos actualizados
        setTimeout(() => window.location.reload(), 3000);
    }
}
