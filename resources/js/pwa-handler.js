import Swal from 'sweetalert2';

/**
 * PWA Handler Simplificado
 * Solo maneja el registro del Service Worker para que la app sea instalable
 */
export async function initPWA() {
    if (window.PWA_INITIALIZED) return;
    window.PWA_INITIALIZED = true;

    // Registro del Service Worker
    if ('serviceWorker' in navigator) {
        try {
            const registration = await navigator.serviceWorker.register('/sw.js', { 
                scope: '/',
            });

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
            console.error('PWA: Error al registrar SW:', error);
        }

        // Recarga suave cuando el nuevo SW toma el control
        let refreshing = false;
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (refreshing) return;
            refreshing = true;
            window.location.reload();
        });
    }
}

function showUpdateNotification(worker) {
    Swal.fire({
        title: 'Actualización disponible',
        text: 'Hay una nueva versión del sistema. ¿Deseas actualizar ahora?',
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
