@extends('layouts.guest')

@section('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
@endsection

@php
    $bodyClass = 'bg-light';
@endphp

@section('body')
<div class="page-wrapper d-flex justify-content-center align-items-center min-vh-100 py-4 px-3 service-{{ $tenant->service_type }}">
    <div class="main-card border-0 shadow-2xl rounded-5 overflow-hidden animate__animated animate__fadeIn">
        <div class="row g-0 h-100">
            <!-- Columna Izquierda: Branding & Status -->
            <div class="col-lg-5 bg-gradient-corporate d-none d-lg-flex flex-column justify-content-between p-5 text-white position-relative overflow-hidden">
                <div class="bg-pattern position-absolute top-0 start-0 w-100 h-100"></div>
                
                <div class="brand-section position-relative z-index-10">
                    <div class="mb-5">
                        @if($tenant->logo)
                            <div class="logo-container bg-white p-3 rounded-4 shadow-sm d-inline-block">
                                <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->business_name }}" 
                                     class="logo-img">
                            </div>
                        @else
                            <div class="logo-placeholder bg-white bg-opacity-10 rounded-4 d-inline-flex align-items-center justify-content-center border border-white border-opacity-20">
                                <i class="fas fa-building fa-2x text-white"></i>
                            </div>
                        @endif
                    </div>
                    <h1 class="h2 fw-bold mb-2">{{ $tenant->business_name ?? 'Portal de Cliente' }}</h1>
                    <div class="d-flex align-items-center text-white-50">
                        <i class="fas fa-globe me-2 small"></i>
                        <span class="small">{{ $tenant->id }}.{{ request()->getHost() }}</span>
                    </div>
                </div>

                <div class="status-indicator position-relative z-index-10">
                    <div class="p-4 rounded-4 bg-white bg-opacity-10 border border-white border-opacity-10 backdrop-blur-sm">
                        <div class="d-flex align-items-center gap-3">
                            <div class="pulse-container">
                                <div class="pulse-dot {{ $tenant->service_type === 'subscription' ? 'bg-info' : 'bg-warning' }}"></div>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold text-uppercase tracking-wider x-small text-white-50">Estado de la cuenta</h6>
                                <span class="fs-6 fw-bold {{ $tenant->service_type === 'subscription' ? 'text-info' : 'text-warning' }}">
                                    {{ $tenant->service_type === 'subscription' ? 'Suscripción Pendiente' : 'Licencia por Renovar' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Contenido Principal -->
            <div class="col-lg-7 bg-white p-4 p-md-5 d-flex flex-column justify-content-center">
                <!-- Header Móvil -->
                <div class="d-lg-none text-center mb-5">
                    @if($tenant->logo)
                        <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->business_name }}" 
                             class="mb-3" style="max-height: 50px;">
                    @endif
                    <h3 class="fw-bold text-dark mb-1">{{ $tenant->business_name ?? 'Sistema de Ventas' }}</h3>
                    <div class="badge {{ $tenant->service_type === 'subscription' ? 'bg-info' : 'bg-danger' }} bg-opacity-10 {{ $tenant->service_type === 'subscription' ? 'text-info' : 'text-danger' }} rounded-pill px-3 py-2 small fw-semibold mt-2">
                        {{ $tenant->service_type === 'subscription' ? 'SUSCRIPCIÓN SUSPENDIDA' : 'ACCESO SUSPENDIDO' }}
                    </div>
                </div>

                <div class="content-body text-center text-lg-start">
                    <div class="mb-4 d-none d-lg-block">
                        <span class="badge bg-slate-100 text-slate-600 rounded-pill px-3 py-2 fw-semibold text-uppercase tracking-wider x-small border border-slate-200">
                            <i class="fas fa-info-circle me-1 text-primary"></i> 
                            {{ $tenant->service_type === 'subscription' ? 'Detalles de Suscripción' : 'Información de Licencia' }}
                        </span>
                    </div>

                    <h2 class="fw-bold text-slate-900 mb-4 h3">
                        {{ $tenant->service_type === 'subscription' ? 'Aviso de Renovación de Suscripción' : 'Notificación de pago pendiente' }}
                    </h2>
                    
                    <div class="description-text mb-5">
                        @if($tenant->service_type === 'subscription')
                            <p class="text-slate-600 mb-4 fs-6 leading-relaxed">
                                Le informamos que no hemos recibido la confirmación de pago correspondiente a su <strong>suscripción activa</strong>. El acceso a los módulos operativos se ha restringido temporalmente.
                            </p>
                            @if($tenant->next_payment_date)
                                <div class="p-3 bg-blue-50 rounded-3 border border-blue-100 d-inline-flex align-items-center shadow-sm">
                                    <div class="icon-square bg-primary text-white me-3 shadow-blue">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div>
                                        <div class="x-small text-blue-800 text-uppercase fw-bold mb-0">Próximo Ciclo</div>
                                        <div class="fw-bold text-blue-900 fs-5 line-height-1">{{ \Carbon\Carbon::parse($tenant->next_payment_date)->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <p class="text-slate-600 mb-4 fs-6 leading-relaxed">
                                El acceso a su plataforma ha sido suspendido debido a un saldo pendiente por <strong>mantenimiento de licencia</strong>.
                            </p>
                            @if($tenant->next_payment_date)
                                <div class="p-3 bg-orange-50 rounded-3 border border-orange-100 d-inline-flex align-items-center shadow-sm">
                                    <div class="icon-square bg-warning text-dark me-3 shadow-orange">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <div>
                                        <div class="x-small text-orange-800 text-uppercase fw-bold mb-0">Vencimiento Técnico</div>
                                        <div class="fw-bold text-orange-900 fs-5 line-height-1">{{ \Carbon\Carbon::parse($tenant->next_payment_date)->format('d/m/Y') }}</div>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="p-4 bg-slate-50 rounded-4 border border-slate-200 mb-5">
                        <div class="d-flex align-items-start">
                            <div class="icon-square bg-slate-200 text-slate-600 me-3">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="text-start">
                                <h6 class="fw-bold text-slate-900 mb-1">Protección de Datos Garantizada</h6>
                                <p class="mb-0 small text-slate-600 leading-normal">Su información permanece cifrada y resguardada. El servicio se reactivará automáticamente tras procesar su pago.</p>
                            </div>
                        </div>
                    </div>

                    <div class="actions d-flex flex-column flex-md-row gap-3">
                        <button type="button" 
                           class="btn btn-corporate btn-lg rounded-3 px-4 py-3 fw-semibold flex-grow-1 shadow-sm"
                           data-bs-toggle="modal" data-bs-target="#paymentNotificationModal">
                            <i class="fas fa-file-invoice-dollar me-2"></i> Notificar Pago Realizado
                        </button>
                        <a href="javascript:location.reload()" 
                           class="btn btn-outline-slate btn-lg rounded-3 px-4 py-3 fw-semibold flex-grow-1">
                            <i class="fas fa-sync-alt me-2"></i> Actualizar Estado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Notificación de Pago -->
<div class="modal fade" id="paymentNotificationModal" tabindex="-1" aria-labelledby="paymentNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-slate-50 border-bottom border-slate-200 p-4">
                <h5 class="modal-title fw-bold text-slate-900" id="paymentNotificationModalLabel">
                    <i class="fas fa-paper-plane text-primary me-2"></i> Notificar Pago
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentNotificationForm" action="{{ route('tenant.payment-notification.send') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <label for="client_email" class="form-label fw-semibold text-slate-700">Enviar copia a (Opcional)</label>
                        <input type="email" class="form-control bg-slate-50 border-slate-200 rounded-3" 
                               id="client_email" name="client_email" value="{{ old('client_email') }}" placeholder="ejemplo@contabilidad.com">
                        <div id="client_email_error" class="invalid-feedback"></div>
                        <small class="text-muted">Indique un correo si desea que su equipo de contabilidad u otra área reciba una copia de esta notificación.</small>
                    </div>

                    <div class="mb-4">
                        <label for="message" class="form-label fw-semibold text-slate-700">Mensaje (Opcional)</label>
                        <textarea class="form-control bg-slate-50 border-slate-200 rounded-3" id="message" name="message" rows="3" placeholder="Ej: Ya realicé el pago por transferencia bancaria..."></textarea>
                        <small class="text-muted">Puede indicarnos detalles adicionales sobre su pago.</small>
                    </div>

                    <div class="mb-2">
                        <label for="attachment" class="form-label fw-semibold text-slate-700">Adjuntar Comprobante (Imagen o PDF)</label>
                        <input class="form-control bg-slate-50 border-slate-200 rounded-3" type="file" id="attachment" name="attachment" accept="image/*,.pdf">
                        <small class="text-muted">Puedes subir una foto, captura de pantalla o un archivo PDF (Máx. 5MB).</small>
                    </div>
                </div>
                <div class="modal-footer bg-slate-50 border-top border-slate-200 p-4">
                    <button type="button" class="btn btn-outline-slate px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" id="submitBtn" class="btn btn-corporate px-4">
                        <span id="btnText"><i class="fas fa-send me-2"></i> Enviar Notificación</span>
                        <span id="btnLoader" class="d-none">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Enviando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function() {
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('paymentNotificationForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoader = document.getElementById('btnLoader');
            const modalEl = document.getElementById('paymentNotificationModal');

            if (!form) return;

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // 1. Mostrar Alerta de Progreso con SweetAlert2
                if (window.Swal) {
                    window.Swal.fire({
                        title: 'Enviando notificación...',
                        text: 'Por favor, espere mientras procesamos su comprobante.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            window.Swal.showLoading();
                        }
                    });
                }

                // 2. Bloquear botón e interfaz
                submitBtn.disabled = true;
                btnText.classList.add('d-none');
                btnLoader.classList.remove('d-none');

                // Ocultar modal para evitar clics accidentales mientras se muestra el SWAL
                if (modalEl && window.bootstrap) {
                    const modalInstance = window.bootstrap.Modal.getInstance(modalEl);
                    if (modalInstance) modalInstance.hide();
                }

                // Limpiar errores previos
                document.getElementById('client_email').classList.remove('is-invalid');

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const text = await response.text();
                    console.log('Respuesta cruda del servidor:', text);
                    try {
                        const data = JSON.parse(text);
                        return { status: response.status, data };
                    } catch (e) {
                        console.error('La respuesta no es JSON válido:', text);
                        // Intentar extraer el JSON si hay texto antes o después
                        const jsonMatch = text.match(/\{.*\}/s);
                        if (jsonMatch) {
                            try {
                                const data = JSON.parse(jsonMatch[0]);
                                return { status: response.status, data };
                            } catch (e2) {}
                        }
                        throw new Error('El servidor devolvió una respuesta inesperada.');
                    }
                })
                .then(({ status, data }) => {
                    // Cerrar el loading de SweetAlert2 antes de mostrar el resultado
                    if (window.Swal) window.Swal.close();

                    if (status === 200 && data.success) {
                        // Éxito
                        if (window.Swal) {
                            window.Swal.fire({
                                icon: 'success',
                                title: '¡Enviado!',
                                text: data.message,
                                timer: 5000,
                                showConfirmButton: true,
                                confirmButtonText: 'Entendido',
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            alert(data.message);
                        }
                        form.reset();
                    } else {
                        // Error de validación o del servidor
                        let errorMsg = data.message || 'Error al enviar la notificación.';
                        
                        if (status === 422 && data.errors) {
                            if (data.errors.client_email) {
                                const emailInput = document.getElementById('client_email');
                                const emailError = document.getElementById('client_email_error');
                                emailInput.classList.add('is-invalid');
                                emailError.textContent = data.errors.client_email[0];
                                errorMsg = 'Por favor, verifique el correo electrónico ingresado.';
                            }
                        }

                        if (window.Swal) {
                            window.Swal.fire({
                                icon: 'error',
                                title: 'No se pudo enviar',
                                text: errorMsg,
                                confirmButtonColor: '#4e73df',
                                // Si es error de validación, reabrimos el modal al cerrar el alert
                                willClose: () => {
                                    if (status === 422) showModal();
                                }
                            });
                        } else {
                            alert('Error: ' + errorMsg);
                        }
                    }
                })
                .catch(error => {
                    if (window.Swal) {
                        window.Swal.fire({
                            icon: 'error',
                            title: 'Error de Red',
                            text: 'No se pudo conectar con el servidor. Verifique su conexión.',
                            confirmButtonColor: '#4e73df',
                            willClose: () => showModal()
                        });
                    }
                })
                .finally(() => {
                    // Restaurar botón
                    submitBtn.disabled = false;
                    btnText.classList.remove('d-none');
                    btnLoader.classList.add('d-none');
                });
            });
        });
    })();
