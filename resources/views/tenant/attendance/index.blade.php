@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Configuración de Página --}}
@php
    $attendanceConfig = json_encode([
        'routes' => [
            'index'     => route('attendance.index'),
            'clock_in'  => route('attendance.clock-in'),
            'clock_out' => route('attendance.clock-out', ':id'),
            'status'    => route('attendance.status'),
        ],
        'user_id'  => auth()->id(),
        'is_admin' => auth()->user()->hasRole('admin'),
    ], JSON_HEX_APOS);
@endphp
    <div id="attendance-index-page" data-config='{!! $attendanceConfig !!}'></div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Control de Asistencia</h1>
            <p class="text-muted small">Historial de entradas y salidas.</p>
        </div>
        
        <!-- Widget de Asistencia en Header -->
        <div id="attendanceWidget" class="d-flex align-items-center gap-3">
            <div class="text-end d-none d-md-block">
                <div class="small fw-bold text-uppercase text-muted">Estado Actual</div>
                <div class="h5 mb-0" id="currentStatusText">--</div>
            </div>
            <button id="btnClockAction" class="btn btn-lg rounded-pill px-4 shadow-sm d-flex align-items-center gap-2" disabled>
                <i class="fas fa-spinner fa-spin"></i> Cargando...
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4">
        <div class="card-body p-4">
            <!-- Filtros -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Desde</label>
                    <input type="date" id="filterStartDate" class="form-control" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Hasta</label>
                    <input type="date" id="filterEndDate" class="form-control" value="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-auto d-flex align-items-end">
                    <button id="btnFilter" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-search me-2"></i> Filtrar
                    </button>
                </div>
            </div>

            <!-- Tabla -->
            <div id="attendanceGrid"></div>
        </div>
    </div>
</div>
@endsection
