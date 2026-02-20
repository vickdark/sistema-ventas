
export const getColumns = (config) => [
    { id: 'date', name: 'Fecha' },
    { id: 'user', name: 'Usuario', hidden: !config.is_admin },
    { 
        id: 'clock_in', 
        name: 'Entrada',
        formatter: (cell) => formatTime(cell)
    },
    { 
        id: 'clock_out', 
        name: 'Salida',
        formatter: (cell) => cell ? formatTime(cell) : '<span class="badge bg-warning text-dark">ACTIVO</span>',
        html: true
    },
    { 
        id: 'status', 
        name: 'Estado',
        formatter: (cell) => {
            const colors = { present: 'success', late: 'warning', absent: 'danger', early_leave: 'info' };
            return `<span class="badge bg-${colors[cell] || 'secondary'}">${cell.toUpperCase()}</span>`;
        },
        html: true
    },
    { id: 'notes', name: 'Notas' }
];

export const mapData = (item) => [
    item.date,
    item.user.name,
    item.clock_in,
    item.clock_out,
    item.status,
    item.notes
];

function formatTime(isoString) {
    if (!isoString) return '--:--';
    return new Date(isoString).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}
