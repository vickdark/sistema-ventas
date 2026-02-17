@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('central.payment-notifications.index') }}" class="btn btn-link text-decoration-none p-0 text-muted">
            <i class="fas fa-arrow-left me-1"></i> Volver a la bandeja
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark">Detalle de Notificación</h5>
                        @if($notification->status === 'pending')
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Pendiente de Revisión</span>
                        @elseif($notification->status === 'reviewed')
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Revisado</span>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="form-label text-muted x-small text-uppercase fw-bold">Mensaje del Cliente</label>
                        <div class="p-3 bg-light rounded-3 text-dark">
                            {{ $notification->message ?: 'Sin mensaje adjunto.' }}
                        </div>
                    </div>

                    @if($notification->attachment_path)
                        <div>
                            <label class="form-label text-muted x-small text-uppercase fw-bold">Comprobante Adjunto</label>
                            <div class="border rounded-4 p-3 d-flex align-items-center justify-content-between bg-white shadow-sm">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-info bg-opacity-10 text-info rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fa-solid fa-file-invoice-dollar fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Comprobante de Pago</div>
                                        <div class="x-small text-muted">{{ strtoupper(pathinfo($notification->attachment_path, PATHINFO_EXTENSION)) }}</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-primary rounded-pill px-4" 
                                        data-preview-url="{{ asset('storage/' . $notification->attachment_path) }}"
                                        data-is-pdf="{{ str_ends_with(strtolower($notification->attachment_path), '.pdf') ? 'true' : 'false' }}"
                                        onclick="window.previewAttachment(this.dataset.previewUrl, this.dataset.isPdf === 'true')">
                                    <i class="fa-solid fa-eye me-2"></i> Ver Comprobante
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-header bg-white border-bottom-0 p-4">
                    <h5 class="fw-bold mb-0 text-dark">Información de la Empresa</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <div class="mb-3">
                        <div class="text-muted x-small text-uppercase fw-bold mb-1">Nombre Comercial</div>
                        <div class="fw-bold text-dark">{{ $notification->tenant->business_name ?? 'No disponible' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted x-small text-uppercase fw-bold mb-1">ID del Inquilino</div>
                        <div class="text-dark font-monospace small">{{ $notification->tenant_id }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted x-small text-uppercase fw-bold mb-1">Email de la Empresa</div>
                        <div class="text-dark">
                            <i class="fa-solid fa-envelope me-1 text-muted small"></i>
                            {{ $notification->tenant->email ?? ($notification->tenant->data['email'] ?? 'No proporcionado') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="text-muted x-small text-uppercase fw-bold mb-1">Teléfono de la Empresa</div>
                        <div class="text-dark">
                            <i class="fa-solid fa-phone me-1 text-muted small"></i>
                            {{ $notification->tenant->phone ?? ($notification->tenant->data['phone'] ?? 'No proporcionado') }}
                        </div>
                    </div>
                    <div class="mb-0">
                        <div class="text-muted x-small text-uppercase fw-bold mb-1">Fecha de Notificación</div>
                        <div class="text-dark">{{ $notification->created_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                </div>
                @if($notification->status === 'pending')
                    <div class="card-footer bg-white border-top-0 p-4">
                        <form action="{{ route('central.payment-notifications.review', $notification) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 rounded-pill py-2">
                                <i class="fa-solid fa-check me-2"></i> Marcar como Revisado
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3">Acciones Rápidas</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('central.tenants.show', $notification->tenant_id) }}" class="btn btn-outline-primary rounded-pill text-start">
                            <i class="fa-solid fa-building me-2"></i> Ver Ficha de Empresa
                        </a>
                        <button type="button" class="btn btn-outline-danger rounded-pill text-start" onclick="confirmDelete()">
                            <i class="fa-solid fa-trash-can me-2"></i> Eliminar Notificación
                        </button>
                        <form id="delete-form-detail" action="{{ route('central.payment-notifications.destroy', $notification) }}" method="POST" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.previewAttachment = function(url, isPdf) {
        let content = '';
        if (isPdf) {
            content = `<iframe src="${url}" style="width: 100%; height: 600px; border: none;" class="rounded-3"></iframe>`;
        } else {
            content = `<img src="${url}" class="img-fluid rounded-3 shadow-sm" style="max-height: 600px; width: auto; display: block; margin: 0 auto;">`;
        }

        window.Swal.fire({
            title: '<i class="fa-solid fa-image me-2 text-primary"></i>Comprobante de Pago',
            html: content,
            width: isPdf ? '800px' : 'auto',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                popup: 'rounded-4 border-0 shadow-lg p-3'
            }
        });
    };

    async function confirmDelete() {
        const confirmed = await window.Notify.confirm({
            title: '¿Eliminar notificación?',
            text: 'Esta acción no se puede deshacer.',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (confirmed) {
            document.getElementById('delete-form-detail').submit();
        }
    }
</script>
@endsection
