@extends('layouts.app')

@section('content')
@php
    $isPaid = $tenant->is_paid;
    $config = [
        'tenantId' => $tenant->id,
        'routes' => [
            'suspend' => route('central.tenants.suspend', $tenant->id),
            'activate' => route('central.tenants.mark-as-paid', $tenant->id),
            'maintenance' => route('central.tenants.maintenance', $tenant->id),
        ],
        'tokens' => [
            'csrf' => csrf_token()
        ]
    ];
@endphp
<div id="central-tenants-edit-page" data-config='@json($config)'></div>
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
                    <form action="{{ route('central.tenants.update', $tenant->id) }}" method="POST" enctype="multipart/form-data">
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
                                                       value="{{ old('business_name', $tenant->business_name) }}" placeholder="Ej: Mi Tienda">
                                                <small class="text-muted">Nombre que aparecerá en facturas y reportes</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="legal_name" class="form-label fw-semibold">Razón Social</label>
                                                <input type="text" class="form-control" id="legal_name" name="legal_name" 
                                                       value="{{ old('legal_name', $tenant->legal_name) }}" placeholder="Ej: Mi Tienda S.R.L.">
                                                <small class="text-muted">Nombre legal de la empresa</small>
                                            </div>

                                            <div class="col-md-6">
                                                <label for="tax_id" class="form-label fw-semibold">NIT/RUC</label>
                                                <input type="text" class="form-control" id="tax_id" name="tax_id" 
                                                       value="{{ old('tax_id', $tenant->tax_id) }}" placeholder="Ej: 123456789">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="phone" class="form-label fw-semibold">Teléfono</label>
                                                <input type="text" class="form-control" id="phone" name="phone" 
                                                       value="{{ old('phone', $tenant->phone) }}" placeholder="Ej: +591 12345678">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="email" class="form-label fw-semibold">Email</label>
                                                <input type="email" class="form-control" id="email" name="email" 
                                                       value="{{ old('email', $tenant->email) }}" placeholder="contacto@miempresa.com">
                                            </div>

                                            <div class="col-md-6">
                                                <label for="website" class="form-label fw-semibold">Sitio Web (Opcional)</label>
                                                <input type="url" class="form-control" id="website" name="website" 
                                                       value="{{ old('website', $tenant->website) }}" placeholder="https://miempresa.com">
                                            </div>

                                            <div class="col-12">
                                                <label for="address" class="form-label fw-semibold">Dirección</label>
                                                <textarea class="form-control" id="address" name="address" rows="2" 
                                                          placeholder="Dirección completa del negocio">{{ old('address', $tenant->address) }}</textarea>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="currency" class="form-label fw-semibold">Moneda</label>
                                                <select class="form-select" id="currency" name="currency">
                                                    <option value="COP" {{ old('currency', $tenant->currency) == 'COP' ? 'selected' : '' }}>Pesos Colombianos (COP)</option>
                                                    <option value="USD" {{ old('currency', $tenant->currency) == 'USD' ? 'selected' : '' }}>Dólares (USD)</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="business_type" class="form-label fw-semibold">Tipo de Negocio</label>
                                                <select class="form-select" id="business_type" name="business_type">
                                                    <option value="">Seleccionar...</option>
                                                    <option value="retail" {{ old('business_type', $tenant->business_type) == 'retail' ? 'selected' : '' }}>Retail / Minorista</option>
                                                    <option value="wholesale" {{ old('business_type', $tenant->business_type) == 'wholesale' ? 'selected' : '' }}>Mayorista</option>
                                                    <option value="services" {{ old('business_type', $tenant->business_type) == 'services' ? 'selected' : '' }}>Servicios</option>
                                                    <option value="restaurant" {{ old('business_type', $tenant->business_type) == 'restaurant' ? 'selected' : '' }}>Restaurante</option>
                                                    <option value="other" {{ old('business_type', $tenant->business_type) == 'other' ? 'selected' : '' }}>Otro</option>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label for="timezone" class="form-label fw-semibold">Zona Horaria</label>
                                                <select class="form-select" id="timezone" name="timezone">
                                                    <option value="America/Bogota" {{ old('timezone', $tenant->timezone) == 'America/Bogota' ? 'selected' : '' }}>Bogotá / Lima / Quito</option>
                                                    <option value="America/Caracas" {{ old('timezone', $tenant->timezone) == 'America/Caracas' ? 'selected' : '' }}>Caracas</option>
                                                    <option value="America/La_Paz" {{ old('timezone', $tenant->timezone) == 'America/La_Paz' ? 'selected' : '' }}>La Paz / Asunción</option>
                                                    <option value="America/Santiago" {{ old('timezone', $tenant->timezone) == 'America/Santiago' ? 'selected' : '' }}>Santiago</option>
                                                    <option value="America/Argentina/Buenos_Aires" {{ old('timezone', $tenant->timezone) == 'America/Argentina/Buenos_Aires' ? 'selected' : '' }}>Buenos Aires</option>
                                                    <option value="America/Sao_Paulo" {{ old('timezone', $tenant->timezone) == 'America/Sao_Paulo' ? 'selected' : '' }}>Sao Paulo</option>
                                                </select>
                                            </div>

                                            <div class="col-12">
                                                <label for="logo" class="form-label fw-semibold">
                                                    <i class="fas fa-image me-2"></i>Logo de la Empresa
                                                </label>
                                                @if($tenant->logo)
                                                    <div class="mb-3">
                                                        <img src="{{ asset('storage/' . $tenant->logo) }}" alt="Logo" class="img-thumbnail tenant-logo-preview">
                                                        <p class="small text-muted mb-0">Logo actual</p>
                                                    </div>
                                                @endif
                                                <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                                <small class="text-muted">Formatos: JPG, PNG. Tamaño máximo: 2MB (Solo si desea cambiarlo)</small>
                                            </div>

                                            <div class="col-12">
                                                <label for="invoice_footer" class="form-label fw-semibold">Pie de Página para Facturas (Opcional)</label>
                                                <textarea class="form-control" id="invoice_footer" name="invoice_footer" rows="2" 
                                                          placeholder="Texto que aparecerá al final de las facturas">{{ old('invoice_footer', $tenant->invoice_footer) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de Facturación -->
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-4 border shadow-sm mb-2 tenant-collapse-header" 
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
                                                    <option value="subscription" {{ old('service_type', $tenant->service_type) == 'subscription' ? 'selected' : '' }}>Suscripción</option>
                                                    <option value="purchase" {{ old('service_type', $tenant->service_type) == 'purchase' ? 'selected' : '' }}>Compra / Mantenimiento</option>
                                                </select>
                                                @error('service_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6" id="subscription_period_container">
                                                <label for="subscription_period" class="form-label fw-semibold">Periodo de Suscripción</label>
                                                <select class="form-select @error('subscription_period') is-invalid @enderror" id="subscription_period" name="subscription_period">
                                                    <option value="30" {{ old('subscription_period', $tenant->subscription_period) == '30' ? 'selected' : '' }}>Mensual (30 días)</option>
                                                    <option value="90" {{ old('subscription_period', $tenant->subscription_period) == '90' ? 'selected' : '' }}>Trimestral (90 días)</option>
                                                    <option value="365" {{ old('subscription_period', $tenant->subscription_period) == '365' ? 'selected' : '' }}>Anual (365 días)</option>
                                                </select>
                                                @error('subscription_period')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label for="next_payment_date" class="form-label fw-semibold" id="next_payment_date_label">Fecha de Facturación</label>
                                                <input type="date" class="form-control @error('next_payment_date') is-invalid @enderror" 
                                                       id="next_payment_date" name="next_payment_date" value="{{ old('next_payment_date', $tenant->next_payment_date) }}" required>
                                                @error('next_payment_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-6 d-flex align-items-end">
                                                <div class="form-check form-switch p-2 border rounded-3 w-100 bg-white shadow-sm">
                                                    <input class="form-check-input ms-0 me-2" type="checkbox" role="switch" id="is_paid" name="is_paid" value="1" {{ $tenant->is_paid ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="is_paid">¿Pago Realizado?</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4 pt-3 border-top d-flex gap-2">
                                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">
                                    <i class="fas fa-save me-2"></i> Guardar Cambios
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
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden border-start border-warning shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h5 class="fw-bold mb-1 text-warning"><i class="fas fa-tools me-2"></i>Acciones Avanzadas</h5>
                            <p class="text-muted small mb-0">Gestiona la infraestructura técnica del inquilino de forma manual.</p>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-md-end">
                            @if ($isPaid)
                                <button type="button" class="btn btn-outline-danger rounded-pill px-3 fw-bold shadow-sm" id="btn-suspend-tenant">
                                    <i class="fas fa-ban me-2"></i> Suspender
                                </button>
                            @endif
                            @if (!$isPaid)
                                <button type="button" class="btn btn-outline-success rounded-pill px-3 fw-bold shadow-sm" id="btn-activate-tenant">
                                    <i class="fas fa-check me-2"></i> Activar (Marcar Pagado)
                                </button>
                            @endif
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

<style>
    .shadow-soft { box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important; }
    .btn-primary { background: #4e73df; border: none; }
    .form-control:focus { border-color: #4e73df; box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.1); }
</style>
@endsection
