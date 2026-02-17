@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('central.tenants.index') }}" class="text-decoration-none">Empresas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nueva Empresa</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-gray-800">Registrar Nueva Empresa</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="card-title mb-0 fw-bold">Información de Configuración</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('central.tenants.store') }}" method="POST" id="tenant-form" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label for="id" class="form-label fw-semibold small text-uppercase text-muted">ID de la Empresa / Subdominio</label>
                                <div class="input-group input-group-lg mb-2">
                                    <input type="text" class="form-control @error('id') is-invalid @enderror" 
                                         id="id" name="id" value="{{ old('id') }}" placeholder="ej: miempresa" required autofocus>
                                    <span class="input-group-text bg-light text-muted">.{{ request()->getHost() }}</span>
                                </div>
                                
                                <div class="preview-box p-3 bg-light rounded-3 border mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="text-primary small fw-bold mb-0 text-uppercase"><i class="fas fa-eye me-2"></i>Vista Previa de Recursos:</h6>
                                        <span id="id-status" class="small fw-bold"></span>
                                    </div>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="small d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Acceso URL:</span>
                                            <span class="fw-bold text-dark" id="url-preview"><span class="text-primary">...</span>.{{ request()->getHost() }}</span>
                                        </div>
                                        <div class="small d-flex justify-content-between align-items-center">
                                            <span class="text-muted">Base de Datos:</span>
                                            <span class="badge bg-secondary opacity-75 fw-normal" id="db-preview">{{ config('database.connections.central.database') }}_<span class="text-warning">...</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-text mt-0">
                                     <i class="fas fa-info-circle me-1"></i> El ID debe ser corto y sin espacios para garantizar la compatibilidad del sistema.
                                </div>
                                @error('id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Información de la Empresa (Colapsable) -->
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4 border shadow-sm mb-2 tenant-collapse-header" 
                                     data-bs-toggle="collapse" 
                                     data-bs-target="#businessInfoCollapse" 
                                     aria-expanded="false">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 p-2 rounded-circle me-3">
                                            <i class="fas fa-building text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">Información de la Empresa</h6>
                                            <small class="text-muted">Nombre comercial, NIT, logo y contacto</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-down text-muted transition-all" id="business-info-icon"></i>
                                </div>

                                <div class="collapse mt-3" id="businessInfoCollapse">
                                    <div class="card card-body border-0 shadow-none bg-transparent p-0">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label for="business_name" class="form-label fw-semibold">Nombre Comercial</label>
                                                <input type="text" class="form-control" id="business_name" name="business_name" 
                                                       value="{{ old('business_name') }}" placeholder="Ej: Mi Tienda">
                                                <small class="text-muted">Nombre que aparecerá en facturas y reportes</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="legal_name" class="form-label fw-semibold">Razón Social</label>
                                                <input type="text" class="form-control" id="legal_name" name="legal_name" 
                                                       value="{{ old('legal_name') }}" placeholder="Ej: Mi Tienda S.R.L.">
                                                <small class="text-muted">Nombre legal de la empresa</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="tax_id" class="form-label fw-semibold">NIT/RUC</label>
                                                <input type="text" class="form-control" id="tax_id" name="tax_id" 
                                                       value="{{ old('tax_id') }}" placeholder="Ej: 123456789">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="phone" class="form-label fw-semibold">Teléfono</label>
                                                <input type="text" class="form-control" id="phone" name="phone" 
                                                       value="{{ old('phone') }}" placeholder="Ej: +591 12345678">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="email" class="form-label fw-semibold">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       value="{{ old('email') }}" placeholder="contacto@miempresa.com">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="website" class="form-label fw-semibold">Sitio Web (Opcional)</label>
                                                <input type="url" class="form-control" id="website" name="website" 
                                                       value="{{ old('website') }}" placeholder="https://miempresa.com">
                                            </div>

                                            <div class="col-12">
                                                <label for="address" class="form-label fw-semibold">Dirección</label>
                                                <textarea class="form-control" id="address" name="address" rows="2" 
                                                          placeholder="Dirección completa del negocio">{{ old('address') }}</textarea>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="currency" class="form-label fw-semibold">Moneda</label>
                                                <select class="form-select" id="currency" name="currency">
                                                    <option value="COP" selected>Pesos Colombianos (COP)</option>
                                                    <option value="USD">Dólares (USD)</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="business_type" class="form-label fw-semibold">Tipo de Negocio</label>
                                                <select class="form-select" id="business_type" name="business_type">
                                                    <option value="">Seleccionar...</option>
                                                    <option value="retail">Retail / Minorista</option>
                                                    <option value="wholesale">Mayorista</option>
                                                    <option value="services">Servicios</option>
                                                    <option value="restaurant">Restaurante</option>
                                                    <option value="other">Otro</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="timezone" class="form-label fw-semibold">Zona Horaria</label>
                                                <select class="form-select" id="timezone" name="timezone">
                                                    <option value="America/Bogota" selected>Bogotá / Lima / Quito</option>
                                                    <option value="America/Caracas">Caracas</option>
                                                    <option value="America/La_Paz">La Paz / Asunción</option>
                                                    <option value="America/Santiago">Santiago</option>
                                                    <option value="America/Argentina/Buenos_Aires">Buenos Aires</option>
                                                    <option value="America/Sao_Paulo">Sao Paulo</option>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <label for="logo" class="form-label fw-semibold">
                                                    <i class="fas fa-image me-2"></i>Logo de la Empresa
                                                </label>
                                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                                <small class="text-muted">Formatos: JPG, PNG. Tamaño máximo: 2MB</small>
                                            </div>

                                            <div class="col-12">
                                                <label for="invoice_footer" class="form-label fw-semibold">Pie de Página para Facturas (Opcional)</label>
                                                <textarea class="form-control" id="invoice_footer" name="invoice_footer" rows="2" 
                                                          placeholder="Texto que aparecerá al final de las facturas">{{ old('invoice_footer') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Facturación -->
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4 border shadow-sm mb-2" 
                                     style="cursor: pointer;" 
                                     data-bs-toggle="collapse" 
                                     data-bs-target="#billingInfoCollapse" 
                                     aria-expanded="true">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 p-2 rounded-circle me-3">
                                            <i class="fas fa-file-invoice-dollar text-success"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">Información de Facturación</h6>
                                            <small class="text-muted">Tipo de servicio, periodos y fechas de pago</small>
                                        </div>
                                    </div>
                                    <i class="fas fa-chevron-up text-muted transition-all" id="billing-info-icon"></i>
                                </div>

                                <div class="collapse show mt-3" id="billingInfoCollapse">
                                    <div class="card card-body border-0 shadow-none bg-transparent p-0">
                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label for="service_type" class="form-label fw-semibold">Tipo de Servicio</label>
                                                <select class="form-select @error('service_type') is-invalid @enderror" id="service_type" name="service_type" required>
                                                    <option value="subscription" {{ old('service_type') == 'subscription' ? 'selected' : '' }}>Suscripción</option>
                                                    <option value="purchase" {{ old('service_type') == 'purchase' ? 'selected' : '' }}>Compra / Mantenimiento</option>
                                                </select>
                                                @error('service_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6" id="subscription_period_container">
                                                <label for="subscription_period" class="form-label fw-semibold">Periodo de Suscripción</label>
                                                <select class="form-select @error('subscription_period') is-invalid @enderror" id="subscription_period" name="subscription_period">
                                                    <option value="30" {{ old('subscription_period') == '30' ? 'selected' : '' }}>Mensual (30 días)</option>
                                                    <option value="90" {{ old('subscription_period') == '90' ? 'selected' : '' }}>Trimestral (90 días)</option>
                                                    <option value="365" {{ old('subscription_period') == '365' ? 'selected' : '' }}>Anual (365 días)</option>
                                                </select>
                                                @error('subscription_period')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="next_payment_date" class="form-label fw-semibold" id="next_payment_date_label">Fecha de Facturación</label>
                                                <input type="date" class="form-control @error('next_payment_date') is-invalid @enderror" 
                                                       id="next_payment_date" name="next_payment_date" value="{{ old('next_payment_date', date('Y-m-d')) }}" required>
                                                @error('next_payment_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 d-flex align-items-end">
                                                <div class="form-check form-switch p-2 border rounded-3 w-100 bg-white shadow-sm">
                                                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="is_paid" name="is_paid" value="1" checked>
                                                    <label class="form-check-label fw-bold" for="is_paid">¿Pago Realizado?</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuración Técnica -->
                            <div class="col-12">
                                <hr class="my-4">
                                <h6 class="text-uppercase text-muted fw-bold mb-3">
                                    <i class="fas fa-cog me-2"></i>Configuración Técnica
                                </h6>
                            </div>

                            <div class="col-md-12">
                                <div class="form-check form-switch p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between mb-3 shadow-sm transition-all hover-lift">
                                    <div class="ms-1">
                                        <label class="form-check-label fw-bold text-dark mb-0" for="create_database">
                                            <i class="fas fa-server text-primary me-2"></i> Crear Base de Datos y Tablas
                                        </label>
                                        <div class="small text-muted mt-1" id="db-helper-text">Provisiona automáticamente el almacenamiento físico y las tablas del sistema.</div>
                                        <div class="small text-danger mt-1 d-none font-italic" id="db-warning-text">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Solo se creará el registro técnico; la empresa no tendrá base de datos funcional.
                                        </div>
                                    </div>
                                    <input class="form-check-input ms-0 me-2 tenant-switch-lg" type="checkbox" role="switch" id="create_database" name="create_db" value="1" checked>
                                </div>

                                <div class="form-check form-switch p-3 bg-light rounded-3 border d-flex align-items-center justify-content-between shadow-sm transition-all hover-lift" id="seeder-toggle-container">
                                    <div class="ms-1">
                                        <label class="form-check-label fw-bold text-dark mb-0" for="seed_database">
                                            <i class="fas fa-database text-primary me-2"></i> Ejecutar Seeders
                                        </label>
                                        <div class="small text-muted mt-1">Crea automáticamente los roles, permisos y el usuario administrador inicial.</div>
                                    </div>
                                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="seed_database" name="seed" value="1" checked style="width: 3em; height: 1.5em; cursor: pointer;">
                                </div>
                            </div>

                            <div class="col-12 mt-4 pt-3 border-top d-flex gap-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm" id="btn-submit">
                                    <i class="fas fa-save me-2"></i> Registrar Empresa
                                </button>
                                <a href="{{ route('central.tenants.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                                    Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
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
                            <h4 class="fw-bold mb-3" id="process-title">Configurando Entorno de Empresa</h4>
                            <p class="text-muted mb-4" id="process-subtitle">Por favor espera mientras el sistema aprovisiona todos los recursos técnicos.</p>
                            
                            <div class="text-start bg-light rounded-3 p-4 border mb-4 shadow-sm" style="font-size: 0.95rem;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="process-step mb-3 d-flex align-items-center" id="step-1">
                                            <div class="step-icon me-3 text-muted"><i class="fas fa-circle-notch fa-spin"></i></div>
                                            <div class="step-text flex-grow-1">Registrando Empresa</div>
                                            <div class="step-status ms-2 small"></div>
                                        </div>
                                        <div class="process-step mb-3 d-flex align-items-center text-muted" id="step-2">
                                            <div class="step-icon me-3"><i class="far fa-circle"></i></div>
                                            <div class="step-text flex-grow-1">Base de Datos y Dominio</div>
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
                                <div class="text-secondary opacity-50 mb-2 border-bottom border-secondary pb-1">> Terminal de Despliegue v1.0 - Logs de Sistema</div>
                            </div>

                            <div id="final-actions" class="d-none d-flex justify-content-center gap-3">
                                <a id="btn-visit-tenant" href="#" target="_blank" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                                    <i class="fas fa-external-link-alt me-2"></i> VISITAR EMPRESA
                                </a>
                                <a href="{{ route('central.tenants.index') }}" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm">
                                    <i class="fas fa-check me-2"></i> FINALIZAR
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="alert alert-info border-0 shadow-sm rounded-4 mt-4 p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                        <i class="fas fa-magic text-info fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Configuración Automática</h6>
                        <p class="small mb-0 text-muted">A partir del ID, el sistema creará la base de datos (con el prefijo central), asociará el dominio wildcard y ejecutará las migraciones necesarias.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const idInput = document.getElementById('id');
        const urlPreview = document.getElementById('url-preview');
        const dbPreview = document.getElementById('db-preview');
        const statusLabel = document.getElementById('id-status');
        const submitBtn = document.getElementById('btn-submit');
        const centralDb = "{{ config('database.connections.central.database') }}";
        const host = ".{{ request()->getHost() }}";
        const checkUrl = "{{ route('central.tenants.check') }}";
        if (typeof window.bootstrap === 'undefined') {
            // console.error('Bootstrap is not loaded yet. Make sure app.js is included correctly.');
            // return;
        }
        // Intentar obtener la instancia de modal de forma segura, o crear una fallback simple
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
                // Para mantenimiento, asumimos anual (365 días) por defecto o lo que prefieras
                // El usuario mencionó "si es mantenimiento automaticamente calcule la fecha"
                // Asumiremos 365 días para mantenimiento a menos que se indique lo contrario
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

        serviceTypeSelect.addEventListener('change', toggleBillingFields);
        subscriptionPeriodSelect.addEventListener('change', calculateNextPaymentDate);
        
        // Ejecutar al cargar para inicializar la fecha
        toggleBillingFields(); 

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

        // 1. Validación en tiempo real y vista previa
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
                        'Accept': 'application/json'
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
    });
</script>

<style>
    .shadow-soft { box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important; }
    .btn-primary { background: #4e73df; border: none; }
    .form-control:focus { border-color: #4e73df; box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.1); }
    .process-step { transition: all 0.3s ease; }
</style>
@endsection
