<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Mail\PaymentNotificationMail;
use App\Models\Central\CentralSetting;
use App\Models\Central\CentralPaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaymentNotificationController extends Controller
{
    public function send(Request $request)
    {
        Log::info("Iniciando proceso de notificación de pago.");
        
        $request->validate([
            'client_email' => 'nullable|email',
            'message' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:5120',
        ]);

        $tenant = tenant();
        
        // 1. Buscar correos de administración globales
        $adminEmailsRaw = CentralSetting::get('admin_payment_emails', '');
        $adminEmails = array_filter(array_map('trim', explode(',', $adminEmailsRaw)));
        
        Log::info("Correos globales encontrados: " . implode(', ', $adminEmails));

        // 2. Añadir el correo proporcionado por el cliente
        if ($request->client_email) {
            $adminEmails[] = trim($request->client_email);
            Log::info("Añadido correo del cliente: " . $request->client_email);
        }

        // Eliminar duplicados
        $finalEmails = array_unique($adminEmails);

        // 3. Fallback si no hay nada
        if (empty($finalEmails)) {
            $fallbackEmail = config('mail.from.address') ?: 'notificacioneskemuel@gmail.com';
            $finalEmails = [$fallbackEmail];
            Log::info("Usando fallback de correo: " . $fallbackEmail);
        }

        Log::info("Destinatarios finales (antes de enviar): " . implode(', ', $finalEmails));

        $attachmentPath = null;
        $relativePath = null;
        if ($request->hasFile('attachment')) {
            // Guardar el archivo en una carpeta temporal para que el Mailable pueda acceder a él
            // Usamos storeAs para mantener el nombre original o uno controlado
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $relativePath = $file->storeAs('payment_proofs', $fileName, 'public');
            $attachmentPath = Storage::disk('public')->path($relativePath);
            Log::info("Adjunto guardado en: " . $attachmentPath);
        }

        // 3. Guardar en la bandeja central
        try {
            CentralPaymentNotification::create([
                'tenant_id' => $tenant->id,
                'client_email' => $request->client_email,
                'message' => $request->message,
                'attachment_path' => $relativePath, // Guardamos la ruta relativa al disco public
                'status' => 'pending',
            ]);
            Log::info("Notificación guardada en la base de datos central.");
        } catch (\Exception $e) {
            Log::error("Error al guardar en la base de datos central: " . $e->getMessage());
            // No bloqueamos el proceso si falla el guardado central, pero lo registramos
        }

        try {
            $mail = Mail::to($finalEmails);
            
            Log::info("Intentando enviar correo a través de Mail::to()->send()");
            
            $mail->send(new PaymentNotificationMail(
                $tenant,
                $request->message,
                $attachmentPath,
                $request->client_email
            ));

            Log::info("Mail::send() ejecutado sin excepciones.");

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => '¡Comprobante enviado con éxito! El administrador revisará su pago y reactivará el servicio lo antes posible.'
                ]);
            }

            return back()->with('success', '¡Comprobante enviado con éxito! El administrador revisará su pago y reactivará el servicio lo antes posible.');
        } catch (\Exception $e) {
            Log::error("EXCEPCIÓN al enviar correo: " . $e->getMessage());
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al enviar el correo: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Error al enviar el correo: ' . $e->getMessage());
        }
    }
}
