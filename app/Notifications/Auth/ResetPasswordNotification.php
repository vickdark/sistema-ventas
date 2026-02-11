<?php

namespace App\Notifications\Auth;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;
use App\Traits\HasTenantEmailData;

class ResetPasswordNotification extends ResetPasswordBase
{
    use HasTenantEmailData;

    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $tenantData = $this->getTenantEmailData();

        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        return (new MailMessage)
            ->subject(Lang::get('Restablecer contraseña - ') . $tenantData['businessName'])
            ->view('emails.auth.reset-password', array_merge([
                'url' => url(route('password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false)),
                'notifiable' => $notifiable,
            ], $tenantData));
    }
}
