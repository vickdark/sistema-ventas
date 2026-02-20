@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <a href="{{ route('stock-transfers.index') }}" class="btn btn-sm btn-light rounded-pill px-3 mb-2 shadow-sm border-0">
                <i class="fas fa-arrow-left me-1"></i> Volver a la lista
            </a>
            <h1 class="h3 mb-0 text-gray-800">Traslado #{{ $transfer->nro_traslado }}</h1>
        </div>
        <div class="col-auto">
            @if($transfer->status === 'ENVIADO' && session('active_branch_id') == $transfer->destination_branch_id)
                <form action="{{ route('stock-transfers.receive', $transfer->id) }}" method="POST" onsubmit="return confirm('¿Confirmar la recepción de estos productos? El inventario se actualizará en esta sucursal.')">
                    @csrf
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm border-0 fw-bold">
                        <i class="fas fa-check-double me-2"></i> RECIBIR MERCADERÍA
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Ruta de Traslado</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-store text-primary"></i>
                        </div>
                        <div>
                            <label class="text-muted small d-block">Origen</label>
                            <span class="fw-bold">{{ $transfer->originBranch->name }}</span>
                        </div>
                    </div>
                    
                    <div class="text-center my-2 position-relative py-3">
                        <div class="border-start border-primary border-2 position-absolute start-50 translate-middle-x" style="height: 100%; top: 0; opacity: 0.2; z-index: 1;"></div>
                        <i class="fas fa-arrow-down text-primary position-relative bg-white" style="z-index: 2;"></i>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-location-dot text-success"></i>
                        </div>
                        <div>
                            <label class="text-muted small d-block">Destino</label>
                            <span class="fw-bold">{{ $transfer->destinationBranch->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <label class="text-muted small d-block">Estado</label>
                            @php
                                $color = 'warning';
                                if ($transfer->status === 'RECIBIDO') $color = 'success';
                                if ($transfer->status === 'ENVIADO') $color = 'primary';
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ $transfer->status }}</span>
                        </li>
                        <li class="mb-3">
                            <label class="text-muted small d-block">Fecha Envío</label>
                            <span>{{ $transfer->shipped_at ? $transfer->shipped_at->format('d/m/Y H:i') : '-' }}</span>
                        </li>
                        <li class="mb-3">
                            <label class="text-muted small d-block">Fecha Recepción</label>
                            <span>{{ $transfer->received_at ? $transfer->received_at->format('d/m/Y H:i') : 'Pendiente' }}</span>
                        </li>
                        <li>
                            <label class="text-muted small d-block">Usuario Responsable</label>
                            <span>{{ $transfer->user->name }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Productos en Tránsito</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light border-0">
                                <tr>
                                    <th class="py-3 px-3 border-0">Producto</th>
                                    <th class="py-3 border-0 text-center">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transfer->items as $item)
                                    <tr>
                                        <td class="px-3 py-3">
                                            <div class="fw-bold text-dark">{{ $item->product->name }}</div>
                                            <div class="text-muted small">#{{ $item->product->code }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border px-3 py-2 fs-6">{{ $item->quantity }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($transfer->notes)
                    <div class="mt-4 p-4 bg-light rounded-4">
                        <h6 class="fw-bold mb-2">OBSERVACIONES:</h6>
                        <p class="mb-0 text-muted italic">{{ $transfer->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
