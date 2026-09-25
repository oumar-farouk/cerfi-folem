<?php
namespace App\Notifications;

use App\Models\Inscription;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InscriptionEnregistree extends Notification implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable;

    public function __construct(protected Inscription $inscription) {}

    public function via(object $notifiable): array
    {
        // Le SMS échoue proprement (voir SmsChannel) si la passerelle n'est pas
        // configurée — l'email part quoi qu'il arrive.
        return ['mail', SmsChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $inscription = $this->inscription;
        $edition = $inscription->edition;

        return (new MailMessage)
            ->subject("Inscription enregistrée — {$edition->nom}")
            ->greeting("Bonjour {$inscription->participant->prenom},")
            ->line("Votre inscription à {$edition->nom} a bien été enregistrée.")
            ->line("Votre code d'inscription : **{$inscription->code_inscription}**")
            ->line("Montant à régler : ".number_format($inscription->montant, 0, ',', ' ')." FCFA")
            ->action('Procéder au paiement', route('paiement.initier', $inscription->code_inscription))
            ->line("Conservez ce code : il vous permet de reprendre votre dossier et de retélécharger votre récépissé à tout moment.");
    }

    /**
     * Message court dédié au SMS — pas de gras/liens, format texte brut, 160 caractères visés.
     */
    public function toSms(object $notifiable): string
    {
        $inscription = $this->inscription;

        return "FOLEM: inscription enregistree. Code: {$inscription->code_inscription}. "
            ."Montant: ".number_format($inscription->montant, 0, '', ' ')." FCFA. Payez sur folem.cerfi.bf";
    }
}
