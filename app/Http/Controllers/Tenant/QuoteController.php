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

        return DB::transaction(function () use ($request) {
            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $total += $product->sale_price * $item['quantity'];
            }

            $lastQuote = Quote::latest()->first();
            $nroCotizacion = $lastQuote ? (int)$lastQuote->nro_cotizacion + 1 : 1;

            $quote = Quote::create([
                'nro_cotizacion' => (string)$nroCotizacion,
                'client_id' => $request->client_id,
                'branch_id' => session('active_branch_id'),
                'user_id' => Auth::id(),
                'total' => $total,
                'status' => 'PENDIENTE',
                'expiration_date' => $request->expiration_date ?? now()->addDays(15),
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $itemData) {
                $product = Product::find($itemData['product_id']);
                
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $product->sale_price,
                    'subtotal' => $product->sale_price * $itemData['quantity'],
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Cotización creada correctamente.',
                'quote_id' => $quote->id
            ]);
        });
    }

    public function show($id)
    {
        $quote = Quote::with(['client', 'user', 'items.product'])->findOrFail($id);
        return view('tenant.quotes.show', compact('quote'));
    }

    public function convert($id)
    {
        $quote = Quote::with('items.product')->findOrFail($id);
        
        if ($quote->status === 'CONVERTIDA') {
            return redirect()->back()->with('error', 'Esta cotización ya fue convertida en venta.');
        }

        // Check if there is an open cash register
        $cashRegister = CashRegister::open()->where('user_id', Auth::id())->first();
        if (!$cashRegister) {
            return redirect()->route('cash-registers.index')
                ->with('error', 'Debes abrir tu caja antes de convertir la cotización en venta.');
        }

        try {
            return DB::transaction(function () use ($quote) {
                // Verificar stock antes de convertir
                foreach ($quote->items as $item) {
                    if ($item->product->stock < $item->quantity) {
                        throw new \Exception("Stock insuficiente para el producto: {$item->product->name}");
                    }
                }

                $lastSale = Sale::latest()->first();
                $nroVenta = $lastSale ? (int)$lastSale->nro_venta + 1 : 1;

                $sale = Sale::create([
                    'nro_venta' => $nroVenta,
                    'client_id' => $quote->client_id ?? 1, // Default to a general client if null
                    'total_paid' => $quote->total,
                    'user_id' => Auth::id(),
                    'sale_date' => now(),
                    'payment_type' => 'CONTADO',
                    'payment_status' => 'PAGADO',
                ]);

                foreach ($quote->items as $item) {
                    SaleItem::create([
                        'sale_id' => $sale->id,
                        'client_id' => $sale->client_id,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'sale_date' => now(),
                    ]);

                    $item->product->removeStock($item->quantity, 'Venta (Cotización)', 'De Cotización #' . $quote->nro_cotizacion, $sale);
                }

                $quote->update(['status' => 'CONVERTIDA']);

                return redirect()->route('sales.show', $sale->id)
                    ->with('success', 'Cotización convertida en venta exitosamente.');
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al convertir: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $quote = Quote::findOrFail($id);
        $quote->delete();
        return redirect()->route('quotes.index')->with('success', 'Cotización eliminada.');
    }
}
