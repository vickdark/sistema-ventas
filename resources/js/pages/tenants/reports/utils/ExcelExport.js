import * as XLSX from 'xlsx';

export const initExcelExport = (data, topProductos) => {
    const exportBtn = document.getElementById('exportExcel');
    if (!exportBtn) return;

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
};
