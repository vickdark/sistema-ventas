<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sales = Sale::with(['client', 'product'])->get();
            return response()->json(['data' => $sales]);
        }
        $sales = Sale::with(['client', 'product'])->get();
        return view('tenant.sales.index', compact('sales'));
    }
}