</script>

<style>
    :root {
        --corporate-blue: #0f172a;
        --corporate-blue-light: #1e293b;
        --primary-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        --danger-soft: #fef2f2;
        --blue-soft: #f0f9ff;
        --blue-dark: #075985;
        --border-color: #e2e8f0;
    }

    body {
        background-color: #f1f5f9;
        font-family: 'Inter', sans-serif;
        color: #334155;
    }

    .main-card {
        max-width: 1050px;
        width: 100%;
        min-height: 650px;
        background: white;
        border: 1px solid var(--border-color) !important;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .main-card > .row {
        flex: 1;
        width: 100%;
        margin: 0;
        display: flex;
        align-items: stretch;
    }

    .bg-gradient-corporate {
        background: linear-gradient(180deg, var(--corporate-blue) 0%, var(--corporate-blue-light) 100%);
        display: flex;
        flex-direction: column;
    }

    .bg-pattern {
        background-image: radial-gradient(#ffffff10 1px, transparent 1px);
        background-size: 20px 20px;
    }

    .backdrop-blur {
        backdrop-filter: blur(10px);
    }

    .fw-extrabold { font-weight: 800; }
    .x-small { font-size: 0.75rem; }
    .tracking-tight { letter-spacing: -0.02em; }
    .tracking-wider { letter-spacing: 0.05em; }
    .leading-relaxed { line-height: 1.6; }

    .line-height-1 { line-height: 1; }

    .logo-container {
        border: 1px solid #e2e8f0;
        transition: transform 0.2s;
    }

    .logo-container:hover {
        transform: scale(1.02);
    }

    .logo-img {
        max-height: 45px;
        width: auto;
        filter: grayscale(0.2);
    }

    .logo-img:hover {
        filter: grayscale(0);
    }

    .logo-placeholder {
        width: 64px;
        height: 64px;
        backdrop-filter: blur(4px);
    }

    .pulse-container {
        position: relative;
        width: 10px;
        height: 10px;
    }

    .pulse-dot {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        position: relative;
    }

    .pulse-dot::after {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        border-radius: 50%;
        background: inherit;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(3); opacity: 0; }
    }

    .icon-square {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .btn-corporate {
        background: #2563eb;
        color: white;
        border: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
    }

    .btn-corporate:hover {
        background: #1d4ed8;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
    }

    .btn-corporate:active {
        transform: translateY(0);
    }

    .btn-outline-slate {
        border: 1px solid #e2e8f0;
        color: #475569;
        background: white;
        transition: all 0.2s;
    }

    .btn-outline-slate:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #0f172a;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .bg-slate-50 { background-color: #f8fafc; }
    .bg-slate-100 { background-color: #f1f5f9; }
    .text-slate-500 { color: #64748b; }
    .text-slate-600 { color: #475569; }
    .text-slate-900 { color: #0f172a; }
    .border-slate-200 { border-color: #e2e8f0; }

    .bg-blue-50 { background-color: #eff6ff; }
    .text-blue-600 { color: #2563eb; }
    .text-blue-800 { color: #1e40af; }
    .text-blue-900 { color: #1e3a8a; }
    .border-blue-100 { border-color: #dbeafe; }

    .bg-orange-50 { background-color: #fff7ed; }
    .text-orange-800 { color: #9a3412; }
    .text-orange-900 { color: #7c2d12; }
    .border-orange-100 { border-color: #ffedd5; }

    .shadow-blue { box-shadow: 0 4px 12px -2px rgba(37, 99, 235, 0.3); }
    .shadow-orange { box-shadow: 0 4px 12px -2px rgba(245, 158, 11, 0.3); }

    .service-subscription .bg-gradient-corporate {
        background: linear-gradient(180deg, #0f172a 0%, #1e3a8a 100%);
    }

    .service-license .bg-gradient-corporate {
        background: linear-gradient(180deg, #0f172a 0%, #431407 100%);
    }

    .shadow-2xl {
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15);
    }

    @media (max-width: 991.98px) {
        .main-card {
            min-height: auto;
            border-radius: 1.5rem !important;
            margin: 1rem 0;
        }
        
        .page-wrapper {
            padding-top: 2rem !important;
            padding-bottom: 2rem !important;
            align-items: flex-start !important;
        }

        .content-body {
            padding: 1rem 0.5rem;
        }

        .actions {
            gap: 1rem !important;
        }

        .btn-lg {
            padding: 1rem !important;
            font-size: 1rem;
        }

        .description-text p {
            font-size: 0.95rem !important;
        }
    }
</style>
@endsection
