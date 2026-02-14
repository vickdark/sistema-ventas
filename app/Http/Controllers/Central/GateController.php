<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\CentralSetting;

class GateController extends Controller
{
    /**
     * Verify the gate key submitted by the user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyKey(Request $request)
    {
        $request->validate([
            'gate_key' => 'required|string',
        ]);

        $correctKey = CentralSetting::where('key', 'central_login_gate_key')->value('value');

        if ($request->input('gate_key') === $correctKey) {
            Session::put('central_gate_passed', true);
            return redirect()->route('central.login')->with('status', '¡Clave correcta! Acceso concedido.');
        }

        return back()->withErrors(['gate_key' => 'Clave de acceso incorrecta.'])->withInput();
    }

    /**
     * Show the form for editing the central login gate key.
     *
     * @return \Illuminate\View\View
     */
    public function editGateKey()
    {
        $gateKeySetting = CentralSetting::where('key', 'central_login_gate_key')->first();
        $currentKey = $gateKeySetting ? $gateKeySetting->value : '';

        // Obtener también los datos para la vista de settings general
        $adminEmails = CentralSetting::where('key', 'admin_payment_emails')->value('value');

        return view('central.settings.index', compact('currentKey', 'adminEmails'));
    }

    /**
     * Update the central login gate key.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGateKey(Request $request)
    {
        $request->validate([
            'gate_key' => 'required|string|min:4',
        ]);

        CentralSetting::updateOrCreate(
            ['key' => 'central_login_gate_key'],
            ['value' => $request->input('gate_key'), 'description' => 'Clave de acceso para el login central']
        );

        Session::flash('status', 'La clave de acceso ha sido actualizada correctamente.');

        return redirect()->route('central.settings.index');
    }
}
