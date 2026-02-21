<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Product;
use App\Models\Tenant\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;    

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Purchase::with(['items.product.suppliers', 'supplier']);

            // Grid.js parameters
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nro_compra', 'like', "%{$search}%")
                      ->orWhere('voucher', 'like', "%{$search}%")
                      ->orWhereHas('items.product', function($pq) use ($search) {
                          $pq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('supplier', function($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();
            
            $purchases = $query->orderBy('id', 'desc')
                               ->offset($offset)
                               ->limit($limit)
                               ->get();

            return response()->json([
                'data' => $purchases,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        
        $config = [
            'routes' => [
                'index' => route('purchases.index'),
                'show' => route('purchases.show', ':id'),
                'edit' => route('purchases.edit', ':id'),
                'destroy' => route('purchases.destroy', ':id')
            ]
        ];

        return view('tenant.purchases.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::with('suppliers')->get();
        $suppliers = Supplier::all();
        $lastPurchase = Purchase::latest()->first();
        $nextNroCompra = $lastPurchase ? (int)$lastPurchase->nro_compra + 1 : 1;

        return view('tenant.purchases.create', compact('products', 'suppliers', 'nextNroCompra'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'voucher' => 'required|string|max:255',
            'nro_compra' => 'required|unique:purchases,nro_compra',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1];
                    $productId = $request->input("items.$index.product_id");
                    if ($productId) {
                        $product = Product::find($productId);
                        if ($product && $product->max_stock > 0 && ($product->stock + $value) > $product->max_stock) {
                            $fail("La cantidad del producto '{$product->name}' supera el stock máximo permitido ({$product->max_stock}). Stock actual: {$product->stock}.");
                        }
                    }
                },
            ],
            'items.*.price' => 'required|numeric|min:0',
            'payment_condition' => 'required|in:cash,credit',
            'due_date' => 'nullable|date',
        ]);

        return DB::transaction(function () use ($request) {
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['quantity'] * $item['price'];
            }

            $purchaseData = $request->only(['supplier_id', 'purchase_date', 'voucher', 'nro_compra']);
            $purchaseData['user_id'] = Auth::id();
            $purchaseData['total'] = $total;
            $purchaseData['total_amount'] = $total;

            // Manejo de condición de pago (Contado vs Crédito)
            if ($request->payment_condition === 'credit') {
                $purchaseData['pending_amount'] = $total;
                $purchaseData['payment_status'] = 'PENDIENTE';
                // Si viene fecha de vencimiento la usamos, si no, queda NULL (pero sigue siendo crédito/pendiente)
                $purchaseData['due_date'] = $request->due_date;
            } else {
                // Contado
                $purchaseData['pending_amount'] = 0;
                $purchaseData['payment_status'] = 'PAGADO';
                $purchaseData['due_date'] = null;
            }

            $purchase = Purchase::create($purchaseData);

            foreach ($request->items as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['price'];
                
                $purchase->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $subtotal,
                ]);

                // Update product stock using helper
                /** @var Product $product */
                $product = Product::find($itemData['product_id']);
                if ($product) {
                    $product->addStock($itemData['quantity'], 'Compra', 'Compra #' . $purchase->nro_compra, $purchase);
                }
            }

            return redirect()->route('purchases.index')
                ->with('success', 'Compra registrada correctamente.')
                ->with('new_purchase_id', $purchase->id);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $purchase = Purchase::with(['items.product', 'supplier', 'user'])->findOrFail($id);
        return view('tenant.purchases.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $purchase = Purchase::with(['items.product', 'supplier'])->findOrFail($id);
        $products = Product::with('suppliers')->get();
        $suppliers = Supplier::all();
        
        $purchaseItems = $purchase->items->map(function($item) use ($purchase) {
            return [
                'product_id' => $item->product_id,
                'name' => $item->product->name ?? 'N/A',
                'code' => $item->product->code ?? 'N/A',
                'quantity' => (int)$item->quantity,
                'price' => (float)$item->price,
                'subtotal' => (float)$item->subtotal,
                'supplier_id' => $purchase->supplier_id,
                'supplier_name' => $purchase->supplier->name ?? 'N/A'
            ];
        });

        return view('tenant.purchases.edit', compact('purchase', 'products', 'suppliers', 'purchaseItems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $purchase = Purchase::findOrFail($id);

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_date' => 'required|date',
            'voucher' => 'required|string|max:255',
            'nro_compra' => 'required|unique:purchases,nro_compra,' . $id,
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) use ($request, $purchase) {
                    $index = explode('.', $attribute)[1];
                    $productId = $request->input("items.$index.product_id");
                    if ($productId) {
                        $product = Product::find($productId);
                        if ($product && $product->max_stock > 0) {
                            // Obtener la cantidad previa de este producto en esta compra (si existe)
                            $oldItem = $purchase->items->where('product_id', $productId)->first();
                            $oldQuantity = $oldItem ? $oldItem->quantity : 0;
                            
                            // El stock proyectado es: stock actual - cantidad vieja + cantidad nueva
                            $projectedStock = $product->stock - $oldQuantity + $value;
                            
                            if ($projectedStock > $product->max_stock) {
                                $fail("La cantidad del producto '{$product->name}' supera el stock máximo permitido ({$product->max_stock}). Stock proyectado: {$projectedStock}.");
                            }
                        }
                    }
                },
            ],
            'items.*.price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $purchase) {
            // Revert stock for existing items
            foreach ($purchase->items as $item) {
                /** @var Product $product */
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->removeStock($item->quantity, 'Edición de Compra', 'Compra #' . $purchase->nro_compra);
                }
            }

            // Delete existing items
            $purchase->items()->delete();

            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['quantity'] * $item['price'];
            }

            $purchaseData = $request->only(['supplier_id', 'purchase_date', 'voucher', 'nro_compra']);
            $purchaseData['total'] = $total;

            $purchase->update($purchaseData);

            foreach ($request->items as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['price'];
                
                $purchase->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'subtotal' => $subtotal,
                ]);

                // Update product stock
                /** @var Product $product */
                $product = Product::find($itemData['product_id']);
                if ($product) {
                    $product->addStock($itemData['quantity'], 'Edición de Compra', 'Compra #' . $purchase->nro_compra, $purchase);
                }
            }

            return redirect()->route('purchases.index')->with('success', 'Compra actualizada correctamente.');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $purchase = Purchase::with('items')->findOrFail($id);

        return DB::transaction(function () use ($purchase) {
            foreach ($purchase->items as $item) {
                // Adjust product stock
                /** @var Product $product */
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->removeStock($item->quantity, 'Anulación de Compra', 'Compra #' . $purchase->nro_compra . ' eliminada');
                }
            }

            $purchase->delete();
            return redirect()->route('purchases.index')->with('success', 'Compra eliminada correctamente y stock revertido.');
        });
    }

    public function voucher(Purchase $purchase)
    {
        $purchase->load(['items.product', 'supplier', 'user']);
        return view('tenant.purchases.voucher', compact('purchase'));
    }

    public function quickStoreSupplier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $supplier = Supplier::create($validated);

        return response()->json([
            'status' => 'success',
            'supplier' => $supplier
        ]);
    }
}
