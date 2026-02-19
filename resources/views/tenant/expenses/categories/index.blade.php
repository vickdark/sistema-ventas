@extends('layouts.app')

@section('content')

<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="expense-categories-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Categorías de Gastos</h1>
            <p class="mb-0 text-muted">Gestiona las clasificaciones para tus egresos.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary rounded-pill px-4 me-2">
                <i class="fas fa-arrow-left me-2"></i> Volver a Gastos
            </a>
            @if(auth()->user()->hasPermission('expense-categories.create'))
            <a href="{{ route('expense-categories.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Nueva Categoría
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
