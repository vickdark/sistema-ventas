@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Dashboard Central</h1>
            <p class="text-muted small mb-0">Resumen general de todo el ecosistema de inquilinos y dominios.</p>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row g-4 mb-4">
        <!-- Total Tenants -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-soft rounded-4 h-100 border-start border-primary border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 rounded-3 p-3 text-primary">
                            <i class="fas fa-building fs-4"></i>
                        </div>
                        <div class="ms-4">
                            <p class="text-muted small mb-1 fw-medium uppercase">Total Inquilinos</p>
                            <h3 class="mb-0 fw-bold">{{ $totalTenants }}</h3>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <a href="{{ route('central.tenants.index') }}" class="text-primary text-decoration-none small fw-semibold">
                            Ver todos los registros <i class="fas fa-arrow-right ms-1 small"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Tenant Users -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-soft rounded-4 h-100 border-start border-info border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 rounded-3 p-3 text-info">
                            <i class="fas fa-users fs-4"></i>
                        </div>
                        <div class="ms-4">
                            <p class="text-muted small mb-1 fw-medium uppercase">Usuarios Inquilinos</p>
                            <h3 class="mb-0 fw-bold">{{ $totalTenantUsers }}</h3>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <span class="text-muted small">Cuentas activas en el ecosistema</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Domains -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-soft rounded-4 h-100 border-start border-success border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 rounded-3 p-3 text-success">
                            <i class="fas fa-globe fs-4"></i>
                        </div>
                        <div class="ms-4">
                            <p class="text-muted small mb-1 fw-medium uppercase">Dominios Activos</p>
                            <h3 class="mb-0 fw-bold">{{ $totalDomains }}</h3>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top">
                        <span class="text-muted small">Portales web desplegados</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- DB Size / Growth -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-soft rounded-4 h-100 border-start border-warning border-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-warning bg-opacity-10 rounded-3 p-3 text-warning">
                            <i class="fas fa-database fs-4"></i>
                        </div>
                        <div class="ms-4">
                            <p class="text-muted small mb-1 fw-medium uppercase">Almacenamiento</p>
                            <h3 class="mb-0 fw-bold">{{ $dbSize }} <span class="fs-6 fw-normal text-muted">MB</span></h3>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-top d-flex justify-content-between">
                        <span class="text-muted small">Nuevos (mes):</span>
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill">{{ $newTenantsThisMonth }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- HTTP Logs & Additional Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
                <div class="card-header bg-white p-4 border-bottom-0">
                    <h5 class="mb-0 fw-bold">Estado de Respuestas HTTP</h5>
                    <p class="text-muted small mb-0">Distribución de tráfico global (Central + Inquilinos)</p>
                </div>
                <div class="card-body p-4">
                    <div class="progress rounded-pill mb-4 http-progress-container">
                        @php
                            $totalHttp = array_sum($httpStats);
                            $percent2xx = $totalHttp > 0 ? ($httpStats['2xx'] / $totalHttp) * 100 : 0;
                            $percent3xx = $totalHttp > 0 ? ($httpStats['3xx'] / $totalHttp) * 100 : 0;
                            $percent4xx = $totalHttp > 0 ? ($httpStats['4xx'] / $totalHttp) * 100 : 0;
                            $percent5xx = $totalHttp > 0 ? ($httpStats['5xx'] / $totalHttp) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success http-progress-bar" role="progressbar" style="--percent: {{ $percent2xx }}%" title="2xx: {{ $httpStats['2xx'] }}"></div>
                        <div class="progress-bar bg-info http-progress-bar" role="progressbar" style="--percent: {{ $percent3xx }}%" title="3xx: {{ $httpStats['3xx'] }}"></div>
                        <div class="progress-bar bg-warning http-progress-bar" role="progressbar" style="--percent: {{ $percent4xx }}%" title="4xx: {{ $httpStats['4xx'] }}"></div>
                        <div class="progress-bar bg-danger http-progress-bar" role="progressbar" style="--percent: {{ $percent5xx }}%" title="5xx: {{ $httpStats['5xx'] }}"></div>
                    </div>
                    <div class="row text-center">
                        <div class="col">
                            <h4 class="mb-0 fw-bold text-success">{{ $httpStats['2xx'] }}</h4>
                            <span class="text-muted small">Éxito (2xx)</span>
                        </div>
                        <div class="col border-start">
                            <h4 class="mb-0 fw-bold text-info">{{ $httpStats['3xx'] }}</h4>
                            <span class="text-muted small">Redirec (3xx)</span>
                        </div>
                        <div class="col border-start">
                            <h4 class="mb-0 fw-bold text-warning">{{ $httpStats['4xx'] }}</h4>
                            <span class="text-muted small">Error (4xx)</span>
                        </div>
                        <div class="col border-start">
                            <h4 class="mb-0 fw-bold text-danger">{{ $httpStats['5xx'] }}</h4>
                            <span class="text-muted small">Falla (5xx)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-soft rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white p-4 border-bottom-0">
                    <h5 class="mb-0 fw-bold">Salud del Sistema</h5>
                </div>
                <div class="card-body p-4 pt-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 py-3 border-0 d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-database text-primary me-2"></i>
                                <span class="text-muted small uppercase fw-semibold">Base de Datos</span>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                <i class="fas fa-check-circle me-1"></i> {{ $serverHealth['db_connection'] }}
                            </span>
                        </li>
                        <li class="list-group-item px-0 py-3 border-0 d-flex justify-content-between align-items-center border-top">
                            <div>
                                <i class="fas fa-microchip text-primary me-2"></i>
                                <span class="text-muted small uppercase fw-semibold">Memoria PHP</span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">{{ $serverHealth['memory_usage'] }} MB</div>
                                <div class="text-muted text-tiny">Límite: {{ $serverHealth['memory_limit'] }}</div>
                            </div>
                        </li>
                        <li class="list-group-item px-0 py-3 border-0 d-flex justify-content-between align-items-center border-top">
                            <div>
                                <i class="fab fa-php text-primary me-2"></i>
                                <span class="text-muted small uppercase fw-semibold">Versión PHP</span>
                            </div>
                            <span class="fw-bold text-dark">{{ $serverHealth['php_version'] }}</span>
                        </li>
                        <li class="list-group-item px-0 py-3 border-0 d-flex justify-content-between align-items-center border-top">
                            <div>
                                <i class="fas fa-server text-primary me-2"></i>
                                <span class="text-muted small uppercase fw-semibold">Web Server</span>
                            </div>
                            <span class="text-muted small text-truncate" style="max-width: 150px;">{{ str_replace('Microsoft-', '', $serverHealth['server_software']) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <!-- Tenant DB Details Table -->
    <div class="card border-0 shadow-soft rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">Métricas de Bases de Datos por Inquilino</h5>
                <p class="text-muted small mb-0">Uso de disco y conteo de tablas por cada empresa</p>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted small text-uppercase fw-bold border-0">Inquilino</th>
                            <th class="px-4 py-3 text-muted small text-uppercase fw-bold border-0">Nombre DB</th>
                            <th class="px-4 py-3 text-muted small text-uppercase fw-bold border-0 text-center">Tablas</th>
                            <th class="px-4 py-3 text-muted small text-uppercase fw-bold border-0 text-center">Usuarios</th>
                            <th class="px-4 py-3 text-muted small text-uppercase fw-bold border-0 text-end">Tamaño</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($tenantMetrics as $id => $metrics)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="fw-semibold text-dark">{{ $id }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <code class="small text-primary bg-primary bg-opacity-10 px-2 py-1 rounded">{{ $metrics['db_name'] }}</code>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">{{ $metrics['tables'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-blue bg-opacity-10 text-primary rounded-pill">{{ $metrics['users'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="fw-bold">{{ $metrics['size'] }} MB</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Tenants Table -->
    <div class="card border-0 shadow-soft rounded-4 overflow-hidden mb-4">

        <div class="card-header bg-white p-4 border-bottom-0 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold">Inquilinos Recientes</h5>
                <p class="text-muted small mb-0">Últimos 5 registros incorporados al sistema</p>
            </div>
            <a href="{{ route('central.tenants.create') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                <i class="fas fa-plus me-1"></i> Nuevo
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted small text-uppercase fw-bold border-0">ID / Empresa</th>
                            <th class="px-4 py-3 text-muted small text-uppercase fw-bold border-0">Dominio principal</th>
                            <th class="px-4 py-3 text-muted small text-uppercase fw-bold border-0">Fecha Registro</th>
                            <th class="px-4 py-3 text-muted small text-uppercase fw-bold border-0 text-center">Estado</th>
                            <th class="px-4 py-3 border-0"></th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @foreach($recentTenants as $tenant)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="app-user-avatar sm me-3 tenant-avatar-bg">
                                        {{ strtoupper(substr($tenant->id, 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold text-dark">{{ $tenant->id }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-muted">{{ $tenant->domains->first()?->domain ?? 'Sin dominio' }}</span>
                            </td>
                            <td class="px-4 py-3 text-muted">
                                {{ $tenant->created_at->format('d M, Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">
                                    Activo
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <a href="{{ route('central.tenants.edit', $tenant->id) }}" class="btn btn-light btn-sm rounded-pill border shadow-sm">
                                    Administrar <i class="fas fa-chevron-right ms-1 small"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
