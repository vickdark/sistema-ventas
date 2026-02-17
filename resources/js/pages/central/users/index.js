import CrudManager from '../../../modules/CrudManager';
import Notifications from '../../../modules/Notifications';
import { getColumns, mapData } from './columns';

export function initUsersIndex(config) {
    const manager = new CrudManager(config, {
        columns: getColumns(config.routes),
        mapData: mapData,
        deleteMessage: {
            title: '¿Eliminar Usuario?',
            text: 'Esta acción es IRREVERSIBLE.'
        }
    });

    manager.init();

    initCustomHandlers(config.tokens.csrf);
}

function initCustomHandlers(csrfToken) {
    const container = document.getElementById('wrapper');
    if (!container) return;

    container.addEventListener('click', async (e) => {
        const btnResend = e.target.closest('.btn-resend-verification');
        
        if (btnResend) {
            e.preventDefault();
            const userId = btnResend.dataset.id;
            const url = btnResend.dataset.url;
            
            await handleResendVerification(userId, url, csrfToken);
        }
    });
}

async function handleResendVerification(userId, url, csrfToken) {
    const confirmed = await Notifications.confirm({
        title: '¿Reenviar correo de verificación?',
        text: 'Se enviará un nuevo correo de verificación a este usuario.',
        confirmButtonText: 'Sí, reenviar',
        confirmButtonColor: '#28a745'
    });

    if (confirmed) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ user_id: userId })
            });

            const result = await response.json();

            if (response.ok) {
                Notifications.success('Enviado', result.message || 'Correo de verificación reenviado correctamente.');
            } else {
                Notifications.error('Error', result.message || 'No se pudo reenviar el correo de verificación.');
            }
        } catch (error) {
            console.error('Error:', error);
            Notifications.error('Error', 'Ocurrió un error al intentar reenviar el correo.');
        }
    }
}
