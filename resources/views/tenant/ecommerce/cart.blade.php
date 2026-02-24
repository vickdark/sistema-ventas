@extends('tenant.ecommerce.layout')

@section('content')
<div class="bg-light py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('tenant.shop.index') }}" class="text-decoration-none text-muted">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Carrito de Compras</li>
            </ol>
        </nav>
    </div>
</div>

<div class="container py-5" id="cart-app">
    <h2 class="fw-bold mb-4">Tu Carrito de Compras</h2>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(count($cart) > 0)
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 p-4 text-muted small fw-bold text-uppercase">Producto</th>
                                        <th class="border-0 p-4 text-muted small fw-bold text-uppercase">Precio</th>
                                        <th class="border-0 p-4 text-muted small fw-bold text-uppercase text-center">Cantidad</th>
                                        <th class="border-0 p-4 text-muted small fw-bold text-uppercase">Subtotal</th>
                                        <th class="border-0 p-4 text-muted small fw-bold text-uppercase text-end">Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart as $id => $details)
                                        <tr id="row-{{ $id }}" class="border-bottom">
                                            <td class="p-4">
                                                <div class="d-flex align-items-center">
                                                    @if($details['image'])
                                                        <img src="{{ asset('storage/' . $details['image']) }}" alt="{{ $details['name'] }}" class="rounded-3 object-fit-cover" style="width: 70px; height: 70px;">
                                                    @else
                                                        <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-secondary" style="width: 70px; height: 70px;">
                                                            <i class="fas fa-image fa-lg"></i>
                                                        </div>
                                                    @endif
                                                    <div class="ms-3">
                                                        <h6 class="mb-0 fw-bold text-dark">{{ $details['name'] }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4">
                                                <span class="text-muted fw-bold">${{ number_format($details['price'], 2) }}</span>
                                            </td>
                                            <td class="p-4">
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <div class="input-group input-group-sm" style="width: 100px;">
                                                        <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity({{ $id }}, -1)">-</button>
                                                        <input type="number" class="form-control text-center" id="quantity-{{ $id }}" value="{{ $details['quantity'] }}" min="1" readonly>
                                                        <button class="btn btn-outline-secondary" type="button" onclick="updateQuantity({{ $id }}, 1)">+</button>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4">
                                                <span class="text-primary-custom fw-bold" id="subtotal-{{ $id }}">${{ number_format($details['price'] * $details['quantity'], 2) }}</span>
                                            </td>
                                            <td class="p-4 text-end">
                                                <button class="btn btn-link text-danger p-0" onclick="removeFromCart({{ $id }})" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Resumen del Pedido</h5>
                        
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-bold" id="cart-total-summary">${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted">Envío</span>
                            <span class="text-success fw-bold">Gratis</span>
                        </div>
                        
                        <hr class="text-muted opacity-25">
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="h5 fw-bold mb-0">Total</span>
                            <span class="h5 fw-bold text-primary-custom mb-0" id="cart-grand-total">${{ number_format($total, 2) }}</span>
                        </div>
                        
                        <button class="btn btn-primary-custom w-100 py-3 rounded-pill fw-bold shadow-lg hover-scale transition-all">
                            PROCEDER AL PAGO <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        
                        <div class="mt-3 text-center">
                            <a href="{{ route('tenant.shop.products') }}" class="text-muted text-decoration-underline small">Continuar Comprando</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                    <i class="fas fa-shopping-cart fa-4x text-muted opacity-25"></i>
                </div>
            </div>
            <h3 class="fw-bold text-secondary">Tu carrito está vacío</h3>
            <p class="text-muted mb-4">¡Parece que aún no has agregado nada!</p>
            <a href="{{ route('tenant.shop.products') }}" class="btn btn-primary-custom rounded-pill px-5 py-3 fw-bold shadow-sm">
                Explorar Productos
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    function updateQuantity(id, change) {
        let input = document.getElementById('quantity-' + id);
        let newQuantity = parseInt(input.value) + change;
        
        if (newQuantity < 1) return;
        
        // Optimistic update
        input.value = newQuantity;
        
        axios.post('{{ route('tenant.shop.cart.update') }}', {
            id: id,
            quantity: newQuantity,
            _token: '{{ csrf_token() }}'
        })
        .then(response => {
            if(response.data.success) {
                document.getElementById('subtotal-' + id).innerText = '$' + response.data.subtotal;
                document.getElementById('cart-total-summary').innerText = '$' + response.data.total;
                document.getElementById('cart-grand-total').innerText = '$' + response.data.total;
                updateNavbarCartCount(response.data.cartCount);
            }
        })
        .catch(error => {
            console.error(error);
            input.value = newQuantity - change; // Revert on error
            alert('Error al actualizar el carrito');
        });
    }

    function removeFromCart(id) {
        if(!confirm('¿Estás seguro de eliminar este producto?')) return;
        
        axios.post('{{ route('tenant.shop.cart.remove') }}', {
            id: id,
            _token: '{{ csrf_token() }}'
        })
        .then(response => {
            if(response.data.success) {
                // Remove row with animation
                let row = document.getElementById('row-' + id);
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    // If cart is empty, reload to show empty state
                    if(response.data.cartCount == 0) {
                        location.reload();
                    } else {
                        document.getElementById('cart-total-summary').innerText = '$' + response.data.total;
                        document.getElementById('cart-grand-total').innerText = '$' + response.data.total;
                        updateNavbarCartCount(response.data.cartCount);
                    }
                }, 300);
            }
        })
        .catch(error => {
            console.error(error);
            alert('Error al eliminar producto');
        });
    }
</script>

<style>
    .hover-scale:hover {
        transform: scale(1.02);
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
@endsection
