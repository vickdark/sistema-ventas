<?php

namespace App\Observers\Tenant;

use App\Models\Tenant\Account;
use App\Models\Tenant\JournalEntry;
use App\Models\Tenant\Expense;
use Illuminate\Support\Facades\DB;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        $this->createJournalEntry($expense);
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        // En MVP, solo creamos si no existe.
    }

    protected function createJournalEntry(Expense $expense)
    {
        if (JournalEntry::where('reference_type', Expense::class)->where('reference_id', $expense->id)->exists()) {
            return;
        }

        DB::transaction(function () use ($expense) {
            $entry = JournalEntry::create([
                'date' => $expense->expense_date ?? now(),
                'description' => "Gasto #{$expense->id} - " . ($expense->category->name ?? 'Gasto General'),
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
                'reference_number' => $expense->reference_voucher,
                'branch_id' => $expense->branch_id,
                'user_id' => $expense->user_id,
                'status' => 'posted',
            ]);

            // 1. DEBE (Debit) -> Gasto Operativo
            // Idealmente tomar la cuenta asociada a la categoría, pero usaremos 5.2.04 (Papelería y Útiles) como genérico o 5.2.03 (Servicios)
            // Code: 5.2.03 Servicios Básicos
            $debitAccount = Account::where('code', '5.2.03')->first();
            
            if ($debitAccount) {
                $entry->details()->create([
                    'account_id' => $debitAccount->id,
                    'debit' => $expense->amount,
                    'credit' => 0,
                ]);
            }

            // 2. HABER (Credit) -> Caja (1.1.01.01)
            $creditAccountCode = '1.1.01.01'; 
            $creditAccount = Account::where('code', $creditAccountCode)->first();

            if ($creditAccount) {
                $entry->details()->create([
                    'account_id' => $creditAccount->id,
                    'debit' => 0,
                    'credit' => $expense->amount,
                ]);
            }
        });
    }
}
