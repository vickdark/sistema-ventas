@extends('layouts.app')

@section('content')

<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="activity-logs-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Auditoría de Sistema</h1>
            <p class="text-muted small">Registro detallado de acciones y cambios en el sistema.</p>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>
@endsection
