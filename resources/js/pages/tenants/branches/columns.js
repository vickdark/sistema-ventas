import { html } from 'gridjs';

export const columns = [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'name', name: "Nombre" },
    { id: 'address', name: "Dirección" },
    { id: 'phone', name: "Teléfono" },
    { id: 'email', name: "Email" },
    { 
        id: 'is_main', 
        name: "Principal",
        formatter: (cell) => html(cell 
            ? '<span class="badge bg-success rounded-pill">Principal</span>' 
            : '<span class="badge bg-light text-dark rounded-pill">Secundaria</span>')
    },
    { id: 'actions', name: "Acciones" }
];

export const mapData = (b) => [
    b.id, 
    b.name, 
    b.address || 'N/A',
    b.phone || 'N/A',
    b.email || 'N/A',
    b.is_main,
    null
];
