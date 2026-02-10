<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Product;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Obtiene los productos con stock bajo (menor o igual al stock mínimo)
     */
    public function getLowStockProducts()
    {
        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
            ->select('id', 'name', 'stock', 'min_stock')
            ->orderBy('stock', 'asc')
            ->take(5) // Limitamos a los 5 más críticos para el navbar
            ->get();

        return response()->json([
            'count' => $lowStockProducts->count(),
            'products' => $lowStockProducts
        ]);
    }
}
