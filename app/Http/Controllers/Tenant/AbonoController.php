<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Abono;
use App\Models\Tenant\Sale;
use App\Models\Tenant\Client;
use App\Models\Tenant\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbonoController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            if ($request->get('type') === 'debtors') {
                return $this->getDebtors($request);
            }

            $query = Abono::with(['client', 'sale']);
            if ($request->has('client_id')) {
                $query->where('client_id', $request->client_id);
            }

            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            
            $total = $query->count();
            $abonos = $query->orderBy('id', 'desc')
                            ->offset($offset)
                            ->limit($limit)
                            ->get();

            return response()->json([
                'data' => $abonos,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }

        return view('tenant.abonos.index');
    }

    private function getDebtors(Request $request)
    {
        $limit = $request->get('limit', 10);
        $offset = $request->get('offset', 0);
        $search = $request->get('search');

        $query = Client::whereHas('sales', function($q) {
            $q->where('payment_status', 'PENDIENTE');
        });

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('nit_ci', 'like', "%{$search}%");
        }

        $total = $query->count();
        
        $clients = $query->withCount(['sales' => function($q) {
            $q->where('payment_status', 'PENDIENTE');
        }])->with(['sales' => function($q) {
            $q->where('payment_status', 'PENDIENTE');
        }])
        ->offset($offset)
        ->limit($limit)
        ->get()
        ->map(function($client) {
            $totalDebt = 0;
            foreach ($client->sales as $sale) {
                $paid = Abono::where('sale_id', $sale->id)->sum('amount');
                $totalDebt += ($sale->total_paid - $paid);
            }
            $client->total_debt = $totalDebt;
            return $client;
        });

        return response()->json([
            'data' => $clients,
            'total' => (int) $total,
            'status' => 'success'
        ]);
    }

    public function create()
    {
        $clients = Client::whereHas('sales', function($q) {
            $q->where('payment_status', 'PENDIENTE');
        })->get();

        return view('tenant.abonos.create', compact('clients'));
    }

    /**
     * Get pending sales for a client
     */
    public function getPendingSales(Client $client)
    {
        $sales = Sale::where('client_id', $client->id)
                     ->where('payment_status', 'PENDIENTE')
                     ->get()
                     ->map(function($sale) {
                         // Calculate remaining balance
                         $paid = Abono::where('sale_id', $sale->id)->sum('amount');
                         $sale->remaining = $sale->total_paid - $paid;
                         return $sale;
                     })
                     ->filter(function($sale) {
                         return $sale->remaining > 0;
                     })
                     ->values();

        return response()->json($sales);
    }

    /**
     * Get summary of debt for a client
     */
    public function getDebtSummary(Client $client)
    {
        // Only pending sales (active debts)
        $pendingSales = Sale::where('client_id', $client->id)
                            ->where('payment_status', 'PENDIENTE')
                            ->get();
        
        $pendingSalesIds = $pendingSales->pluck('id');

        // Total value of currently pending sales
        $totalInvoiced = $pendingSales->sum('total_paid');
        
        // Sum of payments for THESE pending sales
        $abonosOnActiveSales = Abono::whereIn('sale_id', $pendingSalesIds)->sum('amount');
        
        // General payments (advances / excess)
        $generalAbonos = Abono::where('client_id', $client->id)
                              ->whereNull('sale_id')
                              ->sum('amount');
        
        $totalAbonos = $abonosOnActiveSales + $generalAbonos;
        
        $totalPendingDebt = $totalInvoiced - $totalAbonos;

        return response()->json([
            'total_invoiced' => (float)$totalInvoiced,
            'total_abonos' => (float)$totalAbonos,
            'total_debt' => (float)$totalPendingDebt
        ]);
    }

    /**
     * Get abono history for a client (Only active debts)
     */
    public function getClientAbonoHistory(Client $client)
    {
        $pendingSalesIds = Sale::where('client_id', $client->id)
                               ->where('payment_status', 'PENDIENTE')
                               ->pluck('id');

        $history = Abono::with('sale')
                        ->where('client_id', $client->id)
                        ->where(function($query) use ($pendingSalesIds) {
                            $query->whereIn('sale_id', $pendingSalesIds)
                                  ->orWhereNull('sale_id');
                        })
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return response()->json($history);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'amount' => 'required|numeric|min:0.01',
            'sale_id' => 'nullable|exists:sales,id',
        ]);

        $cashRegister = CashRegister::open()->first();
        if (!$cashRegister) {
            return response()->json(['message' => 'Debe abrir caja antes de registrar abonos.'], 403);
        }

        return DB::transaction(function () use ($request) {
            $amountToDistribute = $request->amount;
            $abonosCreated = [];

            if ($request->sale_id) {
                // Specific sale payment
                $sale = Sale::findOrFail($request->sale_id);
                $paid = Abono::where('sale_id', $sale->id)->sum('amount');
                $remaining = $sale->total_paid - $paid;

                if ($amountToDistribute > $remaining) {
                    // Logic: Apply remaining to this sale, then maybe distribute the rest or error.
                    // For now, let's just allow it or cap it? 
                    // Better to cap it and maybe warn? Or just allow overpayment?
                    // User said "until finishing my debt", so overpayment might be rare.
                }

                $abono = Abono::create([
                    'client_id' => $request->client_id,
                    'sale_id' => $request->sale_id,
                    'amount' => $amountToDistribute
                ]);

                $this->checkIfSaleIsPaid($sale);
                $abonosCreated[] = $abono;
            } else {
                // General payment - distribute among pending sales (oldest first)
                $pendingSales = Sale::where('client_id', $request->client_id)
                                    ->where('payment_status', 'PENDIENTE')
                                    ->orderBy('sale_date', 'asc')
                                    ->get();

                foreach ($pendingSales as $sale) {
                    if ($amountToDistribute <= 0) break;

                    $paid = Abono::where('sale_id', $sale->id)->sum('amount');
                    $remaining = $sale->total_paid - $paid;

                    if ($remaining <= 0) {
                        $this->checkIfSaleIsPaid($sale); // Cleanup status just in case
                        continue;
                    }

                    $paymentForThisSale = min($amountToDistribute, $remaining);
                    
                    $abono = Abono::create([
                        'client_id' => $request->client_id,
                        'sale_id' => $sale->id,
                        'amount' => $paymentForThisSale
                    ]);

                    $amountToDistribute -= $paymentForThisSale;
                    $this->checkIfSaleIsPaid($sale);
                    $abonosCreated[] = $abono;
                }

                if ($amountToDistribute > 0) {
                    // Still have money left? Create a general abono without sale_id
                    $abono = Abono::create([
                        'client_id' => $request->client_id,
                        'sale_id' => null,
                        'amount' => $amountToDistribute
                    ]);
                    $abonosCreated[] = $abono;
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Abono registrado correctamente.',
                'data' => $abonosCreated
            ]);
        });
    }

    private function checkIfSaleIsPaid(Sale $sale)
    {
        $paid = Abono::where('sale_id', $sale->id)->sum('amount');
        if ($paid >= $sale->total_paid) {
            $sale->payment_status = 'PAGADO';
            $sale->save();
        }
    }

    public function destroy(Abono $abono)
    {
        return DB::transaction(function () use ($abono) {
            $saleId = $abono->sale_id;
            $abono->delete();

            if ($saleId) {
                $sale = Sale::find($saleId);
                if ($sale) {
                    $paid = Abono::where('sale_id', $sale->id)->sum('amount');
                    if ($paid < $sale->total_paid) {
                        $sale->payment_status = 'PENDIENTE';
                        $sale->save();
                    }
                }
            }

            return response()->json(['status' => 'success', 'message' => 'Abono eliminado correctamente.']);
        });
    }
}
