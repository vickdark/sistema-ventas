@extends('layouts.app')

@section('content')
<div class="container-fluid">
    @php
        $attendanceConfig = json_encode([
            'routes' => [
                'status'    => route('attendance.status'),
                'clock_in'  => route('attendance.clock-in'),
                'clock_out' => route('attendance.clock-out', 'FAKE_ID'),
            ],
        ], JSON_HEX_APOS);
    @endphp
    <div id="dashboard-attendance-widget" data-config='{!! $attendanceConfig !!}'></div>
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
            <h1 class="h3 mb-1 text-gray-800 fw-bold">Bienvenido al Panel de Control, {{ auth()->user()->name }}</h1>
            <p class="text-muted mb-0 d-flex align-items-center flex-wrap gap-2">
                <span class="badge bg-info bg-opacity-10 text-info px-3 rounded-pill">{{ auth()->user()->role->nombre ?? 'Dashboard' }}</span>
                <span class="opacity-50">|</span>
                <i class="fa-solid fa-building opacity-50"></i>
                <span class="fw-medium">{{ tenant('business_name') ?? tenant('id') }}</span>
                <span class="opacity-50">|</span>
                <i class="fa-solid fa-calendar-day opacity-50"></i>
                <span>{{ now()->format('d/m/Y') }}</span>
            </p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center">
                    <i class="fa-solid fa-circle-check fa-3x text-success mb-3"></i>
                    <h4>Sistema Configurado</h4>
                    <p class="text-muted mb-0">Utiliza el menú lateral para navegar por los módulos de tu cuenta.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4 border-start border-primary h-100" style="border-width: 0 0 0 4px;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Control Asistencia</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="dashboardAttendanceStatus">
                                --
                            </div>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle">
                            <i class="fa-solid fa-clock fs-4 text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button id="btnDashboardClock" class="btn btn-sm btn-outline-primary w-100 rounded-pill" disabled>
                            Cargando...
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection