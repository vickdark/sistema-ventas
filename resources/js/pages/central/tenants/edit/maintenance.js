import { logToConsole, updateStep, showMaintenanceSuccess } from './ui.js';

export function initMaintenance(config) {
    const maintenanceButtons = document.querySelectorAll('.btn-maintenance');
    const csrfToken = config.tokens.csrf;

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
                            showMaintenanceSuccess();

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
}
