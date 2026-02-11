export function initReportsIndex(data) {
    const { ventasSemana, topProductos, datosCaja, catProductos, balanceMensual, metodosPago, efectivoVsTransferencia } = data;

    // Helper para gradientes
    const getGradient = (ctx, color1, color2) => {
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, color1);
        gradient.addColorStop(1, color2);
        return gradient;
    };

    // Configuración global de Chart.js para un look premium
    Chart.defaults.font.family = "'Instrument Sans', sans-serif";
    Chart.defaults.color = '#64748b';
    Chart.defaults.plugins.tooltip.backgroundColor = '#1e293b';
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.borderRadius = 8;

    // 1. VENTAS SEMANALES (Area Chart)
    const ctxVentas = document.getElementById('ventasChart');
    if (ctxVentas) {
        const context = ctxVentas.getContext('2d');
        const gradient = getGradient(context, 'rgba(16, 185, 129, 0.3)', 'rgba(16, 185, 129, 0)');
        
        new Chart(ctxVentas, {
            type: 'line',
            data: {
                labels: ventasSemana.map(v => v.fecha),
                datasets: [{
                    label: 'Ingresos ($)',
                    data: ventasSemana.map(v => v.total),
                    borderColor: '#10b981',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointBackgroundColor: '#10b981',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { display: false }, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false } }
                }
            }
        });
    }

    // 2. TOP PRODUCTOS (Horizontal Bar)
    const ctxProd = document.getElementById('productosChart');
    if (ctxProd) {
        new Chart(ctxProd, {
            type: 'bar',
            data: {
                labels: topProductos.map(p => p.product_name),
                datasets: [{
                    label: 'Unidades',
                    data: topProductos.map(p => p.total_vendido),
                    backgroundColor: ['#6366f1', '#8b5cf6', '#a855f7', '#d946ef', '#ec4899'],
                    borderRadius: 10,
                    barThickness: 20
                }]
            },
            options: { 
                indexAxis: 'y',
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { grid: { display: false }, border: { display: false } }
                }
            }
        });
    }

    // 3. CAJA (Stacked / Grouped Bar)
    const ctxCaja = document.getElementById('cajaChart');
    if (ctxCaja) {
        new Chart(ctxCaja, {
            type: 'bar',
            data: {
                labels: datosCaja.map(c => c.date),
                datasets: [
                    { 
                        label: 'M. Inicial', 
                        data: datosCaja.map(c => c.initial_amount), 
                        backgroundColor: '#cbd5e1',
                        borderRadius: 5
                    },
                    { 
                        label: 'M. Final', 
                        data: datosCaja.map(c => c.final_amount), 
                        backgroundColor: '#6366f1',
                        borderRadius: 5
                    }
                ]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                scales: {
                    y: { grid: { color: 'rgba(0,0,0,0.05)' }, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false } }
                }
            }
        });
    }

    // 4. CATEGORIAS (Clean Doughnut)
    const ctxCat = document.getElementById('categoriaChart');
    if (ctxCat) {
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: catProductos.map(c => c.name),
                datasets: [{
                    data: catProductos.map(c => c.products_count),
                    backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#f43f5e', '#8b5cf6', '#06b6d4'],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                cutout: '70%',
                plugins: { 
                    legend: { 
                        position: 'right',
                        labels: { usePointStyle: true, padding: 20 }
                    } 
                } 
            }
        });
    }

    // 5. BALANCE MENSUAL (Modern Bar)
    const ctxBalance = document.getElementById('balanceChart');
    if (ctxBalance) {
        const labelsBalance = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        new Chart(ctxBalance, {
            type: 'bar',
            data: {
                labels: labelsBalance,
                datasets: [
                    { 
                        label: 'Ingresos ($)', 
                        data: balanceMensual.map(b => b.ingresos), 
                        backgroundColor: '#6366f1', 
                        borderRadius: 6,
                        barPercentage: 0.6
                    },
                    { 
                        label: 'Egresos ($)', 
                        data: balanceMensual.map(b => b.egresos), 
                        backgroundColor: '#f43f5e', 
                        borderRadius: 6,
                        barPercentage: 0.6
                    }
                ]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                scales: { 
                    y: { grid: { color: 'rgba(0,0,0,0.05)' }, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false } }
                } 
            }
        });
    }

    // 6. MÉTODOS DE PAGO (Clean Pie)
    const ctxMetodos = document.getElementById('metodosChart');
    if (ctxMetodos) {
        new Chart(ctxMetodos, {
            type: 'pie',
            data: {
                labels: metodosPago.map(m => m.payment_type),
                datasets: [{
                    data: metodosPago.map(m => m.total),
                    backgroundColor: ['#10b981', '#6366f1', '#fb923c', '#ef4444'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 15 }
                    } 
                } 
            }
        });
    }

    // 7. EFECTIVO VS TRANSFERENCIA (Doughnut Chart)
    const ctxCashTransfer = document.getElementById('cashTransferChart');
    if (ctxCashTransfer && efectivoVsTransferencia) {
        const labels = efectivoVsTransferencia.map(e => {
            if (e.payment_type === 'CONTADO') return 'Efectivo';
            if (e.payment_type === 'TRANSFERENCIA') return 'Transferencia';
            return e.payment_type;
        });
        
        new Chart(ctxCashTransfer, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: efectivoVsTransferencia.map(e => e.total),
                    backgroundColor: ['#10b981', '#6366f1'],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 15
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                cutout: '65%',
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: { 
                            usePointStyle: true, 
                            padding: 20,
                            font: { size: 13, weight: '600' }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: $${value.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    }
                } 
            }
        });
    }

    // EXPORTAR A EXCEL (Keep simple but functional)
    const exportBtn = document.getElementById('exportExcel');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            let exportData = [
                ["REPORTE ESTRATÉGICO DE VENTAS"],
                ["Generado el:", new Date().toLocaleString()],
                [],
                ["INDICADORES CLAVE (KPIs)"],
                ["Concepto", "Monto"],
                ["Ingresos de Hoy", "$ " + data.stats.ingresoDiario],
                ["Ingresos del Mes", "$ " + data.stats.ingresoMensual],
                ["Ingresos del Año", "$ " + data.stats.ingresoAnual],
                ["Cartera por Cobrar", "$ " + data.stats.deudaTotalClientes],
                ["Créditos Pendientes", data.stats.cantidadCreditosPendientes],
                ["Valor Inventario Actual", "$ " + data.stats.valorInventario],
                [],
                ["PRODUCTOS CON MAYOR ROTACIÓN"],
                ["Nombre del Producto", "Unidades Vendidas"]
            ];

            topProductos.forEach(tp => {
                exportData.push([tp.product_name, tp.total_vendido]);
            });

            const ws = XLSX.utils.aoa_to_sheet(exportData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Dashboard");
            XLSX.writeFile(wb, "Reporte_Analisis_" + new Date().toISOString().split('T')[0] + ".xlsx");
        });
    }
}
