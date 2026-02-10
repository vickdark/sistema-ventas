import DataGrid from '../../modules/DataGrid';
import Notifications from '../../modules/Notifications';

export function initClientsIndex(config) {
    const { routes, tokens } = config;

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'id', name: "ID", width: "80px" },
            { id: 'nombre', name: "Nombre" },
            { id: 'apellido', name: "Apellido" },
            { id: 'email', name: "Email" },
            { id: 'telefono', name: "Teléfono" },
            { 
                id: 'actions',
                name: "Acciones",
                formatter: (cell, row) => {
                    const id = row.cells[0].data;
                    const editUrl = routes.edit.replace(':id', id);
                    const deleteUrl = routes.destroy.replace(':id', id);
                    
                    return DataGrid.html(`
                        <div class="btn-group">
                            <a href="${editUrl}" class="btn btn-sm btn-outline-secondary rounded-pill me-2" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" 
                                class="btn btn-sm btn-outline-danger rounded-pill" 
                                onclick="window.deleteClient('${deleteUrl}')"
                                title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `);
                }
            }
        ],
        mapData: (client) => [
            client.id, 
            client.nombre, 
            client.apellido,
            client.email,
            client.telefono,
            null
        ]
    }).render();

    window.deleteClient = async function(url) {
        const confirmed = await Notify.confirm({
            title: '¿Eliminar cliente?',
            text: 'Esta acción no se puede deshacer.',
            confirmButtonText: 'Sí, eliminar',
            confirmButtonColor: '#e74a3b'
        });

        if (confirmed) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': tokens.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ _method: 'DELETE' })
                });

                const result = await response.json();

                if (response.ok) {
                    Notify.success('Eliminado', 'El cliente ha sido eliminado correctamente.');
                    window.location.reload(); 
                } else {
                    Notify.error('Error', result.message || 'No se pudo eliminar el cliente.');
                }
            } catch (error) {
                Notify.error('Error', 'Ocurrió un error inesperado.');
                console.error(error);
            }
        }
    };
}
