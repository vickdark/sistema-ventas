@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 report-facelift">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="reports-index-page" data-config='@json($reportsConfig)'></div>

    <div class="row mb-5 align-items-center">
        <div class="col-sm-6">
            <h1 class="h2 fw-bold text-dark mb-1">Análisis de Negocio</h1>
            <p class="text-muted d-flex align-items-center gap-2">
                <i class="fas fa-magic text-primary"></i> 
                Insights estratégicos y rendimiento financiero en tiempo real.
            </p>
        </div>
        <div class="col-sm-6 text-end">
            <button type="button" class="btn btn-white shadow-sm border-0 rounded-pill px-4 hover-lift" id="exportExcel">
                <i class="fas fa-file-excel text-success me-2"></i> 
                <span class="fw-semibold">Descargar Reporte</span>
            </button>
        </div>
    </div>

    <!-- Compressed KPI Grid -->
    <div class="row g-3 mb-4">
        <!-- Ingresos Hoy -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="report-card primary compact">
                <div class="card-body p-3 position-relative overflow-hidden">
                    <div class="z-1 position-relative">
                        <div class="stat-label">HOY</div>
                        <div class="stat-value-sm">$ {{ number_format($stats['ingresoDiario'], 0) }}</div>
                    </div>
                    <i class="fas fa-calendar-day icon-bg-sm"></i>
                </div>
            </div>
        </div>

        <!-- Ingresos Mes -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="report-card success compact">
                <div class="card-body p-3 position-relative overflow-hidden">
                    <div class="z-1 position-relative">
                        <div class="stat-label">MES</div>
                        <div class="stat-value-sm">$ {{ number_format($stats['ingresoMensual'], 0) }}</div>
                    </div>
                    <i class="fas fa-calendar-alt icon-bg-sm"></i>
                </div>
            </div>
        </div>

        <!-- Ingreso Anual -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="report-card warning compact">
                <div class="card-body p-3 position-relative overflow-hidden">
                    <div class="z-1 position-relative">
                        <div class="stat-label">AÑO</div>
                        <div class="stat-value-sm">$ {{ number_format($stats['ingresoAnual'], 0) }}</div>
                    </div>
                    <i class="fas fa-chart-line icon-bg-sm"></i>
                </div>
            </div>
        </div>

        <!-- Cartera -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="report-card danger compact">
                <div class="card-body p-3 position-relative overflow-hidden">
                    <div class="z-1 position-relative">
                        <div class="stat-label">CARTERA</div>
                        <div class="stat-value-sm">$ {{ number_format($stats['deudaTotalClientes'], 0) }}</div>
                    </div>
                    <i class="fas fa-wallet icon-bg-sm"></i>
                </div>
            </div>
        </div>

        <!-- Facturas Pendientes -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="report-card info compact">
                <div class="card-body p-3 position-relative overflow-hidden">
                    <div class="z-1 position-relative">
                        <div class="stat-label">PENDIENTES</div>
                        <div class="stat-value-sm">{{ $stats['cantidadCreditosPendientes'] }} <small class="opacity-75" style="font-size: 0.6rem;">DOCS</small></div>
                    </div>
                    <i class="fas fa-file-invoice icon-bg-sm"></i>
                </div>
            </div>
        </div>

        <!-- Inventario -->
        <div class="col-xl-2 col-md-4 col-6">
            <div class="report-card dark compact">
                <div class="card-body p-3 position-relative overflow-hidden">
                    <div class="z-1 position-relative">
                        <div class="stat-label">INVENTARIO</div>
                        <div class="stat-value-sm">$ {{ number_format($stats['valorInventario'], 0) }}</div>
                    </div>
                    <i class="fas fa-warehouse icon-bg-sm"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4">
        <!-- Main Balance Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 chart-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">Balance Financiero Anual</h5>
                        <small class="text-muted">Comparativa de ingresos vs egresos por mes</small>
                    </div>
                    <div class="chart-actions">
                        <button class="btn btn-light btn-sm rounded-circle"><i class="fas fa-sync-alt"></i></button>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-container" style="height: 380px;">
                        <canvas id="balanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Placeholder Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 chart-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Próximamente</h5>
                    <small class="text-muted">Nuevas métricas en desarrollo</small>
                </div>
                <div class="card-body px-4 pb-4 d-flex align-items-center justify-content-center">
                    <div class="text-center py-5">
                        <i class="fas fa-chart-pie fa-4x text-muted opacity-25 mb-3"></i>
                        <p class="text-muted mb-0">Espacio reservado para<br>futuras estadísticas</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="col-xl-6 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 chart-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Métodos de Pago</h5>
                    <small class="text-muted">Distribución de facturación</small>
                </div>
                <div class="card-body px-4 pb-4 d-flex align-items-center">
                    <div class="chart-container w-100" style="height: 300px;">
                        <canvas id="metodosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash vs Transfer -->
        <div class="col-xl-6 col-lg-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 chart-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Efectivo vs Transferencia</h5>
                    <small class="text-muted">Comparativa de pagos inmediatos</small>
                </div>
                <div class="card-body px-4 pb-4 d-flex align-items-center">
                    <div class="chart-container w-100" style="height: 300px;">
                        <canvas id="cashTransferChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly Performance -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 chart-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Rendimiento Semanal</h5>
                    <small class="text-muted">Flujo de caja de los últimos 7 días</small>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="ventasChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 chart-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Top 5 Productos Estrella</h5>
                    <small class="text-muted">Artículos con mayor rotación</small>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="productosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cash Performance -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 chart-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Arqueos de Caja</h5>
                    <small class="text-muted">Eficiencia en los cierres diarios</small>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="cajaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories Distribution -->
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 chart-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="fw-bold text-dark mb-0">Stock por Categoría</h5>
                    <small class="text-muted">Diversificación del inventario</small>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="chart-container" style="height: 250px;">
                        <canvas id="categoriaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
