<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CashRegister;
use App\Models\Tenant\Configuration;
use App\Models\Tenant\Sale;
use App\Models\Tenant\Abono;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CashRegisterController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = CashRegister::with(['user', 'branch']);

            // Si no es administrador, solo puede ver sus propias cajas
            if (!Auth::user()->isAdmin()) {
                $query->where('user_id', Auth::id());
            }

            // Grid.js parameters
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhereHas('user', function($uq) use ($search) {
                          $uq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();
            
            $registers = $query->latest()
                               ->offset($offset)
                               ->limit($limit)
                               ->get();

            // Transformar datos para Grid.js si es necesario (ej: formatear fechas)
            $data = $registers->map(function($register) {
                return [
                    'id' => $register->id,
                    'name' => $register->name,
                    'opening_date' => $register->opening_date->format('d/m/Y H:i'),
                    'closing_date' => $register->closing_date ? $register->closing_date->format('d/m/Y H:i') : null,
                    'initial_amount' => $register->initial_amount,
                    'final_amount' => $register->final_amount,
                    'user' => $register->user,
                    'branch' => $register->branch ? $register->branch->name : 'N/A',
                    'status' => $register->status,
                ];
            });

            return response()->json([
                'data' => $data,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }

        if (Auth::user()->isAdmin()) {
            $openRegisters = CashRegister::open()->with(['user', 'branch'])->get();
            $currentRegister = $openRegisters->where('user_id', Auth::id())->first();
        } else {
            $currentRegister = CashRegister::open()->where('user_id', Auth::id())->first();
            $openRegisters = $currentRegister ? collect([$currentRegister]) : collect();
        }

        $config = Configuration::firstOrCreate(['id' => 1]);
        
        $pageConfig = [
            'routes' => [
                'index' => route('cash-registers.index'),
                'show' => route('cash-registers.show', ':id')
            ]
        ];

        return view('tenant.cash_registers.index', compact('currentRegister', 'openRegisters', 'config', 'pageConfig'));
    }

    public function create()
    {
        if (CashRegister::open()->where('user_id', Auth::id())->exists()) {
            return redirect()->route('cash-registers.index')->with('error', 'Ya tienes una caja abierta.');
        }

        $config = Configuration::first();
        $occupiedRegisters = CashRegister::open()->pluck('name')->toArray();

        return view('tenant.cash_registers.create', compact('config', 'occupiedRegisters'));
    }

    public function store(Request $request)
    {
        $config = Configuration::first();
        $allowedNames = $config && $config->cash_register_names ? $config->cash_register_names : [];

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($allowedNames) {
                    if (!empty($allowedNames) && !in_array($value, $allowedNames)) {
                        $fail('El identificador de caja seleccionado no es válido.');
                    }
                    
                    // Verificar que la caja no esté abierta por otro usuario
                    if (CashRegister::open()->where('name', $value)->exists()) {
                        $fail('Esta caja ya se encuentra abierta por otro usuario.');
                    }
                },
            ],
            'initial_amount' => 'required|numeric|min:0',
            'opening_date' => 'required|date',
            'observations' => 'nullable|string',
        ]);

        if (CashRegister::open()->where('user_id', Auth::id())->exists()) {
            return redirect()->route('cash-registers.index')->with('error', 'Ya tienes una caja abierta.');
        }

        $config = Configuration::first();
        
        CashRegister::create([
            'name' => $request->name,
            'opening_date' => $request->opening_date,
            'scheduled_closing_time' => $config ? $config->cash_register_closing_time : null,
            'initial_amount' => $request->initial_amount,
            'user_id' => Auth::id(),
            'status' => 'abierta',
            'observations' => $request->observations,
        ]);

        return redirect()->route('cash-registers.index')->with('success', 'Caja abierta correctamente.');
    }

    public function show(CashRegister $cashRegister)
    {
        // Si no es administrador y la caja no le pertenece, denegar acceso
        if (!Auth::user()->hasRole('Administrador') && $cashRegister->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para ver esta sesión de caja.');
        }

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
        // Si no es administrador y la caja no le pertenece, denegar acceso
        if (!Auth::user()->hasRole('Administrador') && $cashRegister->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para cerrar esta sesión de caja.');
        }

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
        // Si no es administrador y la caja no le pertenece, denegar acceso
        if (!Auth::user()->hasRole('Administrador') && $cashRegister->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para cerrar esta sesión de caja.');
        }

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
