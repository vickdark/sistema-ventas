@extends('layouts.app')

@section('content')

<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="quotes-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Cotizaciones</h1>
            <p class="text-muted small">Gestiona tus presupuestos y ofertas comerciales.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('quotes.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm border-0">
                <i class="fas fa-plus me-2"></i> Nueva Cotización
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
