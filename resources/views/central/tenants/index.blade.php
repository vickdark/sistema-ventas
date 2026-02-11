@extends('layouts.app')

@section('content')
<div class="container-fluid">
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
            <div id="tenants-grid"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.initTenantsIndex) {
            window.initTenantsIndex({
                routes: {
                    index: "{{ route('central.tenants.index') }}",
                    edit: "{{ route('central.tenants.edit', ':id') }}",
                    destroy: "{{ route('central.tenants.destroy', ':id') }}",
                    markPaid: "{{ route('central.tenants.mark-as-paid', ':id') }}"
                },
                db_prefix: "{{ config('database.connections.central.database') }}",
                tokens: {
                    csrf: "{{ csrf_token() }}"
                }
            });
        }
    });
</script>

<style>
    .hover-lift:hover {
        transform: translateY(-2px);
    }
    .shadow-soft {
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important;
    }
</style>
@endsection
