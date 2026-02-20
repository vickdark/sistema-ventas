<?php

namespace App\Observers\Tenant;

use App\Models\Tenant\Account;
use App\Models\Tenant\JournalEntry;
use App\Models\Tenant\Purchase;
use Illuminate\Support\Facades\DB;

class PurchaseObserver
{
    /**
     * Handle the Purchase "created" event.
     */
    public function created(Purchase $purchase): void
    {
        // Al crear compra, se carga inventario y se genera deuda o sale caja
        $this->createJournalEntry($purchase);
    }

    /**
     * Handle the Purchase "updated" event.
     */
    public function updated(Purchase $purchase): void
    {
        // En MVP, solo creamos si no existe.
    }

    protected function createJournalEntry(Purchase $purchase)
    {
        if (JournalEntry::where('reference_type', Purchase::class)->where('reference_id', $purchase->id)->exists()) {
            return;
        }

        DB::transaction(function () use ($purchase) {
            $entry = JournalEntry::create([
                'date' => $purchase->purchase_date ?? now(),
                'description' => "Compra #{$purchase->nro_compra} - Proveedor: " . ($purchase->supplier->business_name ?? 'Desconocido'),
                'reference_type' => Purchase::class,
                'reference_id' => $purchase->id,
                'reference_number' => $purchase->nro_compra,
                'branch_id' => $purchase->branch_id,
                'user_id' => $purchase->user_id,
                'status' => 'posted',
            ]);

            // 1. DEBE (Debit) -> Inventario (1.1.03.01 Mercaderías)
            // Asumimos compra de bienes
            $debitAccount = Account::where('code', '1.1.03.01')->first();
            
            if ($debitAccount) {
                $entry->details()->create([
                    'account_id' => $debitAccount->id,
                    'debit' => $purchase->total, // Asumimos total entra a inventario (o neto+iva si no desglosamos)
                    'credit' => 0,
                ]);
            }

            // 2. HABER (Credit)
            // Si es Crédito -> Proveedores Nacionales (2.1.01.01)
            // Si es Contado -> Caja General (1.1.01.01)
            
            $creditAccountCode = '2.1.01.01'; // Default: Create debt (Proveedores)
            if ($purchase->payment_status === 'paid') {
                $creditAccountCode = '1.1.01.01'; // Caja
            }

            $creditAccount = Account::where('code', $creditAccountCode)->first();

            if ($creditAccount) {
                $entry->details()->create([
                    'account_id' => $creditAccount->id,
                    'debit' => 0,
                    'credit' => $purchase->total,
                ]);
            }
        });
    }
}
