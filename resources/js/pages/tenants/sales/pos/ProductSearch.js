import Swiper from 'swiper';
import { Navigation, Pagination, Grid } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/grid';

export class ProductSearch {
    constructor(cartManager) {
        this.cartManager = cartManager;
        this.productSearch = document.getElementById('productSearch');
        this.productsGrid = document.getElementById('productsGrid');
        this.categoryBtns = document.querySelectorAll('.category-btn');
        
        this.swiper = null;
        this.originalSlides = [];

        this.init();
    }

    init() {
        // Store original slides
        if (this.productsGrid) {
            this.originalSlides = Array.from(this.productsGrid.querySelectorAll('.swiper-slide'));
        }

        // Initialize Swiper
        this.initSwiper();

        // Search Events
        if (this.productSearch) {
            this.productSearch.addEventListener('input', () => this.filterProducts());
        }

        // Category Events
        this.categoryBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                this.categoryBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                this.filterProducts();
            });
        });

        // Add to Cart Event (Delegation)
        if (this.productsGrid) {
            this.productsGrid.addEventListener('click', (e) => this.handleProductClick(e));
        }
    }

    initSwiper() {
        if (this.swiper) {
            this.swiper.destroy(true, true);
        }

        this.swiper = new Swiper('.swiper-products', {
            modules: [Navigation, Pagination, Grid],
            slidesPerView: 1,
            grid: {
                fill: 'row',
                rows: 2,
            },
            spaceBetween: 20,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: { slidesPerView: 2, grid: { rows: 2 } },
                1024: { slidesPerView: 3, grid: { rows: 2 } },
                1400: { slidesPerView: 4, grid: { rows: 2 } }
            },
            observer: true,
            observeParents: true,
        });
    }

    filterProducts() {
        const query = this.productSearch.value.toLowerCase();
        const activeCategory = document.querySelector('.category-btn.active')?.textContent.trim().toLowerCase();

        // Clear the grid
        this.productsGrid.innerHTML = '';

        // Filter and append matching slides
        this.originalSlides.forEach(slide => {
            const name = slide.dataset.name.toLowerCase();
            const code = slide.dataset.code.toLowerCase();
            const category = (slide.dataset.category || '').toLowerCase();
            
            const matchesSearch = name.includes(query) || code.includes(query);
            const matchesCategory = activeCategory === 'todos los productos' || category === activeCategory;

            if (matchesSearch && matchesCategory) {
                this.productsGrid.appendChild(slide);
            }
        });
        
        // Re-initialize Swiper
        this.initSwiper();
        if (this.swiper) {
            this.swiper.update();
            this.swiper.slideTo(0);
        }
    }

    handleProductClick(e) {
        const btn = e.target.closest('.btn-add-product');
        if (!btn) return;

        const product = {
            id: btn.dataset.id,
            name: btn.dataset.name,
            price: parseFloat(btn.dataset.price),
            stock: parseInt(btn.dataset.stock),
            image: btn.dataset.image
        };
        
        this.cartManager.addToCart(product);
        
        // Visual feedback
        const card = btn.classList.contains('card') ? btn : btn.querySelector('.card');
        if (card) {
            card.style.transform = 'scale(0.95)';
            setTimeout(() => card.style.transform = '', 100);
        }
    }

    focusSearch() {
        this.productSearch?.focus();
    }
}
