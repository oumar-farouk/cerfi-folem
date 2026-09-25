<?php

namespace App\Notifications\Channels;

use App\Services\Sms\SmsGateway;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    public function __construct(protected SmsGateway $gateway) {}

    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toSms')) {
            return;
        }

        $numero = $notifiable->routeNotificationFor('sms', $notification);
        $message = $notification->toSms($notifiable);

        try {
            $this->gateway->envoyer($numero, $message);
        } catch (\Throwable $e) {
            // Un SMS qui échoue ne doit jamais faire échouer l'email associé,
            // ni faire planter la file d'attente : on log et on continue.
            Log::error('Échec envoi SMS', ['numero' => $numero, 'erreur' => $e->getMessage()]);
        }
    }
}
