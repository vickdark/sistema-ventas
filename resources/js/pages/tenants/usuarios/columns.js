export const columns = [
    { id: 'id', name: "ID", width: "80px" },
    { id: 'name', name: "Nombre" },
    { id: 'email', name: "Email" },
    { id: 'role', name: "Rol" },
    { id: 'actions', name: "Acciones" } // El manager inyectará automáticamente los botones
];

export const mapData = (u) => [
    u.id, 
    u.name, 
    u.email, 
    u.role ? u.role.nombre : 'Sin Rol',
    null
];
