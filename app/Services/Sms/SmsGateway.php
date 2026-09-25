<?php

namespace App\Services\Sms;

interface SmsGateway
{
    public function envoyer(string $numero, string $message): void;
}
