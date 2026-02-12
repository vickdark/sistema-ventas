@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Mantenimiento y Comandos del Sistema</h1>
            <p class="text-muted">Ejecuta tareas de mantenimiento de forma manual desde esta interfaz.</p>
        </div>
    </div>

    <div class="row">
        <!-- Tarjeta de Información de Cronable -->
        <div class="col-12 mb-4">
            <div class="card border-left-info shadow-sm bg-light">
                <div class="card-body py-2 px-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <i class="fas fa-clock text-info small"></i>
                                <span class="text-xs font-weight-bold text-info text-uppercase">Configuración Cronable</span>
                                <span class="text-muted small ms-2 d-none d-md-inline">(Ejecutar cada minuto)</span>
                            </div>
                            <div class="bg-dark text-light p-2 rounded d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <code class="text-info small">php /RUTA/A/TU/PROYECTO/artisan schedule:run</code>
                                <div class="d-flex align-items-center gap-2 border-start border-secondary ps-2">
                                    <small class="text-white-50" style="font-size: 0.75rem;">Local:</small>
                                    <code class="text-warning" style="font-size: 0.75rem;">{{ base_path('artisan') }}</code>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto d-none d-lg-block">
                            <i class="fas fa-server fa-lg text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Tareas de Inquilinos -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Gestión de Inquilinos y Suscripciones</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Suspensión de Inquilinos</div>
                            <p class="small text-muted mt-2">Busca inquilinos con pagos vencidos y cambia su estado a suspendido.</p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-slash fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <form action="{{ route('central.maintenance.run') }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="command" value="tenants:suspend-expired">
                        <button type="submit" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-play me-1"></i> Ejecutar Ahora
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Cierre de Cajas -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Gestión de Ventas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Cierre Automático de Cajas</div>
                            <p class="small text-muted mt-2">Recorre todos los clientes y cierra las cajas que superaron su hora programada.</p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cash-register fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <form action="{{ route('central.maintenance.run') }}" method="POST" class="mt-3">
                        @csrf
                        <input type="hidden" name="command" value="app:auto-close-cash-registers">
                        <button type="submit" class="btn btn-success btn-sm btn-block">
                            <i class="fas fa-play me-1"></i> Ejecutar Ahora
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Limpieza de Caché -->
        <div class="col-xl-12 col-lg-12 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Sistema y Rendimiento</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Limpieza de Caché General</div>
                            <p class="small text-muted mt-2">Limpia la caché de configuración, vistas y optimización del sistema.</p>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-broom fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-4 mb-2">
                            <form action="{{ route('central.maintenance.run') }}" method="POST">
                                @csrf
                                <input type="hidden" name="command" value="optimize:clear">
                                <button type="submit" class="btn btn-warning btn-sm btn-block w-100">
                                    <i class="fas fa-sync me-1"></i> Optimize Clear
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4 mb-2">
                            <form action="{{ route('central.maintenance.run') }}" method="POST">
                                @csrf
                                <input type="hidden" name="command" value="config:clear">
                                <button type="submit" class="btn btn-outline-warning btn-sm btn-block w-100">
                                    <i class="fas fa-cogs me-1"></i> Config Clear
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4 mb-2">
                            <form action="{{ route('central.maintenance.run') }}" method="POST">
                                @csrf
                                <input type="hidden" name="command" value="view:clear">
                                <button type="submit" class="btn btn-outline-warning btn-sm btn-block w-100">
                                    <i class="fas fa-eye me-1"></i> View Clear
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('status'))
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Última Salida del Comando</h6>
                </div>
                <div class="card-body bg-dark text-light p-3 rounded-bottom">
                    <pre class="text-light mb-0" style="white-space: pre-wrap;">{{ session('status') }}</pre>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
