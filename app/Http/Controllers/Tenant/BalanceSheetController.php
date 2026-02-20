<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Account;
use Illuminate\Http\Request;

class BalanceSheetController extends Controller
{
    public function index()
    {
        // Obtener cuentas con saldo
        // O todas las cuentas para mostrar estructura completa.
        // Agrupar por tipo.
        
        $assets = Account::where('type', 'asset')->orderBy('code')->get();
        $liabilities = Account::where('type', 'liability')->orderBy('code')->get();
        $equity = Account::where('type', 'equity')->orderBy('code')->get();

        // Calcular totales usando current_balance
        // Nota: current_balance se actualiza en observers.
        // Si no confíamos en observers, calcularíamos sumando detalles.
        // Para MVP confiamos en current_balance.

        return view('tenant.accounting.balance_sheet.index', compact('assets', 'liabilities', 'equity'));
    }
}
