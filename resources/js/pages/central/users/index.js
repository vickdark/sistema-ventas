export function initUsersIndex(containerId, config) {
    const { routes, tokens } = config;

    const grid = new DataGrid(containerId, {
        url: routes.index,
        columns: [
            { id: 'id', name: "ID", width: "80px" },
            { id: 'name', name: "Nombre" },
            { id: 'email', name: "Email" },
            { 
                id: 'email_verified_at', 
                name: "Verificación Email",
                formatter: (cell) => {
                    const isVerified = cell !== null;
                    const badgeClass = isVerified ? 'bg-success' : 'bg-warning text-dark';
                    const text = isVerified ? 'Verificado' : 'Pendiente';
                    return DataGrid.html(`<span class="badge ${badgeClass} rounded-pill">${text}</span>`);
                }
            },
            {
                id: 'resend_verification',
                name: "Reenviar Verificación",
                formatter: (cell, row) => {
                    const id = row.cells[0].data;
                    const emailVerifiedAt = row.cells[3].data; // Assuming email_verified_at is the 4th column (index 3)
                    if (emailVerifiedAt === null) {
                        return DataGrid.html(`
                            <button type="button"
                                class="btn btn-sm btn-outline-info rounded-pill"
                                onclick="window.resendVerification('${id}', '${routes.resendVerification}')"
                                title="Reenviar Correo de Verificación">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        `);
                    }
                    return '';
                }
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
                            <a href="${editUrl}" class="btn btn-sm btn-outline-secondary rounded-pill me-2" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button"
                                class="btn btn-sm btn-outline-danger rounded-pill"
                                onclick="window.deleteUser('${deleteUrl}')"
                                title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `);
                }
            }
        ],
        mapData: (u) => [
            u.id,
            u.name,
            u.email,
            u.email_verified_at,
            null, // For the new 'resend_verification' column
            null
        ]
    }).render();

    window.resendVerification = async function(userId, url) {
        const confirmed = await Notify.confirm({
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
                        'X-CSRF-TOKEN': tokens.csrf,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ user_id: userId })
                });

                const result = await response.json();

                if (response.ok) {
                    Notify.success('Enviado', result.message || 'Correo de verificación reenviado correctamente.');
                } else {
                    Notify.error('Error', result.message || 'No se pudo reenviar el correo de verificación.');
                }
            } catch (error) {
                Notify.error('Error', 'Ocurrió un error inesperado al reenviar el correo.');
                console.error(error);
            }
        }
    };

    window.deleteUser = async function(url) {
        const confirmed = await Notify.confirm({
            title: '¿Eliminar usuario?',
            text: 'Esta acción no se puede deshacer y el usuario perderá acceso al sistema.',
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
                    Notify.success('Eliminado', 'El usuario ha sido eliminado correctamente.');
                    window.location.reload();
                } else {
                    Notify.error('Error', result.message || 'No se pudo eliminar el usuario.');
                }
            } catch (error) {
                Notify.error('Error', 'Ocurrió un error inesperado.');
                console.error(error);
            }
        }
    };
}
