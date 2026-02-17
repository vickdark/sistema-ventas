export function initClientsCreate() {
    const container = document.getElementById('clientsContainer');
    const addBtn = document.getElementById('addClient');
    
    if (!container || !addBtn) return;

    let clientCount = 1;
    const MAX_CLIENTS = 5;

    addBtn.addEventListener('click', function() {
        if (clientCount >= MAX_CLIENTS) {
            alert('Solo puedes agregar hasta 5 clientes a la vez.');
            return;
        }

        const newClient = document.createElement('div');
        newClient.className = 'client-item mb-4 p-4 border rounded-3 bg-light';
        newClient.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 text-primary">Cliente #${clientCount + 1}</h6>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-client client-remove-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control rounded-3" name="clients[${clientCount}][name]" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">NIT/CI</label>
                    <input type="text" class="form-control rounded-3" name="clients[${clientCount}][nit_ci]" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control rounded-3" name="clients[${clientCount}][phone]" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control rounded-3" name="clients[${clientCount}][email]" required>
                </div>
            </div>
        `;
        
        container.appendChild(newClient);
        clientCount++;
        updateUI();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-client')) {
            e.target.closest('.client-item').remove();
            clientCount--;
            renumberClients();
            updateUI();
        }
    });

    function renumberClients() {
        const items = container.querySelectorAll('.client-item');
        items.forEach((item, index) => {
            item.querySelector('h6').textContent = `Cliente #${index + 1}`;
            item.querySelectorAll('input').forEach(input => {
                const name = input.getAttribute('name');
                input.setAttribute('name', name.replace(/\[\d+\]/, `[${index}]`));
            });
        });
    }

    function updateUI() {
        const items = container.querySelectorAll('.client-item');
        
        // Actualizar botones de eliminar
        items.forEach((item, index) => {
            const btn = item.querySelector('.remove-client');
            btn.disabled = items.length === 1;
        });

        // Actualizar botón de agregar
        addBtn.disabled = clientCount >= MAX_CLIENTS;
        if (clientCount >= MAX_CLIENTS) {
            addBtn.classList.add('disabled');
        } else {
            addBtn.classList.remove('disabled');
        }
    }
}
