<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CashRegister;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Sale;
use App\Models\Tenant\Abono;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = CashRegister::with('user');

            // Grid.js parameters
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                      ->orWhereHas('user', function($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();
            
            $registers = $query->orderBy('id', 'desc')
                               ->offset($offset)
                               ->limit($limit)
                               ->get();

            return response()->json([
                'data' => $registers,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }

        $currentRegister = CashRegister::open()->first();
        $config = Configuration::firstOrCreate(['id' => 1]);
        return view('tenant.cash_registers.index', compact('currentRegister', 'config'));
    }

    public function create()
    {
        if (CashRegister::open()->exists()) {
            return redirect()->route('cash-registers.index')->with('error', 'Ya existe una caja abierta.');
        }

        $config = Configuration::first();
        return view('tenant.cash_registers.create', compact('config'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'initial_amount' => 'required|numeric|min:0',
            'opening_date' => 'required|date',
            'observations' => 'nullable|string',
        ]);

        if (CashRegister::open()->exists()) {
            return redirect()->route('cash-registers.index')->with('error', 'Ya existe una caja abierta.');
        }

        $config = Configuration::first();
        
        CashRegister::create([
            'opening_date' => $request->opening_date,
            'scheduled_closing_time' => $config ? $config->cash_register_closing_time : null,
            'initial_amount' => $request->initial_amount,
            'user_id' => auth()->id(),
            'status' => 'abierta',
            'observations' => $request->observations,
        ]);

        return redirect()->route('cash-registers.index')->with('success', 'Caja abierta correctamente.');
    }

    public function show(CashRegister $cashRegister)
    {
        $endTime = $cashRegister->closing_date ?? now();
        
        $directSales = Sale::with('client')
                         ->whereIn('payment_type', ['CONTADO', 'TRANSFERENCIA'])
                         ->where('created_at', '>=', $cashRegister->opening_date)
                         ->where('created_at', '<=', $endTime)
                         ->orderBy('created_at', 'desc')
                         ->get();
        
        $abonos = Abono::with(['client', 'sale'])
                       ->where('created_at', '>=', $cashRegister->opening_date)
                       ->where('created_at', '<=', $endTime)
                       ->orderBy('created_at', 'desc')
                       ->get();

        $totalSalesValue = $directSales->sum('total_paid');
        $totalAbonos = $abonos->sum('amount');
        $totalIncome = $totalSalesValue + $totalAbonos;
        
        return view('tenant.cash_registers.show', compact('cashRegister', 'directSales', 'abonos', 'totalSalesValue', 'totalAbonos', 'totalIncome'));
    }

    public function closeForm(CashRegister $cashRegister)
    {
        if ($cashRegister->status !== 'abierta') {
            return redirect()->route('cash-registers.index')->with('error', 'Esta caja ya está cerrada.');
        }

        // Obtener ventas DIRECTAS (Contado/Transferencia) en esta sesión
        // Excluimos las ventas a CRÉDITO porque esas ingresan dinero a través de los Abonos
        $directSales = Sale::with('client')
                         ->whereIn('payment_type', ['CONTADO', 'TRANSFERENCIA'])
                         ->where('created_at', '>=', $cashRegister->opening_date)
                         ->orderBy('created_at', 'desc')
                         ->get();
        
        $salesCount = $directSales->count();
        $totalSalesValue = $directSales->sum('total_paid');

        // Obtener abonos en esta sesión (pagos de créditos)
        $abonos = Abono::with(['client', 'sale'])
                       ->where('created_at', '>=', $cashRegister->opening_date)
                       ->orderBy('created_at', 'desc')
                       ->get();
                       
        $totalAbonos = $abonos->sum('amount');

        $totalIncome = $totalSalesValue + $totalAbonos;
        $expectedAmount = $cashRegister->initial_amount + $totalIncome;

        return view('tenant.cash_registers.close', compact('cashRegister', 'salesCount', 'totalSalesValue', 'totalAbonos', 'totalIncome', 'expectedAmount', 'directSales', 'abonos'));
    }

    public function close(Request $request, CashRegister $cashRegister)
    {
        $request->validate([
            'final_amount' => 'required|numeric|min:0',
            'observations' => 'nullable|string',
        ]);

        // Recalcular para guardar final (SOLO DIRECTAS + ABONOS)
        $directSales = Sale::whereIn('payment_type', ['CONTADO', 'TRANSFERENCIA'])
                         ->where('created_at', '>=', $cashRegister->opening_date)
                         ->get();
        
        $totalAbonos = Abono::where('created_at', '>=', $cashRegister->opening_date)
                            ->sum('amount');

        $totalIncome = $directSales->sum('total_paid') + $totalAbonos;

        $cashRegister->update([
            'closing_date' => now(),
            'final_amount' => $request->final_amount,
            'status' => 'cerrada',
            'observations' => $cashRegister->observations . "\nCierre: " . $request->observations,
            'sales_count' => $directSales->count(), // Solo contar ventas directas en el contador de "ventas del turno"
            'total_sales' => $totalIncome,
        ]);

        return redirect()->route('cash-registers.index')->with('success', 'Caja cerrada correctamente.');
    }
}
