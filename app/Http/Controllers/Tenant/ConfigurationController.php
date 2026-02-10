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
        ]);

        $config = Configuration::firstOrCreate(['id' => 1]);
        $config->update([
            'cash_register_closing_time' => $request->cash_register_closing_time,
        ]);

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}
