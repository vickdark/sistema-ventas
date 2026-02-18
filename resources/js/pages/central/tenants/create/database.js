export function initDatabase() {
    const createDbToggle = document.getElementById('create_database');
    const seedDbToggle = document.getElementById('seed_database');
    const dbWarningText = document.getElementById('db-warning-text');
    const dbHelperText = document.getElementById('db-helper-text');
    const seederContainer = document.getElementById('seeder-toggle-container');

    if (createDbToggle) {
        createDbToggle.addEventListener('change', function() {
            if (!this.checked) {
                dbWarningText.classList.remove('d-none');
                dbHelperText.classList.add('d-none');
                seedDbToggle.checked = false;
                seedDbToggle.disabled = true;
                seederContainer.style.opacity = '0.5';
            } else {
                dbWarningText.classList.add('d-none');
                dbHelperText.classList.remove('d-none');
                seedDbToggle.disabled = false;
                seederContainer.style.opacity = '1';
            }
        });
    }
}
