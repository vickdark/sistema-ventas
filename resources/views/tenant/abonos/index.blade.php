@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Cartera y Abonos</h1>
            <p class="text-muted small mb-0">Gestión de clientes con ventas pendientes y cobros.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('abonos.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Nuevo Abono Directo
            </a>
        </div>
    </div>

    <!-- Tabs para alternar entre Deudas y Historial -->
    <ul class="nav nav-pills mb-4" id="abonosTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4 me-2" id="debtors-tab" data-bs-toggle="pill" data-bs-target="#debtors" type="button" role="tab">
                <i class="fas fa-users-slash me-2"></i> Clientes con Deuda
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-4" id="history-tab" data-bs-toggle="pill" data-bs-target="#history" type="button" role="tab">
                <i class="fas fa-history me-2"></i> Historial de Abonos
            </button>
        </li>
    </ul>

    <div class="tab-content" id="abonosTabContent">
        <!-- Pestaña de Deudores -->
        <div class="tab-pane fade show active" id="debtors" role="tabpanel">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div id="debtorsWrapper"></div>
                </div>
            </div>
        </div>

        <!-- Pestaña de Historial -->
        <div class="tab-pane fade" id="history" role="tabpanel">
            <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div id="historyWrapper"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initAbonosIndex({
            routes: {
                index: "{{ route('abonos.index') }}",
                create: "{{ route('abonos.create') }}",
                destroy: "{{ route('abonos.destroy', ':id') }}"
            },
            tokens: {
                csrf: "{{ csrf_token() }}"
            }
        });
    });
</script>
@endsection
