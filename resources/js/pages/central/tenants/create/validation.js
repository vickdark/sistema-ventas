export function initValidation(config) {
    const idInput = document.getElementById('id');
    const urlPreview = document.getElementById('url-preview');
    const dbPreview = document.getElementById('db-preview');
    const statusLabel = document.getElementById('id-status');
    const submitBtn = document.getElementById('btn-submit');

    let timeout = null;
    const centralDb = config.centralDb;
    const host = config.host;
    const checkUrl = config.routes.check;

    if (idInput) {
        idInput.addEventListener('input', function() {
            const value = this.value.toLowerCase().replace(/[^\w-]/g, '');
            this.value = value;
            
            if (value) {
                urlPreview.innerHTML = `<span class="text-primary">${value}</span>${host}`;
                dbPreview.innerHTML = `${centralDb}_<span class="text-warning">${value}</span>`;
            } else {
                urlPreview.innerHTML = `<span class="text-primary">...</span>${host}`;
                dbPreview.innerHTML = `${centralDb}_<span class="text-warning">...</span>`;
                statusLabel.innerHTML = '';
                return;
            }

            clearTimeout(timeout);
            statusLabel.innerHTML = '<i class="fas fa-spinner fa-spin text-muted"></i>';
            
            timeout = setTimeout(async () => {
                try {
                    const response = await fetch(`${checkUrl}?id=${value}`);
                    const data = await response.json();
                    if (data.available) {
                        statusLabel.innerHTML = '<i class="fas fa-check-circle text-success me-1"></i> Disponible';
                        statusLabel.className = 'small fw-bold text-success';
                        idInput.classList.remove('is-invalid');
                        idInput.classList.add('is-valid');
                        submitBtn.disabled = false;
                    } else {
                        statusLabel.innerHTML = `<i class="fas fa-times-circle text-danger me-1"></i> ${data.message || 'No disponible'}`;
                        statusLabel.className = 'small fw-bold text-danger';
                        idInput.classList.add('is-invalid');
                        idInput.classList.remove('is-valid');
                        submitBtn.disabled = true;
                    }
                } catch (error) { console.error(error); }
            }, 500);
        });
    }
}
