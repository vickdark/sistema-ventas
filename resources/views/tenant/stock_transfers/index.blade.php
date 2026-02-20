@extends('layouts.app')

@section('content')

<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="stock-transfers-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Traslados entre Sucursales</h1>
            <p class="text-muted small">Mueve inventario de forma segura entre tus ubicaciones.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('stock-transfers.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm border-0">
                <i class="fas fa-truck-moving me-2"></i> Nuevo Traslado
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>
@endsection
