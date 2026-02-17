import { configureChartDefaults } from './utils/ChartConfig';
import { initVentasChart } from './charts/VentasChart';
import { initProductosChart } from './charts/ProductosChart';
import { initCajaChart } from './charts/CajaChart';
import { initCategoriaChart } from './charts/CategoriaChart';
import { initBalanceChart } from './charts/BalanceChart';
import { initMetodosPagoChart } from './charts/MetodosPagoChart';
import { initEfectivoTransferenciaChart } from './charts/EfectivoTransferenciaChart';
import { initExcelExport } from './utils/ExcelExport';

export function initReportsIndex(data) {
    const { 
        ventasSemana, 
        topProductos, 
        datosCaja, 
        catProductos, 
        balanceMensual, 
        metodosPago, 
        efectivoVsTransferencia 
    } = data;

    // 1. Configuración Global
    configureChartDefaults();

    // 2. Inicializar Gráficos
    initVentasChart(ventasSemana);
    initProductosChart(topProductos);
    initCajaChart(datosCaja);
    initCategoriaChart(catProductos);
    initBalanceChart(balanceMensual);
    initMetodosPagoChart(metodosPago);
    initEfectivoTransferenciaChart(efectivoVsTransferencia);

    // 3. Inicializar Exportación
    initExcelExport(data, topProductos);
}
