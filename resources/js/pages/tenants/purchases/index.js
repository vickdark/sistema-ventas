import CrudManager from '../../../modules/CrudManager';
import { getColumns, mapData } from './columns';

export function initPurchasesIndex(config) {
    new CrudManager(config, {
        columns: getColumns(config.routes),
        mapData: mapData,
        deleteMessage: {
            title: '¿Eliminar compra?',
            text: 'Esta acción revertirá el stock del producto.'
        }
    }).init();

    // Check for new purchase data and show print dialog
    const newPurchaseData = document.getElementById('new-purchase-data');
    if (newPurchaseData) {
        const voucherUrl = newPurchaseData.getAttribute('data-voucher-url');
        
        Swal.fire({
            title: 'Compra Registrada',
            text: "¿Desea imprimir el comprobante de ingreso?",
            icon: 'success',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-print"></i> Imprimir',
            cancelButtonText: 'Cerrar',
            confirmButtonColor: '#ffc107', 
            cancelButtonColor: '#858796',
        }).then((result) => {
            if (result.isConfirmed) {
                window.open(voucherUrl, '_blank');
            }
        });
    }
}
