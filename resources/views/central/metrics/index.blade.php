@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Métricas y Logs del Sistema</h1>
            <p class="text-muted small">Monitoreo de tráfico HTTP y estado de bases de datos de inquilinos.</p>
        </div>
        <div class="col-auto">
            <form action="{{ route('central.metrics.clear') }}" method="POST" onsubmit="return confirm('¿Estás seguro de limpiar todos los logs HTTP?')">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-trash-alt me-1"></i> Limpiar Historial de Logs
                </button>
            </form>
        </div>
    </div>

    <!-- Resumen de Tráfico -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-primary text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase small fw-bold mb-1">Total Peticiones</h6>
                        <h2 class="mb-0 fw-bold">{{ number_format($stats['total_requests']) }}</h2>
                    </div>
                    <i class="fas fa-exchange-alt fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-success text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-uppercase small fw-bold mb-1">Duración Media</h6>
                        <h2 class="mb-0 fw-bold">{{ $stats['avg_duration'] }}s</h2>
                    </div>
                    <i class="fas fa-clock fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-3">
                <h6 class="text-uppercase small fw-bold text-muted mb-3">Distribución de Métodos</h6>
                <div class="d-flex gap-3">
                    @foreach($stats['methods'] as $m)
                    <div class="text-center">
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                            <span class="fw-bold">{{ $m->method }}:</span> {{ $m->count }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Logs HTTP -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2 text-primary"></i>Logs HTTP Recientes</h5>
                    <div class="d-flex gap-2">
                        <select class="form-select form-select-sm w-auto" id="filter-tenant">
                            <option value="">Todos los Inquilinos</option>
                            @foreach($tenants as $t)
                                <option value="{{ $t->id }}">{{ $t->id }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                        <table class="table table-hover align-middle mb-0 table-sm">
                            <thead class="bg-light sticky-top" style="z-index: 1;">
                                <tr>
                                    <th class="border-0 small text-uppercase px-3 py-2">Método</th>
                                    <th class="border-0 small text-uppercase py-2">Ruta</th>
                                    <th class="border-0 small text-uppercase text-center py-2">Estado</th>
                                    <th class="border-0 small text-uppercase text-center py-2">Duración</th>
                                    <th class="border-0 small text-uppercase text-end px-3 py-2">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                <tr>
                                    <td class="px-3 py-2">
                                        <span class="badge {{ $log->method == 'POST' ? 'bg-warning' : ($log->method == 'DELETE' ? 'bg-danger' : 'bg-info') }} bg-opacity-10 {{ $log->method == 'POST' ? 'text-warning' : ($log->method == 'DELETE' ? 'text-danger' : 'text-info') }} rounded-pill px-2 small">
                                            {{ $log->method }}
                                        </span>
                                    </td>
                                    <td class="py-2">
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-medium text-truncate" style="max-width: 200px; font-size: 0.75rem;" title="{{ $log->url }}">{{ $log->url }}</span>
                                            <span class="text-muted" style="font-size: 0.65rem;">{{ $log->tenant_id ?: 'CENTRAL' }} • {{ $log->ip }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center py-2">
                                        <span class="badge {{ $log->status >= 400 ? 'bg-danger' : ($log->status >= 300 ? 'bg-warning' : 'bg-success') }} rounded-pill px-2" style="font-size: 0.7rem;">
                                            {{ $log->status }}
                                        </span>
                                    </td>
                                    <td class="text-center py-2" style="font-size: 0.75rem;">{{ $log->duration }}s</td>
                                    <td class="px-3 text-end text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s d/m') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="p-2 d-flex justify-content-center border-top bg-light">
                        {{ $logs->onEachSide(0)->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas de Inquilinos -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-database me-2 text-success"></i>Bases de Datos</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($tenantMetrics as $id => $m)
                        <div class="list-group-item border-0 px-4 py-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-bold text-dark">{{ strtoupper($id) }}</span>
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill">{{ $m['size'] }} MB</span>
                            </div>
                            <div class="d-flex gap-3 small text-muted">
                                <span><i class="fas fa-users me-1"></i> {{ $m['users'] }}</span>
                                <span><i class="fas fa-shopping-cart me-1"></i> {{ $m['sales'] }}</span>
                                <span class="text-truncate" title="{{ $m['db_name'] }}"><i class="fas fa-link me-1"></i> {{ $m['db_name'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Distribución de Estados (Mini Card) -->
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold mb-3">Distribución HTTP</h6>
                @foreach($stats['status_distribution'] as $sd)
                <div class="mb-2">
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Estado {{ $sd->status }}</span>
                        <span class="fw-bold">{{ $sd->count }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar {{ $sd->status >= 400 ? 'bg-danger' : ($sd->status >= 300 ? 'bg-warning' : 'bg-success') }}" 
                             role="progressbar" 
                             style="width: {{ $stats['total_requests'] > 0 ? ($sd->count / $stats['total_requests']) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
