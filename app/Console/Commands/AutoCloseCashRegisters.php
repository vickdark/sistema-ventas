<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Central\Tenant;
use App\Models\Tenant\CashRegister;
use App\Models\Tenant\Sale;
use App\Models\Tenant\Abono;
use Carbon\Carbon;

class AutoCloseCashRegisters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auto-close-cash-registers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cierra automáticamente las cajas que han superado su hora programada de cierre.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando proceso de cierre automático de cajas para todos los clientes...');

        Tenant::all()->each(function ($tenant) {
            $this->comment("Procesando cliente: {$tenant->id}");
            
            try {
                // Inicializamos el contexto del tenant
                tenancy()->initialize($tenant);

                $now = Carbon::now();
                $currentTime = $now->format('H:i');

                // Usamos el namespace completo para evitar ambigüedades dentro del closure
                $openRegisters = \App\Models\Tenant\CashRegister::where('status', 'abierta')
                    ->where('scheduled_closing_time', '<=', $currentTime)
                    ->get();

                if ($openRegisters->isEmpty()) {
                    $this->line(" - No hay cajas pendientes por cerrar.");
                } else {
                    foreach ($openRegisters as $cashRegister) {
                        $this->line(" - Cerrando caja ID: {$cashRegister->id}");

                        $openingDate = $cashRegister->opening_date;

                        // Recalcular montos
                        $directSales = \App\Models\Tenant\Sale::whereIn('payment_type', ['CONTADO', 'TRANSFERENCIA'])
                            ->where('created_at', '>=', $openingDate)
                            ->get();
                        
                        $totalAbonos = \App\Models\Tenant\Abono::where('created_at', '>=', $openingDate)
                            ->sum('amount');

                        $totalIncome = $directSales->sum('total_paid') + $totalAbonos;
                        $expectedAmount = $cashRegister->initial_amount + $totalIncome;

                        $cashRegister->update([
                            'closing_date' => $now,
                            'final_amount' => $expectedAmount,
                            'status' => 'cerrada',
                            'observations' => $cashRegister->observations . "\n[CIERRE AUTOMÁTICO] Realizado a las " . $now->format('Y-m-d H:i:s'),
                            'sales_count' => $directSales->count(),
                            'total_sales' => $totalIncome,
                        ]);

                        $this->info("   ✔ Caja #{$cashRegister->id} cerrada exitosamente.");
                    }
                }

                // Finalizamos el contexto
                tenancy()->end();
                
            } catch (\Exception $e) {
                $this->error("Error procesando tenant {$tenant->id}: " . $e->getMessage());
            }
        });

        $this->info('Proceso de cierre automático finalizado.');
    }
}
