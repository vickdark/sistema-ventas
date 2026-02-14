<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\CentralSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $adminEmails = CentralSetting::get('admin_payment_emails', '');
        $gateKeySetting = CentralSetting::where('key', 'central_login_gate_key')->first();
        $currentKey = $gateKeySetting ? $gateKeySetting->value : '';
        return view('central.settings.index', compact('adminEmails', 'currentKey'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'admin_payment_emails' => 'required|string',
        ]);

        // Validar que sean correos válidos
        $emails = array_map('trim', explode(',', $request->admin_payment_emails));
        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return back()->with('error', "El correo '$email' no es válido.")->withInput();
            }
        }

        CentralSetting::set('admin_payment_emails', $request->admin_payment_emails, 'Correos de administración para recibir notificaciones de pago (separados por coma)');

        return back()->with('success', 'Configuraciones actualizadas correctamente.');
    }
}
