@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="credit-notes-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Notas de Crédito y Devoluciones</h1>
            <p class="text-muted small">Gestión de productos devueltos y ajustes a ventas realizadas.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('sales.index') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Nueva Devolución
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="wrapper">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary opacity-50 mb-3" role="status"></div>
                    <p class="text-muted small">Cargando registros...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
