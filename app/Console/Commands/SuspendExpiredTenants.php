<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Tenant;
use Carbon\Carbon;

class SuspendExpiredTenants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:suspend-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspende automáticamente a los inquilinos cuya fecha de pago ha vencido.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        $expiredTenants = Tenant::where('is_paid', true)
            ->whereNotNull('next_payment_date')
            ->where('next_payment_date', '<=', $today)
            ->get();

        if ($expiredTenants->isEmpty()) {
            $this->info('No se encontraron inquilinos vencidos el día de hoy.');
            return;
        }

        $count = 0;
        foreach ($expiredTenants as $tenant) {
            $tenant->is_paid = false;
            $tenant->save();
            $this->warn("Inquilino suspendido: {$tenant->id} (Venció el: {$tenant->next_payment_date})");
            $count++;
        }

        $this->info("Proceso finalizado. Se suspendieron {$count} inquilinos.");
    }
}
