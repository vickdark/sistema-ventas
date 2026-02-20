
export const columns = [
    { id: 'date', name: 'Fecha' },
    { id: 'description', name: 'Descripción', width: '30%' },
    { id: 'reference', name: 'Ref.', hidden: true },
    { 
        id: 'details', 
        name: 'Detalle Contable (Debe / Haber)',
        width: '40%',
        formatter: (cell) => {
            // Cell es array de detalles
            let html = '<div class="table-responsive"><table class="table table-sm table-borderless mb-0 small">';
            cell.forEach(detail => {
                const debit = parseFloat(detail.debit || 0);
                const credit = parseFloat(detail.credit || 0);
                const amount = debit > 0 ? debit : credit;
                const type = debit > 0 ? 'Debe' : 'Haber';
                const color = debit > 0 ? 'text-primary' : 'text-danger';
                const indent = debit > 0 ? '' : 'ps-3';
                
                html += `
                    <tr>
                        <td class="${indent}">
                            <span class="fw-bold text-muted">${detail.account?.code || '??'}</span> 
                            ${detail.account?.name || 'Cuenta Desc.'}
                        </td>
                        <td class="text-end ${color}">${amount.toFixed(2)}</td>
                    </tr>
                `;
            });
            html += '</table></div>';
            return html;
        },
        html: true
    },
    { id: 'user', name: 'Usuario', hidden: true }
];

export const mapData = (item) => [
    item.date,
    item.description,
    item.reference_number,
    item.details,
    item.user?.name
];
