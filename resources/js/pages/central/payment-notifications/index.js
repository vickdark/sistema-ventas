import CrudManager from '../../../modules/CrudManager';
import Notifications from '../../../modules/Notifications';
import { getColumns, mapData } from './columns';

export function initPaymentNotificationsIndex(config) {
    const manager = new CrudManager(config, {
        columns: getColumns(config.routes),
        mapData: mapData,
        deleteMessage: {
            title: '¿Eliminar Notificación?',
            text: 'Esta acción es IRREVERSIBLE.'
        }
    });

    manager.init();

    initCustomHandlers();
}

function initCustomHandlers() {
    const container = document.getElementById('wrapper');
    if (!container) return;

    container.addEventListener('click', (e) => {
        const btnPreview = e.target.closest('.btn-preview');
        
        if (btnPreview) {
            e.preventDefault();
            const attachment = btnPreview.dataset.attachment;
            const isPdf = btnPreview.dataset.isPdf === 'true';
            
            previewAttachment(attachment, isPdf);
        }
    });
}

function previewAttachment(url, isPdf) {
    let content = '';
    
    if (isPdf) {
        content = `<iframe src="${url}" style="width:100%; height:500px;" frameborder="0"></iframe>`;
    } else {
        content = `<img src="${url}" class="img-fluid rounded" style="max-height: 500px;">`;
    }

    Notifications.fire({
        title: 'Comprobante de Pago',
        html: content,
        width: '600px',
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            popup: 'rounded-4 shadow-lg'
        }
    });
}
