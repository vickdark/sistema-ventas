import { logToConsole, updateStep, showFinalState } from './ui.js';

export function initForm(config) {
    const tenantForm = document.getElementById('tenant-form');
    const idInput = document.getElementById('id');
    
    let processModal;
    try {
        const modalElement = document.getElementById('processModal');
        if (modalElement && typeof window.bootstrap !== 'undefined') {
            processModal = new window.bootstrap.Modal(modalElement);
        }
    } catch(e) { console.log('Bootstrap modal error:', e); }

    if (tenantForm) {
        tenantForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (processModal) {
                processModal.show();
            } else {
                logToConsole('Iniciando proceso (Modo sin modal)...');
            }
            
            logToConsole('Iniciando proceso de registro para: ' + idInput.value);
            updateStep(1, 'active');

            const formData = new FormData(this);
            
            try {
                logToConsole('Enviando solicitud al servidor central...');
                
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.tokens.csrf
                    }
                });

                const result = await response.json();

                if (response.ok) {
                    updateStep(1, 'complete');
                    logToConsole('Empresa registrada en base de datos central.', 'success');
                    
                    const isCreateDb = formData.get('create_db');
                    
                    if (isCreateDb) {
                        updateStep(2, 'active');
                        logToConsole('Configurando base de datos y dominio para subdominio: ' + idInput.value);
                        
                        setTimeout(() => {
                            updateStep(2, 'complete');
                            updateStep(3, 'active');
                            logToConsole('Ejecutando migraciones de tablas del inquilino...');

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
                                logToConsole('Tablas del sistema creadas correctamente.', 'success');

                                if (formData.get('seed')) {
                                    updateStep(4, 'active');
                                    logToConsole('Poblando base de datos con registros iniciales (roles, permisos, admin)...');
                                    setTimeout(() => {
                                        updateStep(4, 'complete');
                                        logToConsole('Seeders ejecutados con éxito.', 'success');
                                        showFinalState(config.host, idInput.value);
                                    }, 1500);
                                } else {
                                    showFinalState(config.host, idInput.value);
                                }
                            }, 2500);
                        }, 1000);
                    } else {
                        logToConsole('Saltando creación de DB y tablas (Modo: Solo Registro).', 'info');
                        document.getElementById('step-2').classList.add('opacity-50');
                        document.getElementById('step-3').classList.add('opacity-50');
                        document.getElementById('step-4').classList.add('opacity-50');
                        showFinalState(config.host, idInput.value);
                    }
                } else {
                    logToConsole('ERROR: ' + (result.message || 'Fallo en la validación'), 'error');
                    if(processModal) processModal.hide();
                    
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'error',
                            title: 'Error de Validación',
                            text: result.message || 'Hubo un problema al procesar la solicitud.',
                            confirmButtonColor: '#4e73df'
                        });
                    } else {
                        alert('Error: ' + (result.message || 'Hubo un problema al procesar la solicitud.'));
                    }
                }
            } catch (error) {
                logToConsole('ERROR CRÍTICO: ' + error.message, 'error');
                if(processModal) processModal.hide();
                
                if (window.Swal) {
                    window.Swal.fire({
                        icon: 'error',
                        title: 'Error Crítico',
                        text: 'Ocurrió un error inesperado en la comunicación con el servidor: ' + error.message,
                        confirmButtonColor: '#4e73df'
                    });
                } else {
                    alert('Error Crítico: ' + error.message);
                }
            }
        });
    }
}
