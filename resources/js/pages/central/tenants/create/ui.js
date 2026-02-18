export function logToConsole(message, type = 'info') {
    const consoleEl = document.getElementById('terminal-console');
    if (!consoleEl) return;

    const line = document.createElement('div');
    const time = new Date().toLocaleTimeString([], { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
    line.className = 'mb-1 ' + (type === 'error' ? 'text-danger' : (type === 'success' ? 'text-success' : 'text-light'));
    line.innerHTML = `<span class="text-secondary small">[${time}]</span> <span class="opacity-75">></span> ${message}`;
    consoleEl.appendChild(line);
    consoleEl.scrollTop = consoleEl.scrollHeight;
}

export function updateStep(stepNum, state) {
    const step = document.getElementById(`step-${stepNum}`);
    if (!step) return;
    const icon = step.querySelector('.step-icon');
    
    if (state === 'active') {
        step.classList.remove('text-muted');
        step.classList.add('fw-bold', 'text-primary');
        icon.innerHTML = '<i class="fas fa-circle-notch fa-spin text-primary"></i>';
    } else if (state === 'complete') {
        step.classList.remove('text-primary');
        step.classList.add('text-success');
        icon.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
        const statusDiv = step.querySelector('.step-status');
        if (statusDiv) statusDiv.innerHTML = '<span class="badge bg-success">Listo</span>';
    }
}

export function showFinalState(host, tenantId) {
    document.getElementById('main-spinner').classList.add('d-none');
    document.getElementById('success-icon').classList.remove('d-none');
    document.getElementById('process-title').innerText = '¡Proceso Completado!';
    document.getElementById('process-subtitle').innerText = 'La empresa ha sido configurada con éxito y está lista para operar.';
    
    // Configurar el botón de visita
    const visitBtn = document.getElementById('btn-visit-tenant');
    if (tenantId) {
        const protocol = window.location.protocol;
        visitBtn.href = `${protocol}//${tenantId}${host}`;
    } else {
        visitBtn.classList.add('d-none');
    }

    document.getElementById('final-actions').classList.remove('d-none');
    logToConsole('--- FIN DEL PROCESO: LISTO PARA USAR ---', 'success');
}
