@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Crear Categorías</h1>
        </div>
        <div class="col-auto">
            <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-soft rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('categories.store') }}" method="POST" id="categoryForm">
                        @csrf
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Categorías a Crear</h5>
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="addCategory">
                                <i class="fas fa-plus me-1"></i> Agregar Otra
                            </button>
                        </div>

                        <div id="categoriesContainer">
                            <!-- Primera categoría por defecto -->
                            <div class="category-item mb-3 p-3 border rounded-3 bg-light">
                                <div class="d-flex gap-2 align-items-start">
                                    <div class="flex-grow-1">
                                        <input type="text" 
                                               class="form-control rounded-3 @error('categories.0') is-invalid @enderror" 
                                               name="categories[]" 
                                               placeholder="Nombre de la categoría" 
                                               required>
                                        @error('categories.0')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-category category-remove-btn" disabled>
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info small mb-3">
                            <i class="fas fa-info-circle me-1"></i> 
                            Puedes agregar múltiples categorías a la vez. Cada una debe tener un nombre único.
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-pill py-2">
                                <i class="fas fa-save me-2"></i> Guardar Categorías
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('categoriesContainer');
    const addBtn = document.getElementById('addCategory');
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
});
</script>
@endsection
