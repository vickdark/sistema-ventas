<?php

namespace App\Http\Controllers\Tenant\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Tenant\EcommerceConfiguration;
use App\Models\Tenant\Product;
use App\Models\Tenant\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $config = EcommerceConfiguration::first();
        if (!$config) {
            $config = new EcommerceConfiguration();
        }
        
        // Productos Destacados (Últimos 8)
        $products = Product::latest()->take(8)->get();
        
        // Categorías para mostrar en Home
        $categories = Category::has('products')->get();

        // Testimonios desde BD
        $testimonials = \App\Models\Tenant\Testimonial::where('is_active', true)
            ->latest()
            ->take(3)
            ->get();
        
        return view('tenant.ecommerce.index', compact('config', 'products', 'categories', 'testimonials'));
    }

    public function products(Request $request)
    {
        $config = EcommerceConfiguration::first();
        if (!$config) {
            $config = new EcommerceConfiguration();
        }

        $query = Product::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        if ($request->has('category')) {
            $query->where('category_id', $request->input('category'));
        }

        $products = $query->paginate(12);
        
        // Categorías con conteo de productos
        $categories = Category::has('products')->withCount('products')->get();
        $totalProducts = Product::count();
        
        if ($request->ajax()) {
            return response()->json([
                'products' => $products,
                'categories' => $categories,
                'totalProducts' => $totalProducts,
                'html' => view('tenant.ecommerce.partials.products_list', compact('products'))->render(),
                'pagination' => $products->links()->toHtml()
            ]);
        }

        return view('tenant.ecommerce.products', compact('config', 'products', 'categories', 'totalProducts'));
    }

    public function show($id)
    {
        $config = EcommerceConfiguration::first();
        if (!$config) {
            $config = new EcommerceConfiguration();
        }

        $product = Product::findOrFail($id);
        
        return view('tenant.ecommerce.show', compact('config', 'product'));
    }

    public function cart()
    {
        $config = EcommerceConfiguration::first();
        if (!$config) {
            $config = new EcommerceConfiguration();
        }

        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('tenant.ecommerce.cart', compact('config', 'cart', 'total'));
    }

    public function addToCart(Request $request)
    {
        $id = $request->id;
        $quantity = $request->quantity ?? 1;
        
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                "id" => $product->id,
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->sale_price,
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true, 
                'message' => 'Producto agregado al carrito', 
                'cartCount' => count($cart)
            ]);
        }

        return redirect()->back()->with('success', 'Producto agregado al carrito exitosamente!');
    }

    public function updateCart(Request $request)
    {
        if($request->id && $request->quantity){
            $cart = session()->get('cart');
            $cart[$request->id]["quantity"] = $request->quantity;
            session()->put('cart', $cart);
            
            $subtotal = $cart[$request->id]["price"] * $cart[$request->id]["quantity"];
            $total = 0;
            foreach($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Carrito actualizado',
                    'subtotal' => number_format($subtotal, 2),
                    'total' => number_format($total, 2),
                    'cartCount' => count($cart)
                ]);
            }
        }
        
        return redirect()->back()->with('success', 'Carrito actualizado exitosamente');
    }

    public function removeFromCart(Request $request)
    {
        if($request->id) {
            $cart = session()->get('cart');
            if(isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);
            }
            
            $total = 0;
            foreach($cart as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Producto eliminado',
                    'total' => number_format($total, 2),
                    'cartCount' => count($cart)
                ]);
            }
        }
        
        return redirect()->back()->with('success', 'Producto eliminado exitosamente');
    }
}
