<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; }
        .wrapper { background-color: #f1f5f9; padding: 40px 20px; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background-color: #0f172a; padding: 30px; text-align: center; color: #ffffff; }
        .content { padding: 40px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 0.8em; color: #64748b; border-top: 1px solid #e2e8f0; }
        .info-card { background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 20px; border-radius: 8px; margin: 20px 0; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .badge-blue { background-color: #dbeafe; color: #1e40af; }
        .badge-orange { background-color: #ffedd5; color: #9a3412; }
        h2 { margin: 0; font-size: 1.5rem; font-weight: 700; }
        .message-box { border-left: 4px solid #3b82f6; background-color: #eff6ff; padding: 15px; margin: 20px 0; font-style: italic; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                <h2>Validación de Pago Requerida</h2>
                <p style="margin-top: 10px; opacity: 0.8;">Un cliente ha enviado un comprobante de pago</p>
            </div>
            <div class="content">
                <p>Hola Administrador,</p>
                <p>El siguiente inquilino ha notificado un pago realizado para reactivar o mantener su servicio:</p>
                
                <div class="info-card">
                    <p style="margin: 0 0 10px 0;"><strong>Detalles del Cliente:</strong></p>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 5px 0; color: #64748b;">Empresa:</td>
                            <td style="padding: 5px 0; font-weight: 600;">{{ $tenant->business_name ?? $tenant->id }}</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0; color: #64748b;">ID Sistema:</td>
                            <td style="padding: 5px 0; font-weight: 600;"><code>{{ $tenant->id }}</code></td>
                        </tr>
                        @if($clientEmail)
                        <tr>
                            <td style="padding: 5px 0; color: #64748b;">Email de Contacto:</td>
                            <td style="padding: 5px 0; font-weight: 600;">{{ $clientEmail }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="padding: 5px 0; color: #64748b;">Tipo:</td>
                            <td style="padding: 5px 0;">
                                <span class="badge {{ $tenant->service_type === 'subscription' ? 'badge-blue' : 'badge-orange' }}">
                                    {{ $tenant->service_type === 'subscription' ? 'Suscripción' : 'Licencia' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>

                @if($messageText)
                    <p><strong>Mensaje del Cliente:</strong></p>
                    <div class="message-box">
                        "{{ $messageText }}"
                    </div>
                @endif

                @if($attachmentPath)
                    <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 8px; color: #166534; text-align: center;">
                        <p style="margin: 0;"><strong>📎 Comprobante adjunto incluido</strong></p>
                        <p style="margin: 5px 0 0 0; font-size: 0.85rem;">Por favor, revisa el archivo adjunto a este correo.</p>
                    </div>
                @else
                    <div style="background-color: #fff7ed; border: 1px solid #ffedd5; padding: 15px; border-radius: 8px; color: #9a3412; text-align: center;">
                        <p style="margin: 0;"><strong>⚠️ Sin comprobante adjunto</strong></p>
                        <p style="margin: 5px 0 0 0; font-size: 0.85rem;">El cliente envió la notificación sin imagen adjunta.</p>
                    </div>
                @endif

                <div style="margin-top: 30px; text-align: center;">
                    <a href="{{ config('app.url') }}/central/tenants/{{ $tenant->id }}/edit" 
                       style="background-color: #2563eb; color: #ffffff; padding: 12px 24px; border-radius: 6px; text-decoration: none; font-weight: 600; display: inline-block;">
                        Gestionar Inquilino en Panel Central
                    </a>
                </div>
            </div>
            <div class="footer">
                <p>Este es un correo automático del Sistema de Gestión Central.</p>
            </div>
        </div>
    </div>
</body>
</html>
