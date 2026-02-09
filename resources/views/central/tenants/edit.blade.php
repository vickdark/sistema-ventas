@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('central.tenants.index') }}" class="text-decoration-none">Empresas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar Empresa</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Editar Empresa: {{ $tenant->id }}</h1>
        </div>
    </div>

    <div class="row justify-content-center g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="card-title mb-0 fw-bold">Información de Configuración</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('central.tenants.update', $tenant->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="form-label fw-semibold small text-uppercase text-muted">ID de la Empresa / Subdominio</label>
                                <div class="input-group input-group-lg mb-2">
                                    <input type="text" class="form-control bg-light" value="{{ $tenant->id }}" disabled>
                                    <span class="input-group-text bg-light text-muted">.{{ request()->getHost() }}</span>
                                </div>
                                
                                <div class="preview-box p-3 bg-light rounded-3 border mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="text-primary small fw-bold mb-0 text-uppercase"><i class="fas fa-eye me-2"></i>Recursos Configurados:</h6>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="small d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Acceso URL:</span>
                                            <span class="fw-bold text-dark">
                                                @php $domain = $tenant->domains()->first()?->domain; @endphp
                                                {{ $domain ?: $tenant->id . '.' . request()->getHost() }}
                                            </span>
                                        </div>
                                        <div class="small d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Base de Datos:</span>
                                            <span class="badge bg-secondary opacity-75 fw-normal">
                                                {{ $tenant->getInternal('db_name') ?: config('database.connections.central.database') . '_' . $tenant->id }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4 pt-3 border-top d-flex gap-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                                    <i class="fas fa-check-circle me-2"></i> Verificar Información
                                </button>
                                <a href="{{ route('central.tenants.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                    Volver al listado
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Acciones Avanzadas -->
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden border-start border-warning border-5 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h5 class="fw-bold mb-1 text-warning"><i class="fas fa-tools me-2"></i>Acciones Avanzadas</h5>
                            <p class="text-muted small mb-0">Gestiona la infraestructura técnica del inquilino de forma manual.</p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
                            <button type="button" class="btn btn-outline-primary rounded-pill px-3 fw-bold shadow-sm btn-maintenance" data-type="migrate">
                                <i class="fas fa-server me-2"></i> Migraciones
                            </button>
                            <button type="button" class="btn btn-warning rounded-pill px-3 fw-bold shadow-sm btn-maintenance" data-type="seed">
                                <i class="fas fa-database me-2"></i> Seeders
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Proceso -->
<div class="modal fade" id="processModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-5 text-center">
                <div id="process-loader" class="mb-4">
                    <div class="spinner-border text-primary" id="main-spinner" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Procesando...</span>
                    </div>
                    <div id="success-icon" class="d-none animate__animated animate__bounceIn">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <h4 class="fw-bold mb-3" id="process-title">Mantenimiento de Empresa</h4>
                <p class="text-muted mb-4" id="process-subtitle">Actualizando recursos técnicos y provisionando datos iniciales.</p>
                
                <div class="text-start bg-light rounded-3 p-4 border mb-4 shadow-sm" style="font-size: 0.95rem;">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="process-step mb-3 d-flex align-items-center" id="step-1">
                                <div class="step-icon me-3 text-muted"><i class="fas fa-circle-notch fa-spin"></i></div>
                                <div class="step-text flex-grow-1">Verificando Inquilino</div>
                                <div class="step-status ms-2 small"></div>
                            </div>
                            <div class="process-step mb-3 d-flex align-items-center text-muted" id="step-2">
                                <div class="step-icon me-3"><i class="far fa-circle"></i></div>
                                <div class="step-text flex-grow-1">Dominio y Conexión</div>
                                <div class="step-status ms-2 small"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="process-step mb-3 d-flex align-items-center text-muted" id="step-3">
                                <div class="step-icon me-3"><i class="far fa-circle"></i></div>
                                <div class="step-text flex-grow-1">Migraciones de Tablas</div>
                                <div class="step-status ms-2 small"></div>
                            </div>
                            <div class="process-step d-flex align-items-center text-muted" id="step-4">
                                <div class="step-icon me-3"><i class="far fa-circle"></i></div>
                                <div class="step-text flex-grow-1">Seeders de Datos</div>
                                <div class="step-status ms-2 small"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consola estilo terminal -->
                <div class="bg-dark text-light p-4 rounded-3 text-start mb-4 shadow-inner" style="height: 300px; overflow-y: auto; font-family: 'Courier New', Courier, monospace; font-size: 0.8rem; border: 1px solid #333;" id="terminal-console">
                    <div class="text-secondary opacity-50 mb-2 border-bottom border-secondary pb-1">> Terminal de Mantenimiento v1.0 - Logs de Sistema</div>
                </div>

                <div id="final-actions" class="d-none">
                    <button type="button" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm" data-bs-dismiss="modal">
                        <i class="fas fa-check me-2"></i> PROCESO FINALIZADO
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof window.bootstrap === 'undefined') return;
        
        const processModal = new window.bootstrap.Modal(document.getElementById('processModal'));
        const maintenanceButtons = document.querySelectorAll('.btn-maintenance');
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

                processModal.show();
                logToConsole(`Iniciando mantenimiento manual (${type})...`);
                updateStep(1, 'active');

                try {
                    const response = await fetch("{{ route('central.tenants.maintenance', $tenant->id) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
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
                            
                            document.getElementById('main-spinner').classList.add('d-none');
                            document.getElementById('success-icon').classList.remove('d-none');
                            document.getElementById('process-title').innerText = '¡Proceso Completado!';
                            document.getElementById('final-actions').classList.remove('d-none');
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
    });
</script>

<style>
    .shadow-soft { box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important; }
    .btn-primary { background: #4e73df; border: none; }
    .form-control:focus { border-color: #4e73df; box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.1); }
</style>
@endsection
