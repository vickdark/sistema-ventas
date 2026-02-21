export const columns = [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'name', name: "Nombre" },
    { id: 'email', name: "Email" },
    { id: 'role', name: "Rol" },
    { 
        id: 'status', 
        name: "Estado",
        formatter: (cell) => {
            const isActive = cell;
            return window.Gridjs.html(`
                <span class="badge ${isActive ? 'bg-success' : 'bg-danger'}">
                    ${isActive ? 'Activo' : 'Inactivo'}
                </span>
            `);
        }
    },
    { id: 'actions', name: "Acciones" } // El manager inyectará automáticamente los botones
];

export const mapData = (u) => [
    u.id, 
    u.name, 
    u.email, 
    u.role ? u.role.nombre : 'Sin Rol',
    u.is_active,
    null
];
