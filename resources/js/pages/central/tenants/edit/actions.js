import Notifications from '../../../../modules/Notifications';

export function initActions(config) {
    const csrfToken = config.tokens.csrf;
    const btnSuspend = document.getElementById('btn-suspend-tenant');
    const btnActivate = document.getElementById('btn-activate-tenant');

    if (btnSuspend) {
        btnSuspend.addEventListener('click', async () => {
            const confirmed = await Notifications.confirm({
                title: '¿Suspender Servicio?',
                text: `¿Estás seguro de SUSPENDER la empresa "${config.tenantId}"? El acceso será bloqueado.`,
                confirmButtonText: 'Sí, suspender',
                confirmButtonColor: '#e74a3b'
            });

            if (confirmed) {
                try {
                    Notifications.loading('Suspendiendo empresa...');
                    const response = await fetch(config.routes.suspend, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        await Notifications.success(result.message, '¡Empresa Suspendida!');
                        window.location.reload();
                    } else {
                        Notifications.error('Error', result.message || 'No se pudo suspender la empresa.');
                    }
                } catch (error) {
                    Notifications.error('Error', 'Ocurrió un error inesperado.');
                    console.error(error);
                }
            }
        });
    }

    if (btnActivate) {
        btnActivate.addEventListener('click', async () => {
            const confirmed = await Notifications.confirm({
                title: '¿Confirmar Pago?',
                text: `¿Estás seguro de marcar la empresa "${config.tenantId}" como PAGADA? Esto extenderá su fecha de vencimiento.`,
                confirmButtonText: 'Sí, confirmar pago',
                confirmButtonColor: '#1cc88a'
            });

            if (confirmed) {
                try {
                    Notifications.loading('Actualizando estado...');
                    const response = await fetch(config.routes.activate, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        await Notifications.success(result.message, '¡Pago Confirmado!');
                        window.location.reload();
                    } else {
                        Notifications.error('Error', result.message || 'No se pudo actualizar el pago.');
                    }
                } catch (error) {
                    Notifications.error('Error', 'Ocurrió un error inesperado.');
                    console.error(error);
                }
            }
        });
    }
}
