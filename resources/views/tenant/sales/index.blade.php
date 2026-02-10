@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Ventas Realizadas</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('sales.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Nueva Venta (POS)
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initSalesIndex({
            routes: {
                index: "{{ route('sales.index') }}",
                show: "{{ route('sales.show', ':id') }}",
                ticket: "{{ route('sales.ticket', ':id') }}",
                destroy: "{{ route('sales.destroy', ':id') }}"
            },
            tokens: {
                csrf: "{{ csrf_token() }}"
            }
        });
    });
</script>
@endsection
