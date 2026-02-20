@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="journal-entries-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Libro Diario</h1>
            <p class="text-muted small">Registro detallado de todos los movimientos contables.</p>
        </div>
        <div class="col-auto">
            {{-- Filtros de Fecha --}}
            <div class="d-flex gap-2">
                <input type="date" id="filterStartDate" class="form-control form-control-sm" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                <input type="date" id="filterEndDate" class="form-control form-control-sm" value="{{ now()->endOfMonth()->format('Y-m-d') }}">
                <button id="btnFilter" class="btn btn-sm btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div id="journalEntriesGrid"></div>
        </div>
    </div>
</div>
@endsection
