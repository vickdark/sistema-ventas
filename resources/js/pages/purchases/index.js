import DataGrid from '../../modules/DataGrid';
import Notifications from '../../modules/Notifications';

export function initPurchasesIndex(config) {
    const { routes, tokens } = config;

    const grid = new DataGrid("wrapper", {
        url: routes.index,
        columns: [
            { id: 'id', name: "ID", width: "60px" },
            { id: 'nro_compra', name: "Nro. Compra", width: "120px" },
            { id: 'supplier', name: "Proveedor" },
            { id: 'product', name: "Producto" },
            { id: 'quantity', name: "Cant.", width: "80px" },
            { id: 'total', name: "Total", width: "120px" },
            { id: 'purchase_date', name: "Fecha", width: "120px" },
            { 
                id: 'actions',
                name: "Acciones",
                formatter: (cell, row) => {
                    const id = row.cells[0].data;
                    const showUrl = routes.show.replace(':id', id);
                    const editUrl = routes.edit.replace(':id', id);
                    const deleteUrl = routes.destroy.replace(':id', id);
                    
                    return DataGrid.html(`
                        <div class="btn-group">
                            <a href="${showUrl}" class="btn btn-sm btn-outline-info rounded-pill me-2" title="Ver Detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="${routes.index}/${id}/voucher" target="_blank" class="btn btn-sm btn-outline-warning rounded-pill me-2" title="Imprimir Comprobante">
                                <i class="fas fa-print"></i>
                            </a>
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
        mapData: (purchase) => {
            // Calcular proveedores únicos
            let suppliers = [];
            if (purchase.items && purchase.items.length > 0) {
                const suppliersMap = new Map();
                purchase.items.forEach(item => {
                    if (item.product && item.product.suppliers) {
                        item.product.suppliers.forEach(s => suppliersMap.set(s.id, s.name));
                    }
                });
                suppliers = Array.from(suppliersMap.values());
            }

            if (suppliers.length === 0 && purchase.supplier) {
                suppliers = [purchase.supplier.name];
            }

            const supplierDisplay = suppliers.length > 1 
                ? `${suppliers[0]} (+${suppliers.length - 1})` 
                : (suppliers[0] || 'N/A');

            return [
                purchase.id, 
                purchase.nro_compra,
                supplierDisplay,
                purchase.items && purchase.items.length > 0 
                    ? (purchase.items.length > 1 
                        ? `${purchase.items[0].product.name} (+${purchase.items.length - 1})` 
                        : purchase.items[0].product.name)
                    : (purchase.product ? purchase.product.name : 'N/A'),
                purchase.items && purchase.items.length > 0 
                    ? purchase.items.reduce((sum, item) => sum + item.quantity, 0)
                    : purchase.quantity,
                `$${(purchase.total || (purchase.quantity * purchase.price)).toLocaleString()}`,
                purchase.purchase_date,
                null
            ];
        }
    }).render();

    window.deletePurchase = async function(url) {
        const confirmed = await Notify.confirm({
            title: '¿Eliminar compra?',
            text: 'Esta acción revertirá el stock del producto.',
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
                    Notify.success('Eliminada', 'La compra ha sido eliminada y el stock actualizado.');
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
