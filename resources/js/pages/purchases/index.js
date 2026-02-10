import DataGrid from '../../modules/DataGrid';
import Notifications from '../../modules/Notifications';

export function initPurchasesIndex(config) {
    const { routes, tokens } = config;

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'id', name: "ID", width: "80px" },
            { id: 'supplier_name', name: "Proveedor" },
            { id: 'total', name: "Total" },
            { id: 'fecha', name: "Fecha" },
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
                                onclick="window.deletePurchase('${deleteUrl}')"
                                title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `);
                }
            }
        ],
        mapData: (purchase) => [
            purchase.id, 
            purchase.supplier ? purchase.supplier.nombre : 'N/A', 
            purchase.total,
            purchase.fecha,
            null
        ]
    }).render();

    window.deletePurchase = async function(url) {
        const confirmed = await Notify.confirm({
            title: '¿Eliminar compra?',
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
                    Notify.success('Eliminada', 'La compra ha sido eliminada correctamente.');
                    window.location.reload(); 
                } else {
                    Notify.error('Error', result.message || 'No se pudo eliminar la compra.');
                }
            } catch (error) {
                Notify.error('Error', 'Ocurrió un error inesperado.');
                console.error(error);
            }
        }
    };
}
