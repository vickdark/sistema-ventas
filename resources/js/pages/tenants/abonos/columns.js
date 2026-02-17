import DataGrid from '../../../modules/DataGrid';

export const debtorsColumns = [
    { id: 'id', name: "ID", width: "60px" },
    { id: 'name', name: "Cliente" },
    { id: 'nit_ci', name: "NIT/CI" },
    { 
        id: 'sales_count', 
        name: "Facturas Pendientes",
        formatter: (cell) => DataGrid.html(`<span class="badge bg-danger rounded-pill">${cell} pendientes</span>`)
    },
    { 
        id: 'total_debt', 
        name: "Saldo Total",
        formatter: (cell) => DataGrid.html(`<span class="fw-bold text-primary">$${parseFloat(cell).toLocaleString()}</span>`)
    },
    { 
        id: 'actions', 
        name: "Acciones",
        formatter: (cell, row, config) => { // Hack: pasamos config como contexto si fuera necesario, pero aquí usamos row
            // Nota: Aquí necesitamos acceso a `routes` que no tenemos directamente en este scope estático
            // Solución: Dejaremos que el formatter se defina en el index.js o pasaremos rutas al generar columnas
            // Pero CrudManager espera columnas estáticas o función.
            return null; // Lo definiremos dinámicamente en index.js para tener acceso a routes
        }
    }
];

export const historyColumns = [
    { id: 'id', name: "ID", width: "60px" },
    { id: 'client', name: "Cliente" },
    { id: 'sale', name: "Nro. Venta" },
    { 
        id: 'payment_type', 
        name: "Tipo Pago",
        formatter: (cell) => {
            const type = cell || 'CONTADO';
            const badgeClass = type === 'CONTADO' ? 'bg-success' : 'bg-primary';
            const badgeText = type === 'CONTADO' ? 'Efectivo' : 'Transferencia';
            return DataGrid.html(`<span class="badge ${badgeClass} rounded-pill">${badgeText}</span>`);
        }
    },
    { id: 'amount', name: "Monto" },
    { id: 'created_at', name: "Fecha" },
    { id: 'actions', name: "Acciones" }
];

export const mapDebtors = (client) => [
    client.id, 
    client.name,
    client.nit_ci,
    client.sales_count,
    client.total_debt,
    null
];

export const mapHistory = (abono) => [
    abono.id, 
    abono.client ? abono.client.name : 'N/A',
    abono.sale ? `#${abono.sale.nro_venta}` : 'PAGO GENERAL',
    abono.payment_type,
    `$${parseFloat(abono.amount).toLocaleString()}`,
    new Date(abono.created_at).toLocaleDateString(),
    null
];
