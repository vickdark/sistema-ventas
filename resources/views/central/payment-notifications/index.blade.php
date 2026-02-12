@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Bandeja de Notificaciones de Pago</h1>
            <p class="text-muted small mb-0">Comprobantes enviados por los clientes desde el portal de pago pendiente</p>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="notifications-grid"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.initPaymentNotificationsIndex) {
            window.initPaymentNotificationsIndex({
                routes: {
                    index: "{{ route('central.payment-notifications.index') }}"
                },
                tokens: {
                    csrf: "{{ csrf_token() }}"
                }
            });
        }
    });
</script>

<style>
    .shadow-soft {
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important;
    }
    .x-small {
        font-size: 0.75rem;
    }
    /* Alinear encabezados de Grid.js */
    .gridjs-th:nth-last-child(1), 
    .gridjs-th:nth-last-child(2) {
        text-align: center !important;
    }
    .gridjs-td:nth-last-child(1),
    .gridjs-td:nth-last-child(2) {
        text-align: center !important;
    }
</style>
@endsection
