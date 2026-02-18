@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-auto mb-3 mb-md-0">
            @if(tenant('logo'))
                <div class="bg-white p-2 rounded-4 shadow-sm d-inline-block border">
                    <img src="{{ asset('storage/' . tenant('logo')) }}" alt="Logo" class="img-fluid" style="max-height: 85px; width: auto; object-fit: contain;">
                </div>
            @else
                <div class="bg-primary bg-opacity-10 p-4 rounded-4 shadow-sm d-inline-block">
                    <i class="fa-solid fa-rocket fa-3x text-primary"></i>
                </div>
            @endif
        </div>
        <div class="col">
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Dashboard Administrativo</h1>
            <p class="text-muted mb-0 d-flex align-items-center gap-2">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 rounded-pill">Admin</span>
                <span class="opacity-50">|</span>
                <i class="fa-solid fa-building opacity-50"></i>
                <span class="fw-medium">{{ tenant('business_name') ?? tenant('id') }}</span>
            </p>
        </div>
    </div>

    @if($showSubscriptionStatus ?? false)
    <div class="row g-3 mb-3">
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body py-2 px-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bg-info bg-opacity-10 p-2 rounded-circle">
                            <i class="fa-solid fa-calendar-check text-info"></i>
                        </div>
                        <div>
                            <div class="small text-muted text-uppercase fw-semibold">{{ $isMaintenance ? 'Mantenimiento' : 'Suscripción' }}</div>
                            <div class="small mb-0">
                                @if($nextPaymentDate)
                                    Próximo pago: <span class="fw-bold">{{ $formattedNextPaymentDate }}</span>
                                @else
                                    Sin fecha
                                @endif
                            </div>
                        </div>
                        @if($auxLine)
                        <div class="small text-warning mt-1">{{ $auxLine }}</div>
                        @endif
                    </div>
                    <div>
                        <span class="badge rounded-pill px-2 py-1 {{ $badgeClass }}">{{ $label }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Mega Acceso Rápido (Hiper visibles) -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <a href="{{ route('sales.create') }}" class="text-decoration-none action-card-wrapper">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-gradient-primary h-100 transform-hover">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between position-relative">
                        <div class="z-1">
                            <h2 class="fw-bold text-white mb-1">NUEVA VENTA POS</h2>
                            <p class="text-white text-opacity-75 mb-0">Factura productos en segundos</p>
                        </div>
                        <i class="fa-solid fa-cart-shopping fa-4x text-white opacity-25 z-1"></i>
                        <div class="decoration-circle-1"></div>
                        <div class="decoration-circle-2"></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="{{ route('abonos.create') }}" class="text-decoration-none action-card-wrapper">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-gradient-success h-100 transform-hover">
                    <div class="card-body p-4 d-flex align-items-center justify-content-between position-relative">
                        <div class="z-1">
                            <h2 class="fw-bold text-white mb-1">REGISTRAR ABONO</h2>
                            <p class="text-white text-opacity-75 mb-0">Gestión rápida de cobros de cartera</p>
                        </div>
                        <i class="fa-solid fa-money-bill-transfer fa-4x text-white opacity-50 z-1"></i>
                        <div class="decoration-circle-1"></div>
                        <div class="decoration-circle-3"></div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Usuarios Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-primary" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Usuarios Registrados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Tenant\Usuario::count() }}
                            </div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-users fs-4 text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('usuarios.index') }}" class="text-xs text-decoration-none">
                            Ver todos los usuarios <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-success" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Roles Definidos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Tenant\Role::count() }}
                            </div>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-user-shield fs-4 text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('roles.index') }}" class="text-xs text-decoration-none text-success">
                            Gestionar seguridad <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permisos Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-info" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Permisos Activos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Tenant\Permission::count() }}
                            </div>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-key fs-4 text-info"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-xs text-muted">Sincronizados con rutas</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Último Acceso Card -->
        <div class="col-xl-3 col-md-6">
            <div class="card shadow-sm rounded-4 h-100 border-start border-warning" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tu Último Acceso</div>
                            <div class="h6 mb-0 font-weight-bold text-gray-800">
                                {{ now()->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-clock-rotate-left fs-4 text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-xs text-muted">Sesión actual: IP 127.0.0.1</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">Actividad Reciente de Usuarios</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Tenant\Usuario::with('role')->latest()->take(5)->get() as $u)
                                <tr>
                                    <td>{{ $u->name }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>
                                        <span class="badge bg-secondary rounded-pill">
                                            {{ optional($u->role)->nombre ?? 'Sin Rol' }}
                                        </span>
                                    </td>
                                    <td>{{ $u->created_at ? $u->created_at->diffForHumans() : 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary">Acceso Rápido</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @php
                            $openRegister = \App\Models\Tenant\CashRegister::open()->first();
                        @endphp

                        @if(!$openRegister)
                            <a href="{{ route('cash-registers.create') }}" class="btn btn-primary text-start shadow-sm py-2">
                                <i class="fa-solid fa-cash-register me-2"></i> Abrir Nueva Caja
                            </a>
                        @else
                            <a href="{{ route('cash-registers.close-form', $openRegister) }}" class="btn btn-danger text-start shadow-sm py-2">
                                <i class="fa-solid fa-lock me-2"></i> Cerrar Caja Actual
                            </a>
                        @endif

                        <hr class="my-2 opacity-10">
                        <a href="{{ route('usuarios.create') }}" class="btn btn-outline-primary text-start">
                            <i class="fa-solid fa-user-plus me-2"></i> Crear Nuevo Usuario
                        </a>
                        <a href="{{ route('roles.create') }}" class="btn btn-outline-success text-start">
                            <i class="fa-solid fa-shield-halved me-2"></i> Definir Nuevo Rol
                        </a>
                        <form action="{{ route('permissions.sync') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-info text-start w-100">
                                <i class="fa-solid fa-rotate me-2"></i> Sincronizar Permisos
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
