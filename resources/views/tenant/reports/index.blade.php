@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 align-items-center">
        <div class="col-sm-6">
            <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-pie text-primary me-2"></i>Reportes Estadísticos</h1>
            <p class="text-muted small">Visualización periódica del rendimiento del negocio.</p>
        </div>
        <div class="col-sm-6 text-end">
            <button type="button" class="btn btn-success shadow-sm rounded-pill px-4" id="exportExcel">
                <i class="fas fa-file-excel me-2"></i> Exportar a Excel
            </button>
        </div>
    </div>

    <!-- Tarjetas de Ingresos -->
    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card bg-primary text-white border-0 shadow-sm rounded-4 overflow-hidden position-relative h-100">
                <div class="card-body p-4 z-1">
                    <p class="text-uppercase small fw-bold mb-1 opacity-75">Ingresos de Hoy</p>
                    <h2 class="fw-bold mb-0">$ {{ number_format($ingresoDiario, 2) }}</h2>
                    <i class="fas fa-calendar-day fa-4x position-absolute end-0 bottom-0 opacity-25 me-n3 mb-n3"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card bg-success text-white border-0 shadow-sm rounded-4 overflow-hidden position-relative h-100">
                <div class="card-body p-4 z-1">
                    <p class="text-uppercase small fw-bold mb-1 opacity-75">Ingresos del Mes</p>
                    <h2 class="fw-bold mb-0">$ {{ number_format($ingresoMensual, 2) }}</h2>
                    <i class="fas fa-calendar-alt fa-4x position-absolute end-0 bottom-0 opacity-25 me-n3 mb-n3"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="card bg-dark text-white border-0 shadow-sm rounded-4 overflow-hidden position-relative h-100">
                <div class="card-body p-4 z-1">
                    <p class="text-uppercase small fw-bold mb-1 opacity-75">Ingresos del Año</p>
                    <h2 class="fw-bold mb-0">$ {{ number_format($ingresoAnual, 2) }}</h2>
                    <i class="fas fa-chart-line fa-4x position-absolute end-0 bottom-0 opacity-25 me-n3 mb-n3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Deudas y Créditos -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card bg-danger text-white border-0 shadow-sm rounded-4 overflow-hidden position-relative h-100">
                <div class="card-body p-4 z-1">
                    <p class="text-uppercase small fw-bold mb-1 opacity-75">Cartera por Cobrar (Deuda Total)</p>
                    <h2 class="fw-bold mb-0">$ {{ number_format($deudaTotalClientes, 2) }}</h2>
                    <i class="fas fa-hand-holding-usd fa-4x position-absolute end-0 bottom-0 opacity-25 me-n3 mb-n3"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative h-100" style="background: linear-gradient(135deg, #f97316 0%, #fb923c 100%); color: white;">
                <div class="card-body p-4 z-1">
                    <p class="text-uppercase small fw-bold mb-1 opacity-75">Facturas a Crédito Pendientes</p>
                    <h2 class="fw-bold mb-0">{{ $cantidadCreditosPendientes }} <small class="fw-normal" style="font-size: 1rem;">Documentos</small></h2>
                    <i class="fas fa-file-invoice-dollar fa-4x position-absolute end-0 bottom-0 opacity-25 me-n3 mb-n3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Seccion Contable: Inversion -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%); color: white;">
                <div class="card-body p-4 z-1">
                    <div class="row align-items-center">
                        <div class="col-md-9">
                            <p class="text-uppercase small fw-bold mb-1 opacity-75">Inversión Total en Almacén (Costo de Inventario)</p>
                            <h2 class="fw-bold mb-0">$ {{ number_format($valorInversion, 2) }}</h2>
                            <small class="opacity-75">Basado en precio de costo * stock actual de todos los productos.</small>
                        </div>
                        <div class="col-md-3 text-end opacity-25">
                            <i class="fas fa-warehouse fa-5x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Gráfico: Balance Mensual -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="card-title fw-bold text-dark mb-0"><i class="fas fa-balance-scale text-primary me-2"></i>Balance Financiero Anual (Ingresos vs Compras)</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="balanceChart" style="min-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico: Métodos de Pago -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="card-title fw-bold text-dark mb-0"><i class="fas fa-wallet text-warning me-2"></i>Métodos de Pago</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="metodosChart" style="min-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico: Ventas Semanales -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="card-title fw-bold text-dark mb-0"><i class="fas fa-money-bill-wave text-success me-2"></i>Ingresos Semanales</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="ventasChart" style="min-height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico: Top Productos -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="card-title fw-bold text-dark mb-0"><i class="fas fa-star text-warning me-2"></i>Top 5 Productos Más Vendidos</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="productosChart" style="min-height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico: Arqueo de Caja -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="card-title fw-bold text-dark mb-0"><i class="fas fa-cash-register text-info me-2"></i>Desempeño de Caja (Últimos 10 Arqueos)</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="cajaChart" style="min-height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Gráfico: Distribución por Categorías -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="card-title fw-bold text-dark mb-0"><i class="fas fa-tags text-secondary me-2"></i>Stock por Categoría</h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <canvas id="categoriaChart" style="min-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        initReportsIndex({
            ventasSemana: @json($ventasSemana->map(fn($v) => ['fecha' => $v->fecha, 'total' => $v->total])),
            topProductos: @json($topProductos->map(fn($tp) => ['product_name' => optional($tp->product)->name, 'total_vendido' => $tp->total_vendido])),
            datosCaja: @json($datosCaja->map(fn($c) => ['date' => \Carbon\Carbon::parse($c->opening_date)->format('d/m'), 'initial_amount' => $c->initial_amount, 'final_amount' => $c->final_amount])),
            catProductos: @json($catProductos->map(fn($c) => ['name' => $c->name, 'products_count' => $c->products_count])),
            balanceMensual: @json($balanceMensual),
            metodosPago: @json($metodosPago->map(fn($m) => ['payment_type' => $m->payment_type, 'total' => $m->total])),
            stats: {
                ingresoDiario: "{{ number_format($ingresoDiario, 2) }}",
                ingresoMensual: "{{ number_format($ingresoMensual, 2) }}",
                ingresoAnual: "{{ number_format($ingresoAnual, 2) }}",
                deudaTotalClientes: "{{ number_format($deudaTotalClientes, 2) }}",
                cantidadCreditosPendientes: "{{ $cantidadCreditosPendientes }}",
                valorInversion: "{{ number_format($valorInversion, 2) }}"
            }
        });
    });
</script>

<style>
    .z-1 { z-index: 1; }
    .me-n3 { margin-right: -1rem !important; }
    .mb-n3 { margin-bottom: -1rem !important; }
</style>
@endsection
