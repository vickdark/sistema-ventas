import Notifications from '../../../modules/Notifications';

export function initTenantsEdit(config) {
    const csrfToken = config.tokens.csrf;
    const btnSuspend = document.getElementById('btn-suspend-tenant');
    const btnActivate = document.getElementById('btn-activate-tenant');
    const maintenanceButtons = document.querySelectorAll('.btn-maintenance');
    const consoleEl = document.getElementById('terminal-console');
    
    // Billing Fields Logic
    const serviceTypeSelect = document.getElementById('service_type');
    const subscriptionPeriodSelect = document.getElementById('subscription_period');
    const subscriptionPeriodContainer = document.getElementById('subscription_period_container');
    const nextPaymentDateLabel = document.getElementById('next_payment_date_label');
    const nextPaymentDateInput = document.getElementById('next_payment_date');

    // Helper Functions
    function logToConsole(message, type = 'info') {
        if (!consoleEl) return;
        const line = document.createElement('div');
        const time = new Date().toLocaleTimeString([], { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
        line.className = 'mb-1 ' + (type === 'error' ? 'text-danger' : (type === 'success' ? 'text-success' : 'text-light'));
        line.innerHTML = `<span class="text-secondary small">[${time}]</span> <span class="opacity-75">></span> ${message}`;
        consoleEl.appendChild(line);
        consoleEl.scrollTop = consoleEl.scrollHeight;
    }

    function updateStep(stepNum, state) {
        const step = document.getElementById(`step-${stepNum}`);
        if (!step) return;
        const icon = step.querySelector('.step-icon');
        
        if (state === 'active') {
            step.classList.remove('text-muted');
            step.classList.add('fw-bold', 'text-warning');
            icon.innerHTML = '<i class="fas fa-circle-notch fa-spin text-warning"></i>';
        } else if (state === 'complete') {
            step.classList.remove('text-warning');
            step.classList.add('text-success');
            icon.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
            const statusDiv = step.querySelector('.step-status');
            if (statusDiv) statusDiv.innerHTML = '<span class="badge bg-success">Listo</span>';
        } else if (state === 'skipped') {
            step.classList.add('opacity-50');
            icon.innerHTML = '<i class="fas fa-forward text-muted"></i>';
        }
    }

    function calculateNextPaymentDate() {
        if (!nextPaymentDateInput) return;
        
        const today = new Date();
        const startDate = new Date(today);
        startDate.setDate(today.getDate() + 1);

        let daysToAdd = 0;
        if (serviceTypeSelect.value === 'subscription') {
            daysToAdd = parseInt(subscriptionPeriodSelect.value) || 30;
        } else {
            daysToAdd = 365; 
        }

        const nextDate = new Date(startDate);
        nextDate.setDate(startDate.getDate() + daysToAdd);

        const yyyy = nextDate.getFullYear();
        const mm = String(nextDate.getMonth() + 1).padStart(2, '0');
        const dd = String(nextDate.getDate()).padStart(2, '0');
        
        nextPaymentDateInput.value = `${yyyy}-${mm}-${dd}`;
    }

    function toggleBillingFields() {
        if (!serviceTypeSelect) return;
        
        if (serviceTypeSelect.value === 'subscription') {
            subscriptionPeriodContainer.classList.remove('d-none');
            nextPaymentDateLabel.innerText = 'Próxima Fecha de Facturación';
        } else {
            subscriptionPeriodContainer.classList.add('d-none');
            nextPaymentDateLabel.innerText = 'Próxima Fecha de Cobro Mantenimiento';
        }
    }

    // Initialize Billing Logic
    if (serviceTypeSelect) {
        serviceTypeSelect.addEventListener('change', function() {
            toggleBillingFields();
            calculateNextPaymentDate();
        });
        
        if (subscriptionPeriodSelect) {
            subscriptionPeriodSelect.addEventListener('change', calculateNextPaymentDate);
        }
        
        toggleBillingFields();
    }

    // Suspend Tenant
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

    // Activate Tenant
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

    // Maintenance Logic
    if (maintenanceButtons.length > 0) {
        let processModal;
        try {
            const modalElement = document.getElementById('processModal');
            if (modalElement && typeof window.bootstrap !== 'undefined') {
                processModal = new window.bootstrap.Modal(modalElement);
            }
        } catch(e) { console.error('Bootstrap modal error:', e); }

        maintenanceButtons.forEach(button => {
            button.addEventListener('click', async function() {
                const type = this.getAttribute('data-type');
                const actionName = type === 'migrate' ? 'ejecutar migraciones' : 'ejecutar seeders';
                
                const confirmResult = await window.Swal.fire({
                    title: `¿Confirmas ${actionName}?`,
                    text: type === 'seed' 
                        ? 'Esto revisará migraciones y ejecutará los seeders técnicos. ¿Deseas continuar?'
                        : 'Se buscarán nuevas tablas o cambios en la estructura de este inquilino.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: type === 'seed' ? '#f6c23e' : '#4e73df',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, proceder',
                    cancelButtonText: 'Cancelar',
                    customClass: { confirmButton: 'rounded-pill px-4 fw-bold', cancelButton: 'rounded-pill px-4' }
                });

                if (!confirmResult.isConfirmed) return;

                if (processModal) processModal.show();
                logToConsole(`Iniciando mantenimiento manual (${type})...`);
                updateStep(1, 'active');

                try {
                    const response = await fetch(config.routes.maintenance, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ type: type })
                    });

                    const result = await response.json();

                    updateStep(1, 'complete');
                    updateStep(2, 'active');
                    logToConsole('Inquilino verificado correctamente.', 'success');

                    setTimeout(() => {
                        updateStep(2, 'complete');
                        updateStep(3, 'active');
                        logToConsole('Ejecutando Artisan comandos en el servidor...');

                        if (result.output) {
                            const lines = result.output.split('\n');
                            lines.forEach((line, i) => {
                                if (line.trim()) {
                                    setTimeout(() => logToConsole(line.trim()), i * 30);
                                }
                            });
                        }

                        setTimeout(() => {
                            updateStep(3, 'complete');
                            
                            if (type === 'seed' || type === 'both') {
                                updateStep(4, 'complete');
                            } else {
                                updateStep(4, 'skipped');
                            }
                            
                            // Resumen de ejecutados
                            if (result.executed && (result.executed.migrations.length > 0 || result.executed.seeders.length > 0)) {
                                logToConsole('--- RESUMEN DE CAMBIOS REGISTRADOS ---', 'success');
                                result.executed.migrations.forEach(m => logToConsole(`🚀 MIGRACIÓN: ${m}`, 'success'));
                                result.executed.seeders.forEach(s => logToConsole(`🌱 SEEDER: ${s}`, 'success'));
                            } else {
                                logToConsole('No se detectaron nuevas migraciones o registros pendientes.', 'info');
                            }

                            logToConsole('Mantenimiento finalizado con éxito.', 'success');
                            
                            const mainSpinner = document.getElementById('main-spinner');
                            const successIcon = document.getElementById('success-icon');
                            const processTitle = document.getElementById('process-title');
                            const finalActions = document.getElementById('final-actions');

                            if (mainSpinner) mainSpinner.classList.add('d-none');
                            if (successIcon) successIcon.classList.remove('d-none');
                            if (processTitle) processTitle.innerText = '¡Proceso Completado!';
                            if (finalActions) finalActions.classList.remove('d-none');
                        }, 3000);
                    }, 1000);

                } catch (error) {
                    logToConsole('ERROR: ' + error.message, 'error');
                    window.Swal.fire({
                        icon: 'error',
                        title: 'Error en el proceso',
                        text: error.message || 'No se pudo completar el mantenimiento manual.',
                        confirmButtonColor: '#4e73df'
                    });
                }
            });
        });
    }

    console.log('Tenants Edit Page Initialized');
}
