
import DataGrid from '../../../modules/DataGrid';

export const getColumns = (config) => [
    { 
        id: 'date', 
        name: 'Fecha',
        width: '100px',
        formatter: (cell) => formatDate(cell)
    },
    { id: 'user', name: 'Usuario', hidden: !config.is_admin, minWidth: '150px' },
    { 
        id: 'clock_in', 
        name: 'Entrada',
        width: '100px',
        formatter: (cell) => formatTime(cell)
    },
    { 
        id: 'clock_out', 
        name: 'Salida',
        width: '120px',
        formatter: (cell) => cell ? formatTime(cell) : DataGrid.html('<span class="badge bg-warning text-dark">ACTIVO</span>'),
        html: true
    },
    { 
        id: 'status', 
        name: 'Estado',
        width: '180px',
        formatter: (cell, row) => {
            // Check if clock_out (index 3) is present
            const clockOut = row.cells[3].data;
            
            if (!clockOut) {
                return DataGrid.html('<span class="badge bg-success">OPERANDO</span>');
            } else {
                return DataGrid.html('<span class="badge bg-secondary">JORNADA TERMINADA</span>');
            }
        },
        html: true
    },
    { 
        id: 'notes', 
        name: 'Notas', 
        minWidth: '200px',
        formatter: (cell) => DataGrid.html(`<div class="text-wrap" style="max-width: 300px;">${cell || ''}</div>`)
    }
];

export const mapData = (item) => [
    item.date,
    item.user ? item.user.name : 'N/A',
    item.clock_in,
    item.clock_out,
    item.status,
    item.notes
];

function formatTime(isoString) {
    if (!isoString) return '--:--';
    return new Date(isoString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function formatDate(isoString) {
    if (!isoString) return '--/--/----';
    return new Date(isoString).toLocaleDateString();
}
