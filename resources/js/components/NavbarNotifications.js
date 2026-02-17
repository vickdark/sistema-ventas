export default class NavbarNotifications {
    constructor(url) {
        this.url = url;
        this.btn = document.getElementById('notificationBtn');
        this.dot = document.getElementById('notificationDot');
        this.list = document.getElementById('notificationList');
        
        if (this.btn && this.list) {
            this.init();
        }
    }

    init() {
        this.fetchNotifications();
        // Polling interaction every 2 minutes
        setInterval(() => this.fetchNotifications(), 120000);
        
        // Refresh on click just in case
        this.btn.addEventListener('click', () => {
            this.fetchNotifications();
        });
    }

    async fetchNotifications() {
        try {
            const response = await fetch(this.url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                if (response.status === 403 || response.status === 401) {
                    // Si no tiene permiso o sesión expiró, ocultar o mostrar mensaje discreto
                    this.renderError('Sin acceso');
                } else {
                    this.renderError();
                }
                return;
            }

            const data = await response.json();
            this.render(data);
        } catch (error) {
            console.error('Error fetching notifications:', error);
            this.renderError();
        }
    }

    render(data) {
        const { count, products } = data;

        // Update badge
        if (count > 0) {
            this.dot.classList.remove('d-none');
            this.dot.classList.add('d-block');
        } else {
            this.dot.classList.add('d-none');
            this.dot.classList.remove('d-block');
        }

        // Render List
        if (products.length === 0) {
            this.list.innerHTML = `
                <div class="text-center py-5">
                    <i class="fa-regular fa-circle-check fa-2x text-muted mb-2 opacity-50"></i>
                    <p class="text-muted small mb-0">Todo en orden. Stock saludable.</p>
                </div>
            `;
            return;
        }

        let html = '';
        products.forEach(product => {
            html += `
                <a href="/purchases/create?product_id=${product.id}" class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 border-0 border-bottom">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 text-truncate text-dark small fw-bold" style="max-width: 140px;" title="${product.name}">${product.name}</h6>
                            <span class="badge bg-danger rounded-pill">${product.stock} un.</span>
                        </div>
                        <p class="mb-0 text-muted small" style="font-size: 0.7rem;">
                            Reponer Stock (Mín: ${product.min_stock})
                        </p>
                    </div>
                </a>
            `;
        });

        this.list.innerHTML = html;
    }
    
    renderError(msg = 'Error al cargar datos') {
        this.list.innerHTML = `
            <div class="text-center py-3 text-danger small">
                <i class="fa-solid fa-triangle-exclamation mb-1"></i><br>
                ${msg}
            </div>
        `;
    }
}
