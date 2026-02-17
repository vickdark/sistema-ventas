export function initRolePermissions() {
    // Seleccionar Todo Global
    const selectAllGlobal = document.getElementById('selectAllGlobal');
    const allCheckboxes = document.querySelectorAll('.permission-checkbox');
    const allSwitches = document.querySelectorAll('.select-module');
    
    if (!selectAllGlobal) return;

    let isAllSelected = false;

    selectAllGlobal.addEventListener('click', function() {
        isAllSelected = !isAllSelected;
        allCheckboxes.forEach(cb => cb.checked = isAllSelected);
        allSwitches.forEach(sw => sw.checked = isAllSelected);
        
        this.innerHTML = isAllSelected 
            ? '<i class="fas fa-times-circle me-2"></i> Deseleccionar Todo' 
            : '<i class="fas fa-check-double me-2"></i> Seleccionar Todo';
        
        this.classList.toggle('btn-outline-primary');
        this.classList.toggle('btn-outline-danger');
    });

    // Seleccionar por Módulo
    allSwitches.forEach(sw => {
        sw.addEventListener('change', function() {
            const module = this.dataset.module;
            const moduleCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module-group="${module}"]`);
            moduleCheckboxes.forEach(cb => cb.checked = this.checked);
        });
    });

    // Actualizar Switch de módulo si se desmarca uno individualmente
    allCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const module = this.dataset.moduleGroup;
            const moduleSwitch = document.querySelector(`.select-module[data-module="${module}"]`);
            const moduleCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module-group="${module}"]`);
            const allChecked = Array.from(moduleCheckboxes).every(c => c.checked);
            moduleSwitch.checked = allChecked;
        });
    });

    // Inicializar switches de módulo
    allSwitches.forEach(sw => {
        const module = sw.dataset.module;
        const moduleCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module-group="${module}"]`);
        const allChecked = Array.from(moduleCheckboxes).every(c => c.checked);
        sw.checked = allChecked;
    });
}
