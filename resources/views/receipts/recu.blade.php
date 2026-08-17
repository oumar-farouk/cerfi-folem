<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #1a1a1a; }
        .header { text-align: center; border-bottom: 3px solid #065f46; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #065f46; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        td { padding: 6px 0; border-bottom: 1px solid #e5e5e5; }
        td.label { font-weight: bold; width: 40%; color: #555; }
        .badge { background: #065f46; color: #fff; padding: 4px 12px; border-radius: 4px; font-size: 12px; }
        .footer { margin-top: 40px; text-align: center; font-size: 11px; color: #777; }
        .qr { text-align: center; margin-top: 25px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $edition->nom }}</h1>
        <p>Récépissé d'inscription N° {{ $numero }}</p>
        <span class="badge">PAIEMENT CONFIRMÉ</span>
    </div>

    <table>
        <tr><td class="label">Participant</td><td>{{ $participant->prenom }} {{ $participant->nom }}</td></tr>
        <tr><td class="label">Email</td><td>{{ $participant->email }}</td></tr>
        <tr><td class="label">Téléphone</td><td>{{ $participant->telephone }}</td></tr>
        <tr><td class="label">Structure</td><td>{{ $participant->structure ?? '—' }}</td></tr>
        <tr><td class="label">Code d'inscription</td><td>{{ $inscription->code_inscription }}</td></tr>
        <tr><td class="label">Montant payé</td><td>{{ number_format($inscription->montant, 0, ',', ' ') }} FCFA</td></tr>
        <tr><td class="label">Date de paiement</td><td>{{ $inscription->paid_at?->format('d/m/Y à H:i') }}</td></tr>
        <tr><td class="label">Édition</td><td>{{ $edition->theme }} — {{ $edition->lieu }}</td></tr>
        <tr><td class="label">Dates</td><td>{{ $edition->date_debut->format('d/m/Y') }} au {{ $edition->date_fin->format('d/m/Y') }}</td></tr>
    </table>

    <div class="qr">
        <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(120)->generate(route('verification.show', ['code' => $inscription->code_inscription, 'hash' => $hash]))) }}"
            width="120" height="120" alt="QR code de vérification">
        <p style="font-size:10px;color:#999;">Scanner pour vérifier ce récépissé à l'entrée</p>
    </div>
    <div class="footer">
        Document généré automatiquement - {{ config('app.name') }}<br>
        Toute falsification est passible de poursuites.
    </div>
</body>
</html>
