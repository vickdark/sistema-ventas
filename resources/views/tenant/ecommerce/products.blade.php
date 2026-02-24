@extends('tenant.ecommerce.layout')

@section('content')
<div class="bg-white border-bottom py-5">
    <div class="container">
        <div class="d-flex flex-column align-items-center text-center">
            <h1 class="display-4 fw-bold text-uppercase tracking-widest mb-3">Nuestra Colección</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0 small text-uppercase tracking-wide">
                    <li class="breadcrumb-item"><a href="{{ route('tenant.shop.index') }}" class="text-decoration-none text-muted">Inicio</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Catálogo</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="container py-5 my-4" id="catalog-app">
    <div class="row gx-5">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-5">
            <div class="sticky-top" style="top: 100px; z-index: 1;">
                <div class="mb-5">
                    <h6 class="fw-bold text-uppercase tracking-widest mb-4 small border-bottom pb-2">Filtrar por</h6>
                    
                    <!-- Search -->
                    <div class="mb-4">
                        <div class="input-group border-bottom">
                            <input type="text" class="form-control border-0 bg-transparent rounded-0 px-0" id="search-input" placeholder="Buscar producto..." value="{{ request('search') }}">
                            <button class="btn btn-link text-dark p-0" type="button" onclick="applyFilters()"><i class="fas fa-search"></i></button>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="mb-4">
                        <label class="form-label small text-muted fw-bold text-uppercase mb-3 tracking-wide">Categorías</label>
                        <ul class="list-unstyled" id="categories-list">
                            <li class="mb-2">
                                <a href="javascript:void(0)" onclick="filterByCategory('')" class="d-flex justify-content-between align-items-center text-decoration-none category-link {{ !request('category') ? 'text-dark fw-bold' : 'text-secondary' }}" data-id="">
                                    <span>Todas</span>
                                    <span class="small text-muted">{{ $totalProducts }}</span>
                                </a>
                            </li>
                            @foreach($categories as $cat)
                                <li class="mb-2">
                                    <a href="javascript:void(0)" onclick="filterByCategory({{ $cat->id }})" class="d-flex justify-content-between align-items-center text-decoration-none category-link {{ request('category') == $cat->id ? 'text-dark fw-bold' : 'text-secondary' }}" data-id="{{ $cat->id }}">
                                        <span>{{ $cat->name }}</span>
                                        <span class="small text-muted">{{ $cat->products_count }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-end mb-5 pb-3 border-bottom">
                <div>
                    <h1 class="h2 fw-bold mb-0 text-uppercase tracking-wide" id="page-title">
                        @if(request('category'))
                            {{ $categories->find(request('category'))->name }}
                        @elseif(request('search'))
                            Resultados: "{{ request('search') }}"
                        @else
                            Catálogo
                        @endif
                    </h1>
                </div>
                <span class="text-muted small text-uppercase tracking-wide" id="products-count">{{ $products->total() }} productos</span>
            </div>

            <!-- Loading Spinner -->
            <div id="loading-spinner" class="text-center py-5 d-none">
                <div class="spinner-border text-primary-custom" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2 text-muted">Cargando productos...</p>
            </div>

            <!-- Products Container -->
            <div id="products-container">
                @include('tenant.ecommerce.partials.products_list', ['products' => $products])
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
    .btn-primary-custom {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .btn-primary-custom:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    .text-primary-custom {
        color: var(--primary-color) !important;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    let currentCategory = '{{ request('category') }}';
    let currentSearch = '{{ request('search') }}';
    let currentPage = 1;

    // Listen for Enter key on search input
    document.getElementById('search-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
    });

    function filterByCategory(categoryId) {
        currentCategory = categoryId;
        currentPage = 1;
        updateCategoryLinks(categoryId);
        fetchProducts();
    }

    function applyFilters() {
        currentSearch = document.getElementById('search-input').value;
        currentPage = 1;
        fetchProducts();
    }

    function resetFilters() {
        currentCategory = '';
        currentSearch = '';
        currentPage = 1;
        document.getElementById('search-input').value = '';
        updateCategoryLinks('');
        fetchProducts();
    }

    function updateCategoryLinks(activeId) {
        document.querySelectorAll('.category-link').forEach(link => {
            const linkId = link.getAttribute('data-id');
            if (linkId == activeId) {
                link.classList.remove('text-dark');
                link.classList.add('text-primary', 'fw-bold');
            } else {
                link.classList.remove('text-primary', 'fw-bold');
                link.classList.add('text-dark');
            }
        });
    }

    function fetchProducts(url = null) {
        const container = document.getElementById('products-container');
        const spinner = document.getElementById('loading-spinner');
        
        container.style.opacity = '0.5';
        spinner.classList.remove('d-none');

        const requestUrl = url || '{{ route('tenant.shop.products') }}';
        
        axios.get(requestUrl, {
            params: {
                category: currentCategory,
                search: currentSearch,
                page: currentPage
            }
        })
        .then(response => {
            // Update products list
            container.innerHTML = response.data.html;
            
            // Update title and count
            document.getElementById('products-count').innerText = response.data.totalProducts + ' productos encontrados';
            
            let title = 'Todos los Productos';
            if (currentCategory) {
                // We would ideally get the category name from the response or look it up
                // For now we can keep it simple or update if passed from backend
                const activeCatLink = document.querySelector(`.category-link[data-id="${currentCategory}"] span:first-child`);
                if(activeCatLink) title = activeCatLink.innerText;
            } else if (currentSearch) {
                title = 'Resultados para "' + currentSearch + '"';
            }
            document.getElementById('page-title').innerText = title;

            // Re-attach pagination listeners
            attachPaginationListeners();
            
            // Update URL without reload
            const newUrl = new URL(window.location);
            if (currentCategory) newUrl.searchParams.set('category', currentCategory);
            else newUrl.searchParams.delete('category');
            
            if (currentSearch) newUrl.searchParams.set('search', currentSearch);
            else newUrl.searchParams.delete('search');
            
            window.history.pushState({}, '', newUrl);
        })
        .catch(error => {
            console.error('Error fetching products:', error);
            container.innerHTML = '<div class="alert alert-danger">Error al cargar productos. Por favor intente de nuevo.</div>';
        })
        .finally(() => {
            container.style.opacity = '1';
            spinner.classList.add('d-none');
        });
    }

    function attachPaginationListeners() {
        document.querySelectorAll('.pagination a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                if (url) {
                    // Extract page number if needed or just pass the full URL
                    fetchProducts(url);
                }
            });
        });
    }

    // Initial attachment
    document.addEventListener('DOMContentLoaded', attachPaginationListeners);
</script>
@endsection
