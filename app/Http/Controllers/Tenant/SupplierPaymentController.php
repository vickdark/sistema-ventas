<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\SupplierPayment;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = Purchase::with('supplier')
                ->where('payment_status', '!=', 'PAGADO');

            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('nro_compra', 'like', "%{$search}%")
                      ->orWhereHas('supplier', function($sq) use ($search) {
                          $sq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();
            
            $debts = $query->orderBy('due_date', 'asc')
                           ->offset($offset)
                           ->limit($limit)
                           ->get();

            return response()->json([
                'data' => $debts->map(function($debt) {
                    return [
                        'id' => $debt->id,
                        'nro_compra' => $debt->nro_compra,
                        'supplier' => [
                            'name' => $debt->supplier->name
                        ],
                        'total_amount' => (float) $debt->total_amount,
                        'pending_amount' => (float) $debt->pending_amount,
                        'due_date' => $debt->due_date ? $debt->due_date->format('Y-m-d') : null,
                    ];
                }),
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }
        
        $config = [
            'routes' => [
                'index' => route('supplier-payments.index'),
                'store' => route('supplier-payments.store'),
            ]
        ];

        return view('tenant.supplier_payments.index', compact('config'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $purchase = Purchase::lockForUpdate()->findOrFail($request->purchase_id);
                
                if ($request->amount > $purchase->pending_amount) {
                    throw new \Exception('El monto del abono excede el saldo pendiente.');
                }

                $payment = SupplierPayment::create([
                    'purchase_id' => $request->purchase_id,
                    'user_id' => Auth::id(),
                    'amount' => $request->amount,
                    'payment_date' => $request->payment_date,
                    'payment_method' => $request->payment_method,
                    'notes' => $request->notes,
                ]);

                $purchase->pending_amount -= $request->amount;
                
                if ($purchase->pending_amount <= 0) {
                    $purchase->payment_status = 'PAGADO';
                } else {
                    $purchase->payment_status = 'PARCIAL';
                }
                
                $purchase->save();

                return response()->json([
                    'status' => 'success',
                    'message' => 'Abono registrado correctamente.',
                    'payment_id' => $payment->id
                ]);
            });
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'user', 'payments.user', 'items.product'])->findOrFail($id);
        return view('tenant.supplier_payments.show', compact('purchase'));
    }
}
