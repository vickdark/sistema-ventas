import DataGrid from "../../../modules/DataGrid";

export const columns = [
    { id: 'id', name: 'ID', hidden: true },
    { id: 'nro_compra', name: 'Compra' },
    { id: 'supplier', name: 'Proveedor' },
    { id: 'total_amount', name: 'Total', formatter: (cell) => `$${parseFloat(cell).toFixed(2)}` },
    { id: 'pending_amount', name: 'Pendiente', formatter: (cell) => DataGrid.html(`<span class="text-danger fw-bold">$${parseFloat(cell).toFixed(2)}</span>`) },
    { id: 'due_date', name: 'Vencimiento', formatter: (cell) => cell ? new Date(cell).toLocaleDateString() : '-' },
    { id: 'actions', name: 'Acciones' }
];

export const mapData = (p) => [
    p.id,
    p.nro_compra,
    p.supplier.name,
    p.total_amount,
    p.pending_amount,
    p.due_date,
    null
];
