import { CartManager } from './CartManager';
import { ProductSearch } from './ProductSearch';
import { CustomerManager } from './CustomerManager';
import { CheckoutManager } from './CheckoutManager';
import { PosUI } from './PosUI';

export function initSalesPOS(config) {
    // Initialize Managers
    const cartManager = new CartManager();
    const customerManager = new CustomerManager(config);
    const productSearch = new ProductSearch(cartManager);
    const checkoutManager = new CheckoutManager(config, cartManager, customerManager);
    
    // Initialize UI and Orchestration
    new PosUI(productSearch, checkoutManager);
}
