@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Detalle del Gasto</h1>
            <p class="text-muted mb-0">Referencia: <strong>{{ $expense->reference ?? 'S/N' }}</strong></p>
        </div>
        <div class="col-auto">
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
            @if(auth()->user()->hasPermission('expenses.destroy'))
                <button type="button" class="btn btn-danger rounded-pill px-4 ms-2" onclick="window.deleteExpense({{ $expense->id }})">
                    <i class="fas fa-trash me-2"></i> Eliminar
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card border-0 shadow-soft rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-money-bill-wave text-success fs-2"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">$ {{ number_format($expense->amount, 2) }}</h3>
                    <div class="badge border text-dark mb-3" style="border-left: 4px solid {{ $expense->category->color ?? '#6c757d' }} !important;">
                        {{ $expense->category->name }}
                    </div>
                    <hr>
                    <div class="text-start">
                        <p class="mb-2"><span class="text-muted small text-uppercase fw-bold">Fecha:</span><br> {{ $expense->date->format('d/m/Y') }}</p>
                        <p class="mb-2"><span class="text-muted small text-uppercase fw-bold">Registrado por:</span><br> {{ $expense->user->name }}</p>
                        <p class="mb-0"><span class="text-muted small text-uppercase fw-bold">Sucursal:</span><br> {{ $expense->branch->name ?? 'Sede Central' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-soft rounded-4 h-100">
                <div class="card-body p-4">
                    <h5 class="mb-4 text-primary"><i class="fas fa-file-alt me-2"></i>Descripción y Concepto</h5>
                    
                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Concepto</h6>
                        <p class="fs-5 fw-bold text-gray-800">{{ $expense->name }}</p>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted small text-uppercase fw-bold mb-2">Detalles Adicionales</h6>
                        <div class="p-3 bg-light rounded-3 text-gray-700 h-100 min-vh-20">
                            {{ $expense->description ?: 'No se proporcionaron detalles adicionales para este registro.' }}
                        </div>
                    </div>

                    <div class="row mt-auto">
                        <div class="col-md-6">
                             <h6 class="text-muted small text-uppercase fw-bold mb-2">Referencia / Comprobante</h6>
                             <p class="text-gray-800 fw-bold">{{ $expense->reference ?: 'Sin referencia' }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="text-muted small text-uppercase fw-bold mb-2">Fecha de Registro</h6>
                            <p class="text-muted small">{{ $expense->created_at->format('d/m/Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Reutilizamos la función de eliminación del index si es necesario, 
    // o definimos una específica para redireccionar tras borrar.
    window.deleteExpense = (id) => {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete(`/expenses/${id}`)
                    .then(response => {
                        window.location.href = "{{ route('expenses.index') }}";
                    })
                    .catch(error => {
                        console.error(error);
                        Swal.fire('Error', 'No se pudo eliminar el registro.', 'error');
                    });
            }
        })
    };
</script>
@endsection
