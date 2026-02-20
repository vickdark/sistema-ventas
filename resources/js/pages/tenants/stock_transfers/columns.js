import DataGrid from "../../../modules/DataGrid";

export const columns = [
    { id: 'id', name: 'ID', hidden: true },
    { id: 'nro_traslado', name: 'Nro.' },
    { id: 'origin', name: 'Origen' },
    { id: 'destination', name: 'Destino' },
    { 
        id: 'status', 
        name: 'Estado',
        formatter: (cell) => {
            let color = 'warning';
            if (cell === 'RECIBIDO') color = 'success';
            if (cell === 'ENVIADO') color = 'primary';
            if (cell === 'CANCELADO') color = 'danger';
            return DataGrid.html(`<span class="badge bg-${color}">${cell}</span>`);
        }
    },
    { id: 'date', name: 'Fecha/Envío' },
    { id: 'actions', name: 'Acciones' }
];

export const mapData = (t) => [
    t.id,
    t.nro_traslado,
    t.origin,
    t.destination,
    t.status,
    t.date,
    null
];
