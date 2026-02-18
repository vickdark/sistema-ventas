export function initTenantsCreate(config) {
    const idInput = document.getElementById('id');
    const urlPreview = document.getElementById('url-preview');
    const dbPreview = document.getElementById('db-preview');
    const statusLabel = document.getElementById('id-status');
    const submitBtn = document.getElementById('btn-submit');
    
    // Config values
    const centralDb = config.centralDb;
    const host = config.host;
    const checkUrl = config.routes.check;

    if (typeof window.bootstrap === 'undefined') {
        // console.error('Bootstrap is not loaded yet. Make sure app.js is included correctly.');
        // return;
    }
    
    // Intentar obtener la instancia de modal de forma segura
    let processModal;
    try {
            const modalElement = document.getElementById('processModal');
            if (modalElement && typeof window.bootstrap !== 'undefined') {
            processModal = new window.bootstrap.Modal(modalElement);
            }
    } catch(e) { console.log('Bootstrap modal error:', e); }

    const tenantForm = document.getElementById('tenant-form');

    let timeout = null;

    const createDbToggle = document.getElementById('create_database');
    const seedDbToggle = document.getElementById('seed_database');
    const dbWarningText = document.getElementById('db-warning-text');
    const dbHelperText = document.getElementById('db-helper-text');
    const seederContainer = document.getElementById('seeder-toggle-container');

    // Lógica para campos de facturación
    const serviceTypeSelect = document.getElementById('service_type');
    const subscriptionPeriodSelect = document.getElementById('subscription_period');
    const subscriptionPeriodContainer = document.getElementById('subscription_period_container');
    const nextPaymentDateLabel = document.getElementById('next_payment_date_label');
    const nextPaymentDateInput = document.getElementById('next_payment_date');

    function calculateNextPaymentDate() {
        const today = new Date();
        // Empezar a contar desde mañana
        const startDate = new Date(today);
        startDate.setDate(today.getDate() + 1);

        let daysToAdd = 0;
        if (serviceTypeSelect.value === 'subscription') {
            daysToAdd = parseInt(subscriptionPeriodSelect.value) || 30;
        } else {
            // Para mantenimiento, asumimos anual (365 días) por defecto
            daysToAdd = 365; 
        }

        const nextDate = new Date(startDate);
        nextDate.setDate(startDate.getDate() + daysToAdd);

        // Formatear a YYYY-MM-DD para el input date
        const yyyy = nextDate.getFullYear();
        const mm = String(nextDate.getMonth() + 1).padStart(2, '0');
        const dd = String(nextDate.getDate()).padStart(2, '0');
        
        nextPaymentDateInput.value = `${yyyy}-${mm}-${dd}`;
    }

    function toggleBillingFields() {
        if (serviceTypeSelect.value === 'subscription') {
            subscriptionPeriodContainer.classList.remove('d-none');
            nextPaymentDateLabel.innerText = 'Próxima Fecha de Facturación';
        } else {
            subscriptionPeriodContainer.classList.add('d-none');
            nextPaymentDateLabel.innerText = 'Próxima Fecha de Cobro Mantenimiento';
        }
        calculateNextPaymentDate();
    }

    if (serviceTypeSelect) {
        serviceTypeSelect.addEventListener('change', toggleBillingFields);
        subscriptionPeriodSelect.addEventListener('change', calculateNextPaymentDate);
        // Ejecutar al cargar para inicializar la fecha
        toggleBillingFields(); 
    }

    if (createDbToggle) {
        createDbToggle.addEventListener('change', function() {
            if (!this.checked) {
                dbWarningText.classList.remove('d-none');
                dbHelperText.classList.add('d-none');
                seedDbToggle.checked = false;
                seedDbToggle.disabled = true;
                seederContainer.style.opacity = '0.5';
            } else {
                dbWarningText.classList.add('d-none');
                dbHelperText.classList.remove('d-none');
                seedDbToggle.disabled = false;
                seederContainer.style.opacity = '1';
            }
        });
    }

    // 1. Validación en tiempo real y vista previa
    if (idInput) {
        idInput.addEventListener('input', function() {
            const value = this.value.toLowerCase().replace(/[^\w-]/g, '');
            this.value = value;
            
            if (value) {
                urlPreview.innerHTML = `<span class="text-primary">${value}</span>${host}`;
                dbPreview.innerHTML = `${centralDb}_<span class="text-warning">${value}</span>`;
            } else {
                urlPreview.innerHTML = `<span class="text-primary">...</span>${host}`;
                dbPreview.innerHTML = `${centralDb}_<span class="text-warning">...</span>`;
                statusLabel.innerHTML = '';
                return;
            }

            clearTimeout(timeout);
            statusLabel.innerHTML = '<i class="fas fa-spinner fa-spin text-muted"></i>';
            
            timeout = setTimeout(async () => {
                try {
                    const response = await fetch(`${checkUrl}?id=${value}`);
                    const data = await response.json();
                    if (data.available) {
                        statusLabel.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> Disponible';
                        statusLabel.className = 'small fw-bold text-success';
                        idInput.classList.remove('is-invalid');
                        idInput.classList.add('is-valid');
                        submitBtn.disabled = false;
                    } else {
                        statusLabel.innerHTML = `<i class="fas fa-times-circle text-danger me-1"></i> ${data.message || 'No disponible'}`;
                        statusLabel.className = 'small fw-bold text-danger';
                        idInput.classList.add('is-invalid');
                        idInput.classList.remove('is-valid');
                        submitBtn.disabled = true;
                    }
                } catch (error) { console.error(error); }
            }, 500);
        });
    }

    // 2. Manejo del Modal, Consola y proceso AJAX
    const consoleEl = document.getElementById('terminal-console');

    function logToConsole(message, type = 'info') {
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
            step.classList.add('fw-bold', 'text-primary');
            icon.innerHTML = '<i class="fas fa-circle-notch fa-spin text-primary"></i>';
        } else if (state === 'complete') {
            step.classList.remove('text-primary');
            step.classList.add('text-success');
            icon.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
            const statusDiv = step.querySelector('.step-status');
            if (statusDiv) statusDiv.innerHTML = '<span class="badge bg-success">Listo</span>';
        }
    }

    function showFinalState() {
        document.getElementById('main-spinner').classList.add('d-none');
        document.getElementById('success-icon').classList.remove('d-none');
        document.getElementById('process-title').innerText = '¡Proceso Completado!';
        document.getElementById('process-subtitle').innerText = 'La empresa ha sido configurada con éxito y está lista para operar.';
        
        // Configurar el botón de visita
        const visitBtn = document.getElementById('btn-visit-tenant');
        if (idInput.value) {
            const protocol = window.location.protocol;
            visitBtn.href = `${protocol}//${idInput.value}${host}`;
        } else {
            visitBtn.classList.add('d-none');
        }

        document.getElementById('final-actions').classList.remove('d-none');
        logToConsole('--- FIN DEL PROCESO: LISTO PARA USAR ---', 'success');
    }

    if (tenantForm) {
        tenantForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Si tenemos modal de bootstrap, lo mostramos. Si no, mostramos alerta simple.
            if (processModal) {
                processModal.show();
            } else {
                // Fallback si bootstrap JS no cargó bien
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
                                        showFinalState();
                                    }, 1500);
                                } else {
                                    showFinalState();
                                }
                            }, 2500);
                        }, 1000);
                    } else {
                        logToConsole('Saltando creación de DB y tablas (Modo: Solo Registro).', 'info');
                        // Marcar pasos como no realizados o saltados
                        document.getElementById('step-2').classList.add('opacity-50');
                        document.getElementById('step-3').classList.add('opacity-50');
                        document.getElementById('step-4').classList.add('opacity-50');
                        showFinalState();
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

    console.log('Tenants Create Page Initialized');
}
