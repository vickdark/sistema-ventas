export function initForgotPassword() {
    const page = document.getElementById('forgot-password-page');
    if (!page) return;

    // Manejar alerta de rol restringido
    const restrictedRole = page.dataset.restrictedRole;
    if (restrictedRole && window.Swal) {
        window.Swal.fire({
            icon: 'warning',
            title: 'Acceso Restringido',
            text: restrictedRole,
            confirmButtonColor: '#4e73df',
            confirmButtonText: 'Entendido'
        });
    }

    // Manejar envío del formulario
    const form = document.getElementById('forgotPasswordForm');
    if (form) {
        form.addEventListener('submit', function() {
            const formActions = document.getElementById('formActions');
            const loadingAlert = document.getElementById('loadingAlert');
            
            // Ocultar botones y mostrar alerta de carga
            if (formActions) formActions.classList.add('d-none');
            if (loadingAlert) loadingAlert.classList.remove('d-none');
        });
    }
}
