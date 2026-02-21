@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div id="supplier-payments-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Cuentas por Pagar</h1>
            <p class="text-muted small">Control de deudas y abonos a proveedores.</p>
        </div>
    </div>

    <!-- Resumen de Deuda (Opcional, se puede expandir luego) -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-soft h-100 py-2 border-start border-danger border-4 rounded-4">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Pendiente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPendingLabel">$0.00</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>
@endsection
