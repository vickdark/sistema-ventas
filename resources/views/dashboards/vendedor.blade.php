@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0 text-gray-800">Bienvenido, {{ auth()->user()->name }}</h1>
            <p class="text-muted">Panel de Vendedor - {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    <!-- Mega Acceso Rápido principal -->
    <div class="row g-4 mb-5">
        <div class="col-md-8">
            <a href="{{ route('sales.create') }}" class="text-decoration-none action-card-wrapper">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-gradient-primary h-100 transform-hover">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between position-relative">
                        <div class="z-1">
                            <h2 class="fw-bold text-white mb-1">NUEVA VENTA POS</h2>
                            <p class="text-white text-opacity-75 mb-0">Iniciar proceso de facturación rápida</p>
                        </div>
                        <i class="fa-solid fa-cart-shopping fa-4x text-white opacity-25 z-1"></i>
                        <div class="decoration-circle-1"></div>
                        <div class="decoration-circle-2"></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('clients.create') }}" class="text-decoration-none action-card-wrapper">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-gradient-success h-100 transform-hover">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between position-relative">
                        <div class="z-1">
                            <h3 class="fw-bold text-white mb-1">Nuevo Cliente</h3>
                            <p class="text-white text-opacity-75 mb-0">Registrar</p>
                        </div>
                        <i class="fa-solid fa-user-plus fa-3x text-white opacity-50 z-1"></i>
                        <div class="decoration-circle-3"></div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Mis Ventas Recientes -->
        <div class="col-lg-8">
            <div class="card shadow-sm rounded-4 border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Mis Ventas Recientes</h6>
                    <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">Ver todas</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Folio</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Hora</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(\App\Models\Tenant\Sale::where('user_id', auth()->id())->latest()->take(5)->get() as $sale)
                                <tr>
                                    <td class="ps-4 fw-bold">#{{ $sale->nro_venta }}</td>
                                    <td>{{ optional($sale->client)->name ?? 'Consumidor Final' }}</td>
                                    <td class="fw-bold text-success">${{ number_format($sale->total_paid, 2) }}</td>
                                    <td>
                                        @if($sale->payment_status == 'PAID')
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Pagado</span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $sale->created_at->format('H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-receipt fa-2x mb-2 d-block opacity-25"></i>
                                        No has realizado ventas hoy
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas Rápidas -->
        <div class="col-lg-4">
            <div class="row g-4">
                <!-- Ventas Hoy -->
                <div class="col-12">
                    <div class="card shadow-sm rounded-4 border-start border-primary" style="border-width: 0 0 0 4px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Ventas Hoy</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ \App\Models\Tenant\Sale::where('user_id', auth()->id())->whereDate('created_at', today())->count() }}
                                    </div>
                                </div>
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                                    <i class="fa-solid fa-bag-shopping fs-4 text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Hoy -->
                <div class="col-12">
                    <div class="card shadow-sm rounded-4 border-start border-success" style="border-width: 0 0 0 4px;">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Total Vendido Hoy</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ${{ number_format(\App\Models\Tenant\Sale::where('user_id', auth()->id())->whereDate('created_at', today())->sum('total_paid'), 2) }}
                                    </div>
                                </div>
                                <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                                    <i class="fa-solid fa-sack-dollar fs-4 text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Opciones -->
                <div class="col-12">
                     <div class="card shadow-sm rounded-4 border-0">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h6 class="m-0 font-weight-bold text-secondary">Herramientas</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary text-start">
                                    <i class="fa-solid fa-users me-2"></i> Directorio de Clientes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-xs { font-size: .75rem; }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
    }
    
    .transform-hover {
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }
    
    .transform-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    
    .z-1 { z-index: 1; }
    
    /* Decorative elements */
    .decoration-circle-1 {
        position: absolute;
        width: 150px;
        height: 150px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        top: -50px;
        right: -30px;
    }
    
    .decoration-circle-2 {
        position: absolute;
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        bottom: -20px;
        left: 20%;
    }
     .decoration-circle-3 {
        position: absolute;
        width: 100px;
        height: 100px;
        background: rgba(0,0,0,0.05);
        border-radius: 50%;
        bottom: -40px;
        right: 10%;
    }
</style>
@endsection
