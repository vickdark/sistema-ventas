@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <a href="{{ route('activity-logs.index') }}" class="btn btn-sm btn-light rounded-pill px-3 mb-2 shadow-sm border-0">
                <i class="fas fa-arrow-left me-1"></i> Volver a la lista
            </a>
            <h1 class="h3 mb-0 text-gray-800">Detalle de Actividad #{{ $log->id }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Información General</h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <label class="text-muted small d-block">Usuario</label>
                            <span class="fw-bold text-dark">{{ $log->user ? $log->user->name : 'Sistema' }}</span>
                        </li>
                        <li class="mb-3">
                            <label class="text-muted small d-block">Acción</label>
                            @php
                                $badges = [
                                    'created' => 'success',
                                    'updated' => 'info',
                                    'deleted' => 'danger',
                                    'login' => 'primary',
                                    'logout' => 'secondary'
                                ];
                                $color = $badges[$log->action] ?? 'dark';
                            @endphp
                            <span class="badge bg-{{ $color }}">{{ strtoupper($log->action) }}</span>
                        </li>
                        <li class="mb-3">
                            <label class="text-muted small d-block">Módulo</label>
                            <span class="badge bg-light text-dark border">{{ class_basename($log->model_type) }}</span>
                        </li>
                        <li class="mb-3">
                            <label class="text-muted small d-block">ID de Registro</label>
                            <span class="text-dark">#{{ $log->model_id }}</span>
                        </li>
                        <li class="mb-3">
                            <label class="text-muted small d-block">Fecha y Hora</label>
                            <span class="text-dark">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold mb-0">Datos Técnicos</h5>
                </div>
                <div class="card-body p-4">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <label class="text-muted small d-block">Dirección IP</label>
                            <code>{{ $log->ip_address }}</code>
                        </li>
                        <li>
                            <label class="text-muted small d-block">Agente de Usuario</label>
                            <span class="small text-muted" style="word-break: break-all;">{{ $log->user_agent }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">Cambios Realizados</h5>
                    <i class="fas fa-history text-muted"></i>
                </div>
                <div class="card-body p-4">
                    @if($log->changes)
                        @php $changes = $log->changes; @endphp
                        
                        @if(isset($changes['before']))
                            <div class="row">
                                <div class="col-md-6 border-end">
                                    <h6 class="fw-bold text-danger border-bottom pb-2 mb-3">Valor Anterior</h6>
                                    <pre class="bg-light p-3 rounded-3 small border mb-0" style="max-height: 500px; overflow-y: auto;"><code>{{ json_encode($changes['before'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-success border-bottom pb-2 mb-3">Nuevo Valor</h6>
                                    <pre class="bg-light p-3 rounded-3 small border mb-0" style="max-height: 500px; overflow-y: auto;"><code>{{ json_encode($changes['after'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                                </div>
                            </div>
                        @else
                            <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Datos del Registro</h6>
                            <pre class="bg-light p-3 rounded-3 border-start border-primary border-4 shadow-sm" style="max-height: 500px; overflow-y: auto;"><code class="text-dark">{{ json_encode($changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-info-circle text-muted opacity-25" style="font-size: 4rem;"></i>
                            </div>
                            <h5 class="text-muted fw-bold">Sin detalles específicos</h5>
                            <p class="text-muted opacity-75 mb-0">No se registraron cambios granulares para esta acción.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
