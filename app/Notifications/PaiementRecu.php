<?php

namespace App\Notifications;

use App\Models\Inscription;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaiementRecu extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Inscription $inscription) {}

    public function via(object $notifiable): array
    {
        return ['mail', SmsChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $edition = $this->inscription->edition;

        return (new MailMessage)
            ->subject("Paiement confirmé — {$edition->nom}")
            ->greeting("Bonjour {$this->inscription->participant->prenom},")
            ->line("Votre inscription à {$edition->nom} est confirmée. Merci !")
            ->action('Télécharger mon récépissé', route('recu.telecharger', $this->inscription->code_inscription));
    }

    public function toSms(object $notifiable): string
    {
        return 'FOLEM: paiement confirme. Recepisse: '
            .route('recu.telecharger', $this->inscription->code_inscription);
    }
}
