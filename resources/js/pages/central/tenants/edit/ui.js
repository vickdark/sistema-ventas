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
        step.classList.add('fw-bold', 'text-warning');
        icon.innerHTML = '<i class="fas fa-circle-notch fa-spin text-warning"></i>';
    } else if (state === 'complete') {
        step.classList.remove('text-warning');
        step.classList.add('text-success');
        icon.innerHTML = '<i class="fas fa-check-circle text-success"></i>';
        const statusDiv = step.querySelector('.step-status');
        if (statusDiv) statusDiv.innerHTML = '<span class="badge bg-success">Listo</span>';
    } else if (state === 'skipped') {
        step.classList.add('opacity-50');
        icon.innerHTML = '<i class="fas fa-forward text-muted"></i>';
    }
}

export function showMaintenanceSuccess() {
    const mainSpinner = document.getElementById('main-spinner');
    const successIcon = document.getElementById('success-icon');
    const processTitle = document.getElementById('process-title');
    const finalActions = document.getElementById('final-actions');

    if (mainSpinner) mainSpinner.classList.add('d-none');
    if (successIcon) successIcon.classList.remove('d-none');
    if (processTitle) processTitle.innerText = '¡Proceso Completado!';
    if (finalActions) finalActions.classList.remove('d-none');
}
