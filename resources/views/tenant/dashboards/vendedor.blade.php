@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-auto mb-3 mb-md-0">
            @if(tenant('logo'))
                <div class="bg-white p-2 rounded-4 shadow-sm d-inline-block border">
                    <img src="{{ asset('storage/' . tenant('logo')) }}" alt="Logo" class="img-fluid" style="max-height: 80px; width: auto; object-fit: contain;">
                </div>
            @else
                <div class="bg-primary bg-opacity-10 p-4 rounded-4 shadow-sm d-inline-block">
                    <i class="fa-solid fa-rocket fa-2x text-primary" style="font-size: 2.5rem;"></i>
                </div>
            @endif
        </div>
        <div class="col">
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Bienvenido, {{ auth()->user()->name }}</h1>
            <p class="text-muted mb-0 d-flex align-items-center flex-wrap gap-2">
                <span class="badge bg-success bg-opacity-10 text-success px-3 rounded-pill">Panel de Vendedor</span>
                <span class="opacity-50">|</span>
                <i class="fa-solid fa-building opacity-50"></i>
                <span class="fw-medium">{{ tenant('business_name') ?? tenant('id') }}</span>
                <span class="opacity-50">|</span>
                <i class="fa-solid fa-calendar-day opacity-50"></i>
                <span>{{ now()->format('d/m/Y') }}</span>
            </p>
        </div>
    </div>

    <!-- Mega Acceso Rápido principal -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
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
        <div class="col-md-3">
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
        <div class="col-md-3">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-dark h-100 transform-hover cursor-pointer" onclick="window.downloadOfflineMode()">
                <div class="card-body p-4 d-flex align-items-center justify-content-between position-relative">
                    <div class="z-1">
                        <h5 class="fw-bold text-white mb-1">MODO OFFLINE</h5>
                        <p class="text-white text-opacity-75 mb-0 small">Descargar recursos</p>
                    </div>
                    <i class="fa-solid fa-cloud-arrow-down fa-3x text-white opacity-50 z-1"></i>
                    <div class="decoration-circle-1"></div>
                </div>
            </div>
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


@endsection
