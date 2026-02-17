@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 report-facelift">
    {{-- Configuración de Página para PageLoader.js --}}
    <div id="reports-index-page" data-config='@json([
        "ventasSemana" => $ventasSemana,
        "topProductos" => $topProductos,
        "datosCaja" => $datosCaja,
        "catProductos" => $catProductos,
        "balanceMensual" => $balanceMensual,
        "metodosPago" => $metodosPago,
        "efectivoVsTransferencia" => $efectivoVsTransferencia,
        "stats" => [
            "ingresoDiario" => $stats['ingresoDiario'],
            "ingresoMensual" => $stats['ingresoMensual'],
            "ingresoAnual" => $stats['ingresoAnual'],
            "deudaTotalClientes" => $stats['deudaTotalClientes'],
            "cantidadCreditosPendientes" => $stats['cantidadCreditosPendientes'],
            "valorInventario" => $stats['valorInventario']
        ]
    ])'></div>

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

<style>
    .ls-1 { letter-spacing: 0.05em; }
    .z-1 { z-index: 1; }
    .icon-bg {
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 8rem;
        opacity: 0.15;
        transform: rotate(-15deg);
        color: white;
    }

    .icon-bg-sm {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 4rem;
        opacity: 0.12;
        transform: rotate(-10deg);
        color: white;
    }

    .report-card {
        border: 0;
        border-radius: 24px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        color: white;
        height: 100%;
    }

    .report-card.compact {
        border-radius: 16px;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.12);
    }

    .report-card.primary { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
    .report-card.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .report-card.danger { background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); }
    .report-card.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .report-card.info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .report-card.dark { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); }

    .stat-label {
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.1em;
        opacity: 0.8;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
    }

    .stat-value-sm {
        font-size: 1.25rem;
        font-weight: 800;
    }

    .decoration-1, .decoration-2, .decoration-3 {
        position: absolute;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        z-index: 0;
    }

    .decoration-1 { width: 150px; height: 150px; top: -50px; left: -50px; }
    .decoration-2 { width: 120px; height: 120px; bottom: -40px; right: 20%; }
    .decoration-3 { width: 180px; height: 180px; top: -20px; right: -60px; }

    .investment-hero {
        background: #1e293b;
        position: relative;
    }

    .glass-decoration {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background: radial-gradient(circle at 100% 0%, rgba(99, 102, 241, 0.15) 0%, transparent 50%);
    }

    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }

    .chart-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.03);
    }

    .btn-white {
        background: white;
        color: #334155;
    }
</style>
@endsection
