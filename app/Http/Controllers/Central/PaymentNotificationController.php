<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Central\CentralPaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentNotificationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $query = CentralPaymentNotification::with('tenant');

            // Grid.js envía los parámetros por defecto
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            $search = $request->get('search');

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('client_email', 'like', "%{$search}%")
                      ->orWhere('message', 'like', "%{$search}%")
                      ->orWhereHas('tenant', function($t) use ($search) {
                          $t->where('id', 'like', "%{$search}%")
                            ->orWhere('business_name', 'like', "%{$search}%");
                      });
                });
            }

            $total = $query->count();
            
            $notifications = $query->orderBy('created_at', 'desc')
                                   ->offset($offset)
                                   ->limit($limit)
                                   ->get()
                                   ->map(function($notification) {
                                       $tenant = $notification->tenant;
                                       // Obtener datos del JSON 'data' del tenant
                                       $tenantEmail = $tenant->email ?? ($tenant->data['email'] ?? 'No proporcionado');
                                       $tenantPhone = $tenant->phone ?? ($tenant->data['phone'] ?? 'No proporcionado');

                                       return [
                                           'id' => $notification->id,
                                           'tenant_name' => $tenant->business_name ?? $notification->tenant_id,
                                           'tenant_id' => $notification->tenant_id,
                                           'client_email' => $notification->client_email ?: 'No proporcionado',
                                           'tenant_contact' => [
                                               'email' => $tenantEmail,
                                               'phone' => $tenantPhone
                                           ],
                                           'date' => $notification->created_at->format('d/m/Y'),
                                           'time' => $notification->created_at->format('H:i'),
                                           'message' => $notification->message ?: '-',
                                           'status' => $notification->status,
                                           'attachment' => $notification->attachment_path ? asset('storage/' . $notification->attachment_path) : null,
                                           'is_pdf' => $notification->attachment_path ? str_ends_with(strtolower($notification->attachment_path), '.pdf') : false,
                                           'show_url' => route('central.payment-notifications.show', $notification),
                                           'download_url' => route('central.payment-notifications.download', $notification),
                                           'review_url' => route('central.payment-notifications.review', $notification),
                                           'delete_url' => route('central.payment-notifications.destroy', $notification),
                                       ];
                                   });

            return response()->json([
                'data' => $notifications,
                'total' => (int) $total,
                'status' => 'success'
            ]);
        }

        $config = [
            'routes' => [
                'index' => route('central.payment-notifications.index')
            ],
            'tokens' => [
                'csrf' => csrf_token()
            ]
        ];

        return view('central.payment-notifications.index', compact('config'));
    }

    public function show(CentralPaymentNotification $notification)
    {
        return view('central.payment-notifications.show', compact('notification'));
    }

    public function download(CentralPaymentNotification $notification)
    {
        if (!$notification->attachment_path) {
            return back()->with('error', 'Esta notificación no tiene un archivo adjunto.');
        }

        // El disco public central es donde se guardan los adjuntos
        if (!Storage::disk('public')->exists($notification->attachment_path)) {
            return back()->with('error', 'El archivo no existe en el servidor.');
        }

        return Storage::disk('public')->download($notification->attachment_path);
    }

    public function markAsReviewed(CentralPaymentNotification $notification)
    {
        $notification->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Notificación marcada como revisada.');
    }

    public function destroy(CentralPaymentNotification $notification)
    {
        if ($notification->attachment_path) {
            Storage::disk('public')->delete($notification->attachment_path);
        }

        $notification->delete();

        return redirect()->route('central.payment-notifications.index')
            ->with('success', 'Notificación eliminada.');
    }
}
