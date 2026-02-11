@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Caja</h1>
        </div>
        <div class="col-auto">
            @can('cash-registers.create')
                @if(!$currentRegister)
                    <a href="{{ route('cash-registers.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-unlock me-2"></i> Abrir Nueva Caja
                    </a>
                @endif
            @endcan
            
            @can('configurations.update')
            <button type="button" class="btn btn-outline-info rounded-pill px-4 ms-2" data-bs-toggle="modal" data-bs-target="#configModal">
                <i class="fas fa-cog me-2"></i> Configurar Cajas
            </button>
            @endcan
        </div>
    </div>

    <!-- Modal de Configuración -->
    <div class="modal fade" id="configModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Configuración de Cajas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('configurations.update') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-4">
                            <label for="cash_register_names" class="form-label fw-medium">Nombres de Cajas Disponibles</label>
                            <textarea class="form-control rounded-3" id="cash_register_names" name="cash_register_names" rows="3" placeholder="Ej: Caja Principal, Caja Pasillo, Caja 2">{{ $config->cash_register_names ? implode(', ', $config->cash_register_names) : '' }}</textarea>
                            <small class="text-muted mt-2 d-block">Separa los nombres por comas. Estos serán los únicos identificadores que los usuarios podrán seleccionar al abrir una caja.</small>
                        </div>

                        <div class="mb-3">
                            <label for="cash_register_closing_time" class="form-label fw-medium">Hora de Cierre Diario</label>
                            <input type="time" class="form-control rounded-3" id="cash_register_closing_time" name="cash_register_closing_time" 
                                value="{{ $config->cash_register_closing_time ? \Carbon\Carbon::parse($config->cash_register_closing_time)->format('H:i') : '' }}">
                            <small class="text-muted mt-2 d-block">Configura la hora en que el sistema sugerirá el cierre de las cajas abiertas.</small>
                        </div>
                        @if($config->cash_register_closing_time)
                            <div class="alert alert-light border-0 rounded-3 mb-0 small">
                                <i class="fas fa-info-circle me-2 text-info"></i>
                                Programado actualmente para las: <strong>{{ \Carbon\Carbon::parse($config->cash_register_closing_time)->format('h:i A') }}</strong>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($currentRegister)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-soft rounded-4 bg-primary text-white overflow-hidden">
                    <div class="card-body p-4 position-relative">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="text-uppercase fw-bold opacity-75 small mb-2">Estado Actual: {{ $currentRegister->name }} Abierta</h6>
                                <h2 class="fw-bold mb-1">${{ number_format($currentRegister->initial_amount, 2) }}</h2>
                                <p class="mb-0 opacity-75">Monto Inicial - Abierta por {{ $currentRegister->user->name }} el {{ \Carbon\Carbon::parse($currentRegister->opening_date)->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('cash-registers.close-form', $currentRegister) }}" class="btn btn-light rounded-pill px-4 shadow-sm">
                                    <i class="fas fa-lock me-2 text-danger"></i> Cerrar Mi Caja
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-0">
            <h6 class="m-0 font-weight-bold text-primary">Historial de Sesiones</h6>
        </div>
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initCashRegistersIndex({
            routes: {
                index: "{{ route('cash-registers.index') }}",
                show: "{{ route('cash-registers.show', ':id') }}"
            }
        });
    });
</script>
@endsection
