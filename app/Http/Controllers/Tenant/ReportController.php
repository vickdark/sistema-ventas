<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Abono;
use App\Models\Tenant\CashRegister;
use App\Models\Tenant\Category;
use App\Models\Tenant\Product;
use App\Models\Tenant\Purchase;
use App\Models\Tenant\Sale;
use App\Models\Tenant\SaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // 1. TOP PRODUCTOS MÁS VENDIDOS
        $topProductos = SaleItem::with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_vendido'))
            ->groupBy('product_id')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->get();

        // 2. VENTAS SEMANALES (Últimos 7 días)
        $ventasSemana = Sale::select(
                DB::raw('DATE(sale_date) as fecha'),
                DB::raw('SUM(total_paid) as total')
            )
            ->where('sale_date', '>=', now()->subDays(7))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        // 3. CAJA (Últimos 10 Arqueos)
        $datosCaja = CashRegister::orderByDesc('id')
            ->limit(10)
            ->get();

        // 4. CATEGORÍAS CON MÁS PRODUCTOS
        $catProductos = Category::withCount('products')
            ->get();

        // 5. INGRESOS DIARIOS, MENSUALES Y ANUALES
        $ingresoDiario = Sale::whereDate('sale_date', today())->sum('total_paid');
        $ingresoMensual = Sale::whereYear('sale_date', now()->year)
            ->whereMonth('sale_date', now()->month)
            ->sum('total_paid');
        $ingresoAnual = Sale::whereYear('sale_date', now()->year)->sum('total_paid');

        // 6. DEUDAS / CRÉDITOS PENDIENTES
        $totalCreditos = Sale::where('payment_type', 'CREDITO')
            ->where('payment_status', '!=', 'PAGADO')
            ->sum('total_paid');
        
        $totalAbonos = Abono::whereHas('sale', function($query) {
            $query->where('payment_type', 'CREDITO')
                  ->where('payment_status', '!=', 'PAGADO');
        })->sum('amount');

        $deudaTotalClientes = $totalCreditos - $totalAbonos;
        $cantidadCreditosPendientes = Sale::where('payment_type', 'CREDITO')
            ->where('payment_status', '!=', 'PAGADO')
            ->count();

        // 7. BALANCE MENSUAL (INGRESOS VS EGRESOS)
        $balanceMensual = [];
        for ($i = 1; $i <= 12; $i++) {
            $ingresos = Sale::whereYear('sale_date', now()->year)
                ->whereMonth('sale_date', $i)
                ->sum('total_paid');
            
            $egresos = Purchase::whereYear('purchase_date', now()->year)
                ->whereMonth('purchase_date', $i)
                ->sum('total');

            $balanceMensual[] = [
                'mes' => $i,
                'ingresos' => $ingresos,
                'egresos' => $egresos
            ];
        }

        // 8. DISTRIBUCIÓN POR MÉTODOS DE PAGO
        $metodosPago = Sale::select('payment_type', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(total_paid) as total'))
            ->groupBy('payment_type')
            ->get();

        // 9. VALOR TOTAL DEL INVENTARIO ACTUAL
        $valorInventario = Product::select(DB::raw('SUM(purchase_price * stock) as total'))
            ->first()
            ->total ?? 0;

        // 10. EFECTIVO VS TRANSFERENCIA (solo pagos inmediatos)
        $efectivoVsTransferencia = Sale::select('payment_type', DB::raw('SUM(total_paid) as total'))
            ->whereIn('payment_type', ['CONTADO', 'TRANSFERENCIA'])
            ->groupBy('payment_type')
            ->get();

        return view('tenant.reports.index', [
            'ventasSemana' => $ventasSemana->map(fn($v) => [
                'date' => \Carbon\Carbon::parse($v->fecha)->format('d/m'),
                'total' => $v->total
            ]),
            'topProductos' => $topProductos->map(fn($t) => [
                'name' => $t->product->name ?? 'Desconocido',
                'total' => $t->total_vendido
            ]),
            'datosCaja' => $datosCaja->map(fn($c) => [
                'date' => \Carbon\Carbon::parse($c->opening_date)->format('d/m'),
                'name' => $c->name,
                'initial_amount' => $c->initial_amount,
                'final_amount' => $c->final_amount
            ]),
            'catProductos' => $catProductos->map(fn($cp) => [
                'name' => $cp->name,
                'total' => $cp->products_count
            ]),
            'balanceMensual' => $balanceMensual,
            'metodosPago' => $metodosPago->map(fn($m) => [
                'type' => $m->payment_type,
                'total' => $m->total
            ]),
            'efectivoVsTransferencia' => $efectivoVsTransferencia->map(fn($e) => [
                'type' => $e->payment_type,
                'total' => $e->total
            ]),
            'stats' => [
                'ingresoDiario' => number_format($ingresoDiario, 2),
                'ingresoMensual' => number_format($ingresoMensual, 2),
                'ingresoAnual' => number_format($ingresoAnual, 2),
                'deudaTotalClientes' => number_format($deudaTotalClientes, 2),
                'cantidadCreditosPendientes' => $cantidadCreditosPendientes,
                'valorInventario' => number_format($valorInventario, 2)
            ],
            'ingresoDiario' => $ingresoDiario,
            'ingresoMensual' => $ingresoMensual,
            'ingresoAnual' => $ingresoAnual,
            'deudaTotalClientes' => $deudaTotalClientes,
            'cantidadCreditosPendientes' => $cantidadCreditosPendientes,
            'valorInventario' => $valorInventario
        ]);
    }
}
