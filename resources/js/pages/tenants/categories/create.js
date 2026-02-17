export function initCategoriesCreate() {
    const container = document.getElementById('categoriesContainer');
    const addBtn = document.getElementById('addCategory');
    
    if (!container || !addBtn) return;

    let categoryCount = 1;

    addBtn.addEventListener('click', function() {
        const newCategory = document.createElement('div');
        newCategory.className = 'category-item mb-3 p-3 border rounded-3 bg-light';
        newCategory.innerHTML = `
            <div class="d-flex gap-2 align-items-start">
                <div class="flex-grow-1">
                    <input type="text" 
                           class="form-control rounded-3" 
                           name="categories[]" 
                           placeholder="Nombre de la categoría" 
                           required>
                </div>
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-category category-remove-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        container.appendChild(newCategory);
        categoryCount++;
        updateRemoveButtons();
    });

    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-category')) {
            e.target.closest('.category-item').remove();
            categoryCount--;
            updateRemoveButtons();
        }
    });

    function updateRemoveButtons() {
        const items = container.querySelectorAll('.category-item');
        items.forEach((item, index) => {
            const btn = item.querySelector('.remove-category');
            btn.disabled = items.length === 1;
        });
    }
}
