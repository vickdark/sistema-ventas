@extends('layouts.app')

@section('content')

<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="expenses-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Gastos Operativos</h1>
            <p class="mb-0 text-muted">Registra y controla los egresos diarios del negocio.</p>
        </div>
        <div class="col-auto">
             @if(auth()->user()->hasPermission('expense-categories.index'))
            <a href="{{ route('expense-categories.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">
                <i class="fas fa-tags me-2"></i> Categorías
            </a>
            @endif
            @if(auth()->user()->hasPermission('expenses.create'))
            <a href="{{ route('expenses.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Nuevo Gasto
            </a>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>
@endsection
