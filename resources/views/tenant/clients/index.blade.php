@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Clientes</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('clients.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Nuevo Cliente
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
        initClientsIndex({
            routes: {
                index: "{{ route('clients.index') }}",
                edit: "{{ route('clients.edit', ':id') }}",
                destroy: "{{ route('clients.destroy', ':id') }}"
            },
            tokens: {
                csrf: "{{ csrf_token() }}"
            }
        });
    });
</script>
@endsection
