@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Proveedores</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Nuevo Proveedor
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initSuppliersIndex({
                routes: {
                    index: "{{ route('suppliers.index') }}",
                    edit: "{{ route('suppliers.edit', ':id') }}",
                    destroy: "{{ route('suppliers.destroy', ':id') }}"
                },
                tokens: {
                    csrf: "{{ csrf_token() }}"
                }
            });
        });
    </script>
</div>
@endsection
