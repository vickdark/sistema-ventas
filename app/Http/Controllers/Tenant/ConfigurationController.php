<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Configuration;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index()
    {
        $config = Configuration::firstOrCreate(['id' => 1]);
        return view('tenant.configurations.index', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'cash_register_closing_time' => 'nullable|date_format:H:i',
            'cash_register_names' => 'nullable|string', // Vendrá como string separado por comas o similar
        ]);

        $config = Configuration::firstOrCreate(['id' => 1]);
        
        $names = null;
        if ($request->cash_register_names) {
            $names = array_map('trim', explode(',', $request->cash_register_names));
            $names = array_filter($names); // Eliminar vacíos
        }

        $config->update([
            'cash_register_closing_time' => $request->cash_register_closing_time,
            'cash_register_names' => $names,
        ]);

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}
