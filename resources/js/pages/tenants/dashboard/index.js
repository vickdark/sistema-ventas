import Notifications from '../../../modules/Notifications';

export function initDashboardIndex(config) {
    const elements = {
        statusLabel: document.getElementById('dashboardAttendanceStatus'),
        btnClock: document.getElementById('btnDashboardClock')
    };

    if (!elements.statusLabel || !elements.btnClock) return;

    let currentAttendance = null;

    async function loadStatus() {
        try {
            const response = await axios.get(config.routes.status);
            updateWidget(response.data);
        } catch (error) {
            console.error('Error loading attendance status:', error);
            if (elements.statusLabel) elements.statusLabel.textContent = 'Error';
        }
    }

    function updateWidget(data) {
        currentAttendance = data.active_shift;

        if (data.is_clocked_in) {
            elements.statusLabel.innerHTML = '<span class="text-success">TRABAJANDO</span>';
            elements.btnClock.textContent = 'MARCAR SALIDA';
            elements.btnClock.classList.remove('btn-outline-primary', 'disabled');
            elements.btnClock.classList.add('btn-outline-danger');
            elements.btnClock.disabled = false;
            elements.btnClock.onclick = () => confirmClockOut();
        } else {
            elements.statusLabel.innerHTML = '<span class="text-muted">NO ACTIVO</span>';
            elements.btnClock.textContent = 'MARCAR ENTRADA';
            elements.btnClock.classList.remove('btn-outline-danger', 'disabled');
            elements.btnClock.classList.add('btn-outline-primary');
            elements.btnClock.disabled = false;
            elements.btnClock.onclick = () => confirmClockIn();
        }
    }

    async function confirmClockIn() {
        // Simple confirmation
        const confirmed = await Notifications.confirm({
            title: '¿Iniciar Jornada?',
            text: 'Se registrará tu hora de entrada.',
            confirmButtonText: 'Sí, iniciar'
        });

        if (confirmed) {
            performAction(config.routes.clock_in, 'POST');
        }
    }

    async function confirmClockOut() {
        // Simple confirmation with notes
        const { value: notes } = await Swal.fire({
            title: '¿Finalizar Jornada?',
            input: 'textarea',
            inputPlaceholder: 'Notas (opcional)...',
            showCancelButton: true,
            confirmButtonText: 'Finalizar',
            cancelButtonText: 'Cancelar'
        });

        if (notes !== undefined) {
            const url = config.routes.clock_out.replace('FAKE_ID', currentAttendance.id);
            performAction(url, 'PUT', { notes });
        }
    }

    async function performAction(url, method, body = {}) {
        elements.btnClock.disabled = true;
        elements.btnClock.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        try {
            const response = await axios({
                method: method.toLowerCase(),
                url: url,
                data: body
            });

            if (response.data) {
                Notifications.success('Éxito', response.data.message);
                loadStatus();
            }
        } catch (error) {
            const message = error.response?.data?.message || 'Error de conexión';
            Notifications.error('Error', message);
            loadStatus();
        }
    }

    // Init
    loadStatus();
}
