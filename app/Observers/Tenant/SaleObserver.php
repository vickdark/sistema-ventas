<?php

namespace App\Observers\Tenant;

use App\Models\Tenant\Account;
use App\Models\Tenant\JournalEntry;
use App\Models\Tenant\Sale;
use Illuminate\Support\Facades\DB;

class SaleObserver
{
    /**
     * Handle the Sale "created" event.
     */
    public function created(Sale $sale): void
    {
        // Solo si la venta está pagada o parcialmente pagada
        // En este MVP asumimos que si se crea una venta, genera asiento.
        // Si es a crédito, el debe va a Cuentas por Cobrar.
        // Si es contado, va a Caja/Bancos.
        
        $this->createJournalEntry($sale);
    }

    /**
     * Handle the Sale "updated" event.
     */
    public function updated(Sale $sale): void
    {
        // Si cambia el estado de pago o monto, podríamos actualizar el asiento.
        // Por simplicidad en MVP, solo creamos si no existe, o anulamos y recreamos.
        // Aquí asumiremos que 'created' maneja la mayoría.
    }

    protected function createJournalEntry(Sale $sale)
    {
        // Evitar duplicados
        if (JournalEntry::where('reference_type', Sale::class)->where('reference_id', $sale->id)->exists()) {
            return;
        }

        DB::transaction(function () use ($sale) {
            $entry = JournalEntry::create([
                'date' => $sale->sale_date ?? now(),
                'description' => "Venta #{$sale->nro_venta} - Cliente: " . ($sale->client->name ?? 'Anónimo'),
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'reference_number' => $sale->nro_venta,
                'branch_id' => $sale->branch_id,
                'user_id' => $sale->user_id,
                'status' => 'posted',
            ]);

            // 1. DEBE (Debit)
            // Si es crédito -> Cuentas por Cobrar (1.1.02.01)
            // Si es contado -> Caja General (1.1.01.01) o Caja Chica/Bancos según método
            // Simplificamos: Si payment_status = 'pending' -> CxC. Si 'paid' -> Caja.
            
            $debitAccountCode = '1.1.01.01'; // Caja General por defecto
            if ($sale->payment_status === 'pending' || $sale->payment_status === 'partial') {
                $debitAccountCode = '1.1.02.01'; // Clientes Nacionales
            }
            
            // Buscar cuenta
            $debitAccount = Account::where('code', $debitAccountCode)->first();
            
            if ($debitAccount) {
                $entry->details()->create([
                    'account_id' => $debitAccount->id,
                    'debit' => $sale->total_paid,
                    'credit' => 0,
                ]);
            }

            // 2. HABER (Credit)
            // Ventas de Mercadería (4.1.01)
            // Podríamos separar IVA si el sistema lo desglosa. Asumiremos todo a Ventas por ahora o neto+iva.
            // Simplificación: Todo a Ventas.
             $creditAccountCode = '4.1.01.01'; // Ventas Mercadería - A veces es 4.1.01
             // En el seeder pusimos 4.1.01 (Ventas de Mercadería) como padre? No, nivel 3 is_movement=true
             // Code: 4.1.01
             
             $creditAccount = Account::where('code', '4.1.01')->first();

             if ($creditAccount) {
                $entry->details()->create([
                    'account_id' => $creditAccount->id,
                    'debit' => 0,
                    'credit' => $sale->total_paid, // Asumiendo sin desglose de impuestos por ahora
                ]);
             }
             
             // Actualizar saldos (Opcional, si usamos campo current_balance)
             if ($debitAccount) $debitAccount->increment('current_balance', $sale->total_paid);
             if ($creditAccount) $creditAccount->increment('current_balance', $sale->total_paid);
        });
    }
}
