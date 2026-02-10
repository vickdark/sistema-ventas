<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CashRegister;
use App\Models\Tenant\Configuration;
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
        return view('tenant.cash_registers.show', compact('cashRegister'));
    }

    public function closeForm(CashRegister $cashRegister)
    {
        if ($cashRegister->status !== 'abierta') {
            return redirect()->route('cash-registers.index')->with('error', 'Esta caja ya está cerrada.');
        }

        // Simular cálculos de ventas (esto se conectará con el módulo de ventas después)
        // Por ahora, valores en 0 o placeholder
        $salesCount = 0; // TODO: Count actual sales in this session
        $totalSales = 0; // TODO: Sum actual sales totals in this session
        $expectedAmount = $cashRegister->initial_amount + $totalSales;

        return view('tenant.cash_registers.close', compact('cashRegister', 'salesCount', 'totalSales', 'expectedAmount'));
    }

    public function close(Request $request, CashRegister $cashRegister)
    {
        $request->validate([
            'final_amount' => 'required|numeric|min:0',
            'observations' => 'nullable|string',
        ]);

        $cashRegister->update([
            'closing_date' => now(),
            'final_amount' => $request->final_amount,
            'status' => 'cerrada',
            'observations' => $cashRegister->observations . "\nCierre: " . $request->observations,
            // Aquí se deberían guardar los conteos finales reales
            'sales_count' => 0, // TODO: Get actual count
            'total_sales' => 0, // TODO: Get actual total
        ]);

        return redirect()->route('cash-registers.index')->with('success', 'Caja cerrada correctamente.');
    }
}
