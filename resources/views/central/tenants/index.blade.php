@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="central-tenants-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Administración de Empresas</h1>
            <p class="text-muted small mb-0">Listado centralizado de suscripciones y dominios técnicos</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('central.tenants.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm transition-all hover-lift">
                <i class="fas fa-plus-circle me-2"></i> Nueva Empresa
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
