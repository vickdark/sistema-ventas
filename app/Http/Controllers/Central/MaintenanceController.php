<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    public function index()
    {
        return view('central.maintenance.index');
    }

    public function runCommand(Request $request)
    {
        $command = $request->input('command');
        
        $allowedCommands = [
            'tenants:suspend-expired' => 'Suspensión de Inquilinos Vencidos',
            'app:auto-close-cash-registers' => 'Cierre Automático de Cajas',
            'optimize:clear' => 'Limpiar Caché del Sistema',
            'view:clear' => 'Limpiar Caché de Vistas',
            'config:clear' => 'Limpiar Caché de Configuración'
        ];

        if (!array_key_exists($command, $allowedCommands)) {
            return back()->with('error', 'Comando no permitido.');
        }

        try {
            Artisan::call($command);
            $output = Artisan::output();
            
            Log::info("Comando ejecutado manualmente desde el panel central: {$command}");
            
            return back()->with('status', "El comando '{$allowedCommands[$command]}' se ejecutó correctamente. Resultado: " . $output);
        } catch (\Exception $e) {
            Log::error("Error ejecutando comando {$command}: " . $e->getMessage());
            return back()->with('error', "Error al ejecutar el comando: " . $e->getMessage());
        }
    }
}
