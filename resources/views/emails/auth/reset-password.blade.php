<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #1e293b; margin: 0; padding: 0; }
        .wrapper { background-color: #f1f5f9; padding: 40px 20px; }
        .container { width: 100%; max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .header { background-color: #0f172a; padding: 30px; text-align: center; color: #ffffff; }
        .content { padding: 40px; }
        .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 0.8em; color: #64748b; border-top: 1px solid #e2e8f0; }
        .button { 
            display: inline-block; 
            background-color: #2563eb; 
            color: #ffffff !important; 
            padding: 18px 36px; 
            border-radius: 10px; 
            text-decoration: none; 
            font-weight: 800; 
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 30px 0;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
            border: 2px solid #1e40af;
        }
        .logo { max-height: 60px; margin-bottom: 15px; }
        h2 { margin: 0; font-size: 1.5rem; font-weight: 700; }
        p { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container">
            <div class="header">
                @if(isset($logoPath) && $logoPath)
                    <img src="{{ $message->embed($logoPath) }}" alt="{{ $businessName }}" class="logo">
                @endif
                <h2>Restablecer Contraseña</h2>
                <p style="margin-top: 10px; opacity: 0.8;">{{ $businessName }}</p>
            </div>
            <div class="content">
                <p>Hola,</p>
                <p>Recibes este correo porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta.</p>
                
                <div style="text-align: center;">
                    <a href="{{ $url }}" class="button">Restablecer Contraseña</a>
                </div>

                <p style="margin-top: 25px;">Este enlace de restablecimiento de contraseña caducará en {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutos.</p>
                <p>Si no has solicitado un restablecimiento de contraseña, no es necesario realizar ninguna otra acción.</p>
                
                <p>Saludos,<br>{{ $businessName }}</p>

                <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 30px 0;">
                <p style="font-size: 0.85rem; color: #64748b;">
                    Si tienes problemas al hacer clic en el botón "Restablecer Contraseña", copia y pega la siguiente URL en tu navegador: 
                    <br>
                    <span style="word-break: break-all; color: #2563eb;">{{ $url }}</span>
                </p>
            </div>
            <div class="footer">
                <p>Este es un correo automático de {{ $businessName }}.</p>
            </div>
        </div>
    </div>
</body>
</html>
