@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm border-0 print-card">
            <div class="card-body p-5">
                <!-- Header -->
                <div class="text-center mb-5">
                    <h2 class="fw-bold text-uppercase mb-1">{{ tenant('id') }}</h2>
                    <p class="text-muted mb-0">Comprobante de Ingreso de Mercancía</p>
                </div>

                <!-- Info Compra -->
                <div class="row mb-4">
                    <div class="col-6">
                        <small class="text-uppercase text-muted fw-bold">Proveedor</small>
                        <h5 class="mb-0">{{ $purchase->supplier->name }}</h5>
                        <p class="text-muted small mb-0">{{ $purchase->supplier->company }}</p>
                        <p class="text-muted small">RUC/DNI: {{ $purchase->supplier->contact_name }}</p>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-uppercase text-muted fw-bold">Detalles</small>
                        <h5 class="mb-0">#{{ str_pad($purchase->nro_compra, 6, '0', STR_PAD_LEFT) }}</h5>
                        <p class="text-muted small mb-0">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</p>
                        <p class="text-muted small">Ref: {{ $purchase->voucher }}</p>
                    </div>
                </div>

                <!-- Tabla Productos -->
                <div class="table-responsive mb-4">
                    <table class="table table-borderless">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-uppercase small text-muted">Producto</th>
                                <th class="text-end text-uppercase small text-muted">Cant.</th>
                                <th class="text-end text-uppercase small text-muted">Precio U.</th>
                                <th class="text-end text-uppercase small text-muted">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-3">
                                    <span class="fw-bold d-block">{{ $purchase->product->name }}</span>
                                    <small class="text-muted">{{ $purchase->product->code }}</small>
                                </td>
                                <td class="text-end py-3">{{ $purchase->quantity }}</td>
                                <td class="text-end py-3">$ {{ number_format($purchase->price, 2) }}</td>
                                <td class="text-end py-3 fw-bold">$ {{ number_format($purchase->price * $purchase->quantity, 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <td colspan="3" class="text-end pt-3 text-uppercase small fw-bold">Total Pagado</td>
                                <td class="text-end pt-3 fw-bold fs-5">$ {{ number_format($purchase->price * $purchase->quantity, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Footer -->
                <div class="mt-5 pt-4 border-top text-center text-muted small">
                    <p class="mb-1">Registrado por: {{ $purchase->user->name ?? 'Sistema' }}</p>
                    <p class="mb-0">Fecha de impresión: {{ date('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .print-card, .print-card * {
            visibility: visible;
        }
        .print-card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            border: none !important;
            box-shadow: none !important;
        }
        .app-topbar, .app-sidebar, .btn {
            display: none !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.print();
    });
</script>
@endsection
