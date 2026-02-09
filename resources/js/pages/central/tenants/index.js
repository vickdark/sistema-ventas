export function initTenantsIndex(config) {
    const { routes, tokens } = config;

    const grid = new DataGrid("tenants-grid", {
        url: routes.index,
        columns: [
            { id: 'id', name: "Empresa / ID", width: "200px" },
            { 
                id: 'domain', 
                name: "Dominio de Acceso",
                formatter: (cell) => DataGrid.html(`<a href="http://${cell}" target="_blank" class="text-primary text-decoration-none fw-bold"><i class="fas fa-external-link-alt me-1 small"></i>${cell}</a>`)
            },
            { 
                id: 'database', 
                name: "Base de Datos",
                formatter: (cell) => DataGrid.html(`<span class="badge bg-light text-dark border"><i class="fas fa-database me-1 small opacity-50"></i>${cell}</span>`)
            },
            { 
                id: 'actions',
                name: "Acciones",
                formatter: (cell, row) => {
                    const id = row.cells[0].data;
                    const editUrl = routes.edit.replace(':id', id);
                    const deleteUrl = routes.destroy.replace(':id', id);
                    
                    return DataGrid.html(`
                        <div class="btn-group">
                            <a href="${editUrl}" class="btn btn-sm btn-outline-primary rounded-pill me-2" title="Editar Empresa">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <button type="button" 
                                class="btn btn-sm btn-outline-danger rounded-pill" 
                                onclick="window.deleteTenant('${deleteUrl}', '${id}')"
                                title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `);
                }
            }
        ],
        mapData: (t) => [
            t.id, 
            t.domains && t.domains.length > 0 ? t.domains[0].domain : 'N/A',
            t.tenancy_db_name || `${config.db_prefix}_${t.id}`,
            null
        ]
    }).render();

    window.deleteTenant = async function(url, id) {
        const confirmed = await Notify.confirm({
            title: '¿Eliminar Inquilino?',
            text: `Esta acción eliminará permanentemente la empresa "${id}" y TODO su contenido (Base de datos incluida).`,
            confirmButtonText: 'Sí, eliminar permanentemente',
            confirmButtonColor: '#e74a3b'
        });

        if (confirmed) {
            try {
                Notify.loading('Eliminando inquilino y recursos...');
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
                    Notify.success('Eliminado', 'El inquilino ha sido eliminado correctamente.');
                    window.location.reload(); 
                } else {
                    Notify.error('Error', result.message || 'No se pudo eliminar el inquilino.');
                }
            } catch (error) {
                Notify.error('Error', 'Ocurrió un error inesperado.');
                console.error(error);
            }
        }
    };
}
