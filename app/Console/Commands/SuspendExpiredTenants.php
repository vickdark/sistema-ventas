<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Central\Tenant;
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
        $today = Carbon::today();
        $todayStr = $today->format('Y-m-d');
        
        $candidates = Tenant::where('data->is_paid', true)
            ->whereNotNull('data->next_payment_date')
            ->where('data->next_payment_date', '<', $todayStr)
            ->get();

        // Sin días de gracia: suspender a todo candidato con fecha vencida hoy o antes
        $expiredTenants = $candidates;

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
