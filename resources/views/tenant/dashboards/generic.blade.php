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
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-dark h-100 transform-hover cursor-pointer" onclick="window.downloadOfflineMode()">
                <div class="card-body p-4 d-flex align-items-center justify-content-between position-relative">
                    <div class="z-1">
                        <h5 class="fw-bold text-white mb-1">MODO OFFLINE</h5>
                        <p class="text-white text-opacity-75 mb-0 small">Descargar todos los recursos para trabajar sin internet</p>
                    </div>
                    <i class="fa-solid fa-cloud-arrow-down fa-3x text-white opacity-50 z-1"></i>
                    <div class="decoration-circle-1"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection