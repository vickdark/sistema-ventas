<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Quote;
use App\Models\Tenant\QuoteItem;
use App\Models\Tenant\Product;
use App\Models\Tenant\Client;
use App\Models\Tenant\Sale;
use App\Models\Tenant\SaleItem;
use App\Models\Tenant\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Quote::with(['client', 'user']);

            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nro_cotizacion', 'like', "%{$search}%")
                      ->orWhereHas('client', function($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();
            
            $quotes = $query->orderBy('id', 'desc')
                            ->offset($offset)
                            ->limit($limit)
                            ->get();

            return response()->json([
                'data' => $quotes->map(function($quote) {
                    return [
                        'id' => $quote->id,
                        'nro_cotizacion' => $quote->nro_cotizacion,
                        'client' => $quote->client ? $quote->client->name : 'Consumidor Final',
                        'total' => (float) $quote->total,
                        'status' => $quote->status,
                        'expiration_date' => $quote->expiration_date ? $quote->expiration_date->format('d/m/Y') : 'N/A',
                        'date' => $quote->created_at->format('d/m/Y H:i'),
                    ];
                }),
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        
        $config = [
            'routes' => [
                'index' => route('quotes.index'),
                'show' => route('quotes.show', ':id'),
                'create' => route('quotes.create'),
                'edit' => route('quotes.edit', ':id'),
                'destroy' => route('quotes.destroy', ':id'),
                'convert' => route('quotes.convert', ':id')
            ]
        ];

        return view('tenant.quotes.index', compact('config'));
    }

    public function create()
    {
        $clients = Client::all();
        $products = Product::with('category')->get();
        $lastQuote = Quote::latest()->first();
        $nextNroCotizacion = $lastQuote ? (int)$lastQuote->nro_cotizacion + 1 : 1;
        
        $config = [
            'routes' => [
                'store' => route('quotes.store'),
                'index' => route('quotes.index'),
                'clients_store' => route('clients.store'),
            ]
        ];

        return view('tenant.quotes.create', compact('clients', 'products', 'nextNroCotizacion', 'config'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'expiration_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $lastQuote = Quote::latest()->first();
            $nroCotizacion = $lastQuote ? (int)$lastQuote->nro_cotizacion + 1 : 1;

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $total += $product->sale_price * $item['quantity'];
            }

            $quote = Quote::create([
                'nro_cotizacion' => str_pad($nroCotizacion, 6, '0', STR_PAD_LEFT),
                'client_id' => $request->client_id,
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'PENDIENTE',
                'expiration_date' => $request->expiration_date,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->sale_price,
                    'subtotal' => $product->sale_price * $item['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Cotización creada correctamente.',
                'quote_id' => $quote->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al crear la cotización: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $quote = Quote::with(['items.product', 'client', 'user'])->findOrFail($id);
        return view('tenant.quotes.show', compact('quote'));
    }

    public function edit($id)
    {
        $quote = Quote::with(['items.product', 'client'])->findOrFail($id);
        
        if ($quote->status !== 'PENDIENTE') {
            return redirect()->route('quotes.show', $id)
                ->with('error', 'Solo se pueden editar cotizaciones pendientes.');
        }

        $clients = Client::all();
        $products = Product::with('category')->get();
        
        // Preparar la cotización para JS
        $quoteData = [
            'id' => $quote->id,
            'client_id' => $quote->client_id,
            'expiration_date' => $quote->expiration_date ? $quote->expiration_date->format('Y-m-d') : null,
            'notes' => $quote->notes,
            'items' => $quote->items->map(function($item) {
                return [
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'product' => [
                        'name' => $item->product->name,
                        'image' => $item->product->image ? asset('storage/' . $item->product->image) : null,
                    ]
                ];
            })
        ];

        $config = [
            'routes' => [
                'update' => route('quotes.update', $quote->id),
                'index' => route('quotes.index'),
                'clients_store' => route('clients.store'),
            ],
            'quote' => $quoteData
        ];

        return view('tenant.quotes.edit', compact('clients', 'products', 'quote', 'config'));
    }

    public function update(Request $request, $id)
    {
        $quote = Quote::findOrFail($id);
        
        if ($quote->status !== 'PENDIENTE') {
            return response()->json(['message' => 'Solo se pueden editar cotizaciones pendientes.'], 403);
        }

        $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'expiration_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $total += $product->sale_price * $item['quantity'];
            }

            $quote->update([
                'client_id' => $request->client_id,
                'expiration_date' => $request->expiration_date,
                'notes' => $request->notes,
                'total' => $total,
            ]);

            // Sincronizar items
            $quote->items()->delete();

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->sale_price,
                    'subtotal' => $product->sale_price * $item['quantity'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Cotización actualizada correctamente.',
                'quote_id' => $quote->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $quote = Quote::findOrFail($id);
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Cotización eliminada correctamente.');
    }

    public function convert($id)
    {
        try {
            DB::beginTransaction();
            
            $quote = Quote::with('items')->findOrFail($id);
            
            if ($quote->status !== 'PENDIENTE') {
                return redirect()->back()->with('error', 'Esta cotización ya fue procesada.');
            }

            // Verificar Stock antes de convertir
            foreach ($quote->items as $item) {
                $product = Product::find($item->product_id);
                if ($product->stock < $item->quantity) {
                    throw new \Exception("Stock insuficiente para el producto: {$product->name}");
                }
            }

            // Crear Venta
            $sale = Sale::create([
                'client_id' => $quote->client_id,
                'user_id' => Auth::id(),
                'total' => $quote->total,
                'sale_date' => now(),
                'nro_venta' => Sale::max('nro_venta') + 1,
                'status' => 'COMPLETADO',
                'payment_type' => 'EFECTIVO' // Por defecto
            ]);

            // Crear Items de Venta y descontar stock
            foreach ($quote->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => $item->subtotal
                ]);

                // Descontar stock
                $product = Product::find($item->product_id);
                $product->decrement('stock', $item->quantity);
            }

            // Actualizar estado de cotización
            $quote->update(['status' => 'CONVERTIDO']);

            // Registrar movimiento en caja si existe una abierta
            $cashRegister = CashRegister::where('status', 'open')
                ->where('user_id', Auth::id())
                ->first();
                
            if ($cashRegister) {
                $cashRegister->sales_total += $sale->total;
                $cashRegister->final_balance += $sale->total;
                $cashRegister->save();
            }

            DB::commit();
            
            return redirect()->route('sales.index')->with('success', 'Cotización convertida en venta exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
