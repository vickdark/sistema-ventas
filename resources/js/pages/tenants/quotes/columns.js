import DataGrid from "../../../modules/DataGrid";

export const columns = [
    { id: 'id', name: 'ID', hidden: true },
    { id: 'nro_cotizacion', name: 'Nro.' },
    { id: 'client', name: 'Cliente' },
    { 
        id: 'status', 
        name: 'Estado',
        formatter: (cell) => {
            let color = 'warning';
            if (cell === 'CONVERTIDA') color = 'success';
            if (cell === 'VENCIDA') color = 'danger';
            return DataGrid.html(`<span class="badge bg-${color}">${cell}</span>`);
        }
    },
    { id: 'total', name: 'Total', formatter: (cell) => `$${parseFloat(cell).toFixed(2)}` },
    { id: 'date', name: 'Fecha' },
    { id: 'actions', name: 'Acciones' }
];

export const mapData = (q) => [
    q.id,
    q.nro_cotizacion,
    q.client,
    q.status,
    q.total,
    q.date,
    null
];
