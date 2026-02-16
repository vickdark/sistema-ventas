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
</div>
@endsection