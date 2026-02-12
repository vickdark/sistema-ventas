export function initPaymentNotificationsIndex(config) {
    const { routes } = config;

    const gridContainer = document.getElementById("notifications-grid");
    if (gridContainer) {
        new DataGrid("notifications-grid", {
            url: routes.index,
            columns: [
                { 
                    id: 'tenant_name', 
                    name: "Empresa / Inquilino", 
                    width: "25%",
                    formatter: (cell) => {
                        return DataGrid.html(`
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                    <i class="fa-solid fa-building small"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">${cell.name}</div>
                                    <div class="x-small text-muted">${cell.id}</div>
                                </div>
                            </div>
                        `);
                    }
                },
                { 
                    id: 'tenant_contact', 
                    name: "Contacto Empresa",
                    width: "25%",
                    formatter: (cell) => {
                        return DataGrid.html(`
                            <div class="d-flex flex-column">
                                <div class="small text-dark">
                                    <i class="fa-solid fa-envelope me-1 text-muted x-small"></i>${cell.email}
                                </div>
                                <div class="x-small text-muted">
                                    <i class="fa-solid fa-phone me-1 text-muted"></i>${cell.phone}
                                </div>
                            </div>
                        `);
                    }
                },
                { 
                    id: 'date', 
                    name: "Fecha / Hora",
                    width: "20%",
                    formatter: (cell) => {
                        return DataGrid.html(`
                            <div class="text-dark small">${cell.date}</div>
                            <div class="x-small text-muted">${cell.time}</div>
                        `);
                    }
                },
                { 
                    id: 'status', 
                    name: "Estado",
                    width: "120px",
                    formatter: (cell) => {
                        let badgeClass = 'bg-warning';
                        let text = 'Pendiente';
                        
                        if (cell === 'reviewed') {
                            badgeClass = 'bg-success';
                            text = 'Revisado';
                        } else if (cell === 'rejected') {
                            badgeClass = 'bg-danger';
                            text = 'Rechazado';
                        }
                        
                        return DataGrid.html(`
                            <div class="text-center w-100">
                                <span class="badge ${badgeClass} bg-opacity-10 text-${badgeClass.replace('bg-', '')} rounded-pill px-3">${text}</span>
                            </div>
                        `);
                    }
                },
                { 
                    id: 'actions',
                    name: "Acciones",
                    width: "180px",
                    formatter: (cell) => {
                        const data = cell;
                        
                        return DataGrid.html(`
                            <div class="d-flex justify-content-center gap-2 w-100">
                                <a href="${data.show_url}" class="btn btn-sm btn-outline-primary rounded-pill d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Ver Detalle">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                ${data.attachment ? `
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-info rounded-pill d-flex align-items-center justify-content-center" 
                                            style="width: 32px; height: 32px;"
                                            onclick="window.previewAttachment('${data.attachment}', ${data.is_pdf})"
                                            title="Previsualizar Comprobante">
                                        <i class="fa-solid fa-file-invoice-dollar"></i>
                                    </button>
                                ` : ''}
                                
                                ${data.status === 'pending' ? `
                                    <form action="${data.review_url}" method="POST" class="d-inline">
                                        <input type="hidden" name="_token" value="${config.tokens.csrf}">
                                        <button type="submit" class="btn btn-sm btn-outline-success rounded-pill d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Marcar como Revisado">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                ` : ''}

                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill d-flex align-items-center justify-content-center" 
                                        style="width: 32px; height: 32px;"
                                        onclick="window.deleteNotification('${data.delete_url}')" title="Eliminar">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        `);
                    }
                }
            ],
            mapData: (n) => [
                { name: n.tenant_name, id: n.tenant_id },
                n.tenant_contact,
                { date: n.date, time: n.time },
                n.status,
                n // Objeto completo para acciones
            ]
        }).render();
    }

    window.previewAttachment = function(url, isPdf) {
        let content = '';
        if (isPdf) {
            content = `<iframe src="${url}" style="width: 100%; height: 500px; border: none;" class="rounded-3"></iframe>`;
        } else {
            content = `<img src="${url}" class="img-fluid rounded-3 shadow-sm" style="max-height: 500px; width: auto; display: block; margin: 0 auto;">`;
        }

        window.Swal.fire({
            title: '<i class="fa-solid fa-image me-2 text-primary"></i>Comprobante de Pago',
            html: content,
            width: isPdf ? '800px' : 'auto',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                popup: 'rounded-4 border-0 shadow-lg p-3'
            }
        });
    };

    window.deleteNotification = async function(url) {
        const confirmed = await window.Notify.confirm({
            title: '¿Eliminar notificación?',
            text: 'Esta acción no se puede deshacer.',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (confirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = url;
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = config.tokens.csrf;
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    };
}
