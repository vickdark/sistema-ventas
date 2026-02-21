@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <a href="{{ route('quotes.index') }}" class="btn btn-sm btn-light rounded-pill px-3 mb-2 shadow-sm border-0">
                <i class="fas fa-arrow-left me-1"></i> Volver a la lista
            </a>
            <h1 class="h3 mb-0 text-gray-800">Cotización #{{ $quote->nro_cotizacion }}</h1>
        </div>
        <div class="col-auto">
            @if($quote->status === 'PENDIENTE')
                <div class="d-flex gap-2">
                    <a href="{{ route('quotes.edit', $quote->id) }}" class="btn btn-outline-primary rounded-pill px-4 shadow-sm border-0">
                        <i class="fas fa-edit me-2"></i> Editar
                    </a>
                    <form action="{{ route('quotes.convert', $quote->id) }}" method="POST" onsubmit="return confirm('¿Convertir esta cotización en una venta? Esto afectará el stock.')">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm border-0 fw-bold">
                            <i class="fas fa-shopping-cart me-2"></i> Convertir a Venta
                        </button>
                    </form>
                    
                    <button class="btn btn-outline-dark rounded-pill px-4 shadow-sm border-0" onclick="window.print()">
                        <i class="fas fa-print me-2"></i> Imprimir
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div id="printable-area" class="card border-0 shadow-soft rounded-4 overflow-hidden mb-4">
                <div class="card-body p-5">
                    
                    <!-- Encabezado de Impresión (Solo visible al imprimir) -->
                    <div class="print-header mb-5 d-none d-print-block">
                        <div class="row align-items-center">
                            <div class="col-6">
                                @if(tenant('logo'))
                                    <img src="{{ asset('storage/' . tenant('logo')) }}" alt="Logo" class="img-fluid mb-3" style="max-height: 80px;">
                                @endif
                                <h2 class="fw-bold text-uppercase mb-1">{{ tenant('business_name') ?? tenant('id') }}</h2>
                                @if(tenant('tax_id'))
                                    <p class="mb-0 text-muted">NIT/RUC: {{ tenant('tax_id') }}</p>
                                @endif
                                @if(tenant('address'))
                                    <p class="mb-0 text-muted">{{ tenant('address') }}</p>
                                @endif
                                @if(tenant('phone'))
                                    <p class="mb-0 text-muted">Tel: {{ tenant('phone') }}</p>
                                @endif
                                @if(tenant('email'))
                                    <p class="mb-0 text-muted">{{ tenant('email') }}</p>
                                @endif
                            </div>
                            <div class="col-6 text-end">
                                <h1 class="fw-bold text-primary display-6 mb-2">COTIZACIÓN</h1>
                                <h3 class="fw-bold text-dark mb-1">#{{ $quote->nro_cotizacion }}</h3>
                                <p class="mb-0 text-muted">Fecha: {{ now()->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <hr class="my-4 border-2 border-primary opacity-100">
                    </div>

                    <!-- Cabecera de Documento -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h5 class="fw-bold text-primary mb-1">DETALLES DEL CLIENTE</h5>
                            <p class="mb-0 fw-bold fs-4">{{ $quote->client ? $quote->client->name : 'Consumidor Final' }}</p>
                            <p class="text-muted">{{ $quote->client ? $quote->client->email : '' }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5 class="fw-bold text-primary mb-1">INFORMACIÓN</h5>
                            <ul class="list-unstyled">
                                <li><strong>Fecha:</strong> {{ $quote->created_at->format('d/m/Y') }}</li>
                                <li><strong>Vencimiento:</strong> <span class="text-danger">{{ $quote->expiration_date ? $quote->expiration_date->format('d/m/Y') : 'N/A' }}</span></li>
                                <li><strong>Estado:</strong> 
                                    <span class="badge bg-{{ $quote->status === 'PENDIENTE' ? 'warning' : 'success' }}">
                                        {{ $quote->status }}
                                    </span>
                                </li>
                                <li><strong>Realizado por:</strong> {{ $quote->user->name }}</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Tabla de Productos -->
                    <div class="table-responsive mb-5">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light border-0">
                                <tr>
                                    <th class="py-3 px-4 border-0">Producto</th>
                                    <th class="py-3 border-0 text-center">Cantidad</th>
                                    <th class="py-3 border-0 text-end">Precio Unit.</th>
                                    <th class="py-3 px-4 border-0 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quote->items as $item)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="fw-bold">{{ $item->product->name }}</div>
                                            <div class="text-muted small">#{{ $item->product->code }}</div>
                                        </td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                        <td class="px-4 text-end fw-bold">${{ number_format($item->subtotal, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="border-top-0">
                                <tr>
                                    <td colspan="3" class="text-end py-3 fs-5 fw-bold border-0">TOTAL</td>
                                    <td class="text-end py-3 fs-3 fw-extrabold text-primary border-0 px-4">${{ number_format($quote->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($quote->notes)
                    <div class="bg-light p-4 rounded-4">
                        <h6 class="fw-bold mb-2">NOTAS ADICIONALES:</h6>
                        <p class="mb-0 text-muted italic">{{ $quote->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    /* Ocultar todo el contenido por defecto */
    body * {
        visibility: hidden;
    }

    /* Mostrar solo el área de impresión y sus hijos */
    #printable-area, #printable-area * {
        visibility: visible;
    }

    /* Posicionar el área de impresión en la parte superior absoluta */
    #printable-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
        background-color: white !important;
    }

    /* Asegurar que el cuerpo tenga fondo blanco */
    body {
        background-color: white !important;
        margin: 0 !important;
        padding: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Ocultar botones y elementos específicos dentro del área imprimible si los hubiera */
    .no-print, .btn {
        display: none !important;
    }

    /* Asegurar que el card-body ocupe todo el ancho */
    .card-body {
        padding: 20px !important;
        width: 100% !important;
    }

    /* Mostrar encabezado de impresión */
    .d-print-block {
        display: block !important;
        visibility: visible !important;
    }

    /* Ajustes de tabla para impresión */
    .table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    
    .table th, .table td {
        border: 1px solid #ddd !important;
        padding: 8px !important;
    }
}
</style>
@endsection
