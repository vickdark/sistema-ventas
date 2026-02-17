@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="purchases-index-page" data-config='@json($config)'></div>

    <div class="row mb-4 align-items-center">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Compras a Proveedores</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('purchases.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-2"></i> Nueva Compra
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-soft rounded-4 overflow-hidden">
        <div class="card-body p-4">
            <div id="wrapper"></div>
        </div>
    </div>

    @if(session('new_purchase_id'))
        <div id="new-purchase-data" 
             data-id="{{ session('new_purchase_id') }}" 
             data-voucher-url="{{ route('purchases.voucher', session('new_purchase_id')) }}" 
             style="display: none;">
        </div>
    @endif

    {{-- Script inline para Swal de impresión --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const newPurchaseData = document.getElementById('new-purchase-data');
            if (newPurchaseData) {
                const voucherUrl = newPurchaseData.getAttribute('data-voucher-url');
                
                Swal.fire({
                    title: 'Compra Registrada',
                    text: "¿Desea imprimir el comprobante de ingreso?",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-print"></i> Imprimir',
                    cancelButtonText: 'Cerrar',
                    confirmButtonColor: '#ffc107', 
                    cancelButtonColor: '#858796',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(voucherUrl, '_blank');
                    }
                });
            }
        });
    </script>
</div>
@endsection
