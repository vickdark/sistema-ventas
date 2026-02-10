export function initReportsIndex(data) {
    const { ventasSemana, topProductos, datosCaja, catProductos, balanceMensual, metodosPago } = data;

    // 1. VENTAS SEMANALES
    const ctxVentas = document.getElementById('ventasChart');
    if (ctxVentas) {
        new Chart(ctxVentas, {
            type: 'line',
            data: {
                labels: ventasSemana.map(v => v.fecha),
                datasets: [{
                    label: 'Ingresos ($)',
                    data: ventasSemana.map(v => v.total),
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#28a745'
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // 2. TOP PRODUCTOS
    const ctxProd = document.getElementById('productosChart');
    if (ctxProd) {
        new Chart(ctxProd, {
            type: 'bar',
            data: {
                labels: topProductos.map(p => p.product_name),
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: topProductos.map(p => p.total_vendido),
                    backgroundColor: ['#6366f1', '#a855f7', '#ec4899', '#f43f5e', '#f97316'],
                    borderRadius: 8
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // 3. CAJA
    const ctxCaja = document.getElementById('cajaChart');
    if (ctxCaja) {
        new Chart(ctxCaja, {
            type: 'bar',
            data: {
                labels: datosCaja.map(c => c.date),
                datasets: [
                    { label: 'M. Inicial', data: datosCaja.map(c => c.initial_amount), backgroundColor: '#cbd5e1' },
                    { label: 'M. Final', data: datosCaja.map(c => c.final_amount), backgroundColor: '#3b82f6' }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }

    // 4. CATEGORIAS
    const ctxCat = document.getElementById('categoriaChart');
    if (ctxCat) {
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: catProductos.map(c => c.name),
                datasets: [{
                    data: catProductos.map(c => c.products_count),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4']
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false, 
                plugins: { legend: { position: 'right' } } 
            }
        });
    }

    // 5. BALANCE MENSUAL
    const ctxBalance = document.getElementById('balanceChart');
    if (ctxBalance) {
        const labelsBalance = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
        new Chart(ctxBalance, {
            type: 'bar',
            data: {
                labels: labelsBalance,
                datasets: [
                    { label: 'Ingresos ($)', data: balanceMensual.map(b => b.ingresos), backgroundColor: '#3b82f6', borderRadius: 5 },
                    { label: 'Egresos (Compras) ($)', data: balanceMensual.map(b => b.egresos), backgroundColor: '#f43f5e', borderRadius: 5 }
                ]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
        });
    }

    // 6. MÉTODOS DE PAGO
    const ctxMetodos = document.getElementById('metodosChart');
    if (ctxMetodos) {
        new Chart(ctxMetodos, {
            type: 'doughnut',
            data: {
                labels: metodosPago.map(m => m.payment_type),
                datasets: [{
                    data: metodosPago.map(m => m.total),
                    backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });
    }

    // EXPORTAR A EXCEL
    const exportBtn = document.getElementById('exportExcel');
    if (exportBtn) {
        exportBtn.addEventListener('click', function() {
            let exportData = [
                ["REPORTE RESUMEN DEL SISTEMA"],
                ["Generado el:", new Date().toLocaleString()],
                [],
                ["RESUMEN FINANCIERO"],
                ["Concepto", "Monto"],
                ["Ingresos de Hoy", "$ " + data.stats.ingresoDiario],
                ["Ingresos del Mes", "$ " + data.stats.ingresoMensual],
                ["Ingresos del Año", "$ " + data.stats.ingresoAnual],
                ["Cartera por Cobrar", "$ " + data.stats.deudaTotalClientes],
                ["Créditos Pendientes", data.stats.cantidadCreditosPendientes],
                ["Inversión Total en Almacén", "$ " + data.stats.valorInversion],
                [],
                ["TOP PRODUCTOS"],
                ["Nombre", "Total Vendido"]
            ];

            topProductos.forEach(tp => {
                exportData.push([tp.product_name, tp.total_vendido]);
            });

            const ws = XLSX.utils.aoa_to_sheet(exportData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Reportes");
            XLSX.writeFile(wb, "Reporte_Estadistico.xlsx");
        });
    }
}
