@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Procesar Devolución</h1>
            <p class="text-muted">Venta #{{ $sale->nro_venta }} - Cliente: {{ $sale->client->name ?? 'Venta Rápida' }}</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <form action="{{ route('credit-notes.store') }}" method="POST">
        @csrf
        <input type="hidden" name="sale_id" value="{{ $sale->id }}">

        <div class="row" x-data="{ reason: '' }">
            <div class="col-lg-8">
                <div class="card border-0 shadow-soft rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0">Productos de la Venta</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4">Producto</th>
                                        <th class="text-center">Cant. Original</th>
                                        <th class="text-center">Cant. a Devolver</th>
                                        <th class="text-end px-4">Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->items as $index => $item)
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-bold text-dark">{{ $item->product->name }}</div>
                                            <div class="text-muted small">Cod: {{ $item->product->code }}</div>
                                            <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark rounded-pill">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-center" style="width: 150px;">
                                            <input type="number" 
                                                   name="items[{{ $index }}][quantity]" 
                                                   class="form-control form-control-sm text-center rounded-pill" 
                                                   value="0" 
                                                   min="0" 
                                                   max="{{ $item->quantity }}"
                                                   onchange="this.value > {{ $item->quantity }} ? this.value = {{ $item->quantity }} : null">
                                        </td>
                                        <td class="text-end px-4 fw-bold">
                                            ${{ number_format($item->price, 2) }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-soft rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Detalles de la Nota</h5>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Número de Nota</label>
                            <input type="text" class="form-control rounded-3 bg-light" value="{{ $nextNumber }}" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label small fw-bold">Motivo de la Devolución</label>
                            <select name="reason" id="reason" class="form-select rounded-3" required x-model="reason">
                                <option value="" disabled selected>Selecciona un motivo</option>
                                <option value="Producto Defectuoso">Producto Defectuoso</option>
                                <option value="Error en Pedido">Error en Pedido</option>
                                <option value="Cambio de Opinión">Cambio de Opinión</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="mb-3" x-show="reason === 'Otro'" x-transition>
                            <label for="other_reason" class="form-label small fw-bold text-danger">Por favor, especifica el motivo</label>
                            <textarea name="other_reason" id="other_reason" class="form-control rounded-3 border-danger" rows="3" placeholder="Justificación obligatoria..."></textarea>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="restock" name="restock" checked>
                                <label class="form-check-label" for="restock">Reingresar productos al stock</label>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">
                                <i class="fas fa-check-circle me-2"></i> Confirmar Devolución
                            </button>
                            <p class="text-center text-muted x-small mt-2">
                                Esta acción generará una nota de crédito y ajustará el inventario.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
