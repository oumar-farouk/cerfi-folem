<x-public-layout>
<div class="folem-reg">
    <style>
        .folem-reg{
            --night:#0F3D2E; --night-2:#14503C; --ochre:#B8901E; --ochre-light:#E8C766;
            --clay:#F05A28; --sand:#F1E7D6; --cream:#FBF6EC; --ink:#1B2E22; --line:rgba(27,46,34,0.14);
            font-family:'Work Sans', sans-serif; color:var(--ink); background:var(--cream);
        }
        .folem-reg h1,.folem-reg .font-display{ font-family:'Amiri', serif; }
        .folem-reg .hero{
            background:linear-gradient(160deg, var(--night) 0%, var(--night-2) 60%, #1A6650 100%);
            color:var(--cream); padding:44px 24px; text-align:center;
        }
        .folem-reg .code-box{
            border:2px dashed #7CC79A; background:#EFFBF3; border-radius:8px;
            padding:22px; text-align:center; letter-spacing:.15em;
        }
        .folem-reg .notice{ background:#FBF3E1; border:1px solid #EAD9A8; color:#8A6A1F; border-radius:8px; padding:16px 18px; }
        .folem-reg .row{ display:flex; justify-content:space-between; padding:14px 0; border-bottom:1px solid var(--line); }
        .folem-reg .row:last-of-type{ border-bottom:none; }

        /* 1. Styles communs aux boutons et liens CTA */
        .folem-reg .cta {
            display: block;
            width: 100%;
            color: #fff !important;
            text-decoration: none;
            border: none;
            border-radius: 6px;
            padding: 16px;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            letter-spacing: .02em;
            text-align: center;
            box-sizing: border-box;
        }

        /* 2. Couleurs spécifiques (sans le préfixe 'button') */
        .folem-reg .cta-pay {
            background-color: #F05A28 !important;
        }

        .folem-reg .cta-download {
            background-color: var(--night) !important;
        }
    </style>

    <div class="hero">
        <h1 class="text-3xl font-semibold">
            {{ $inscription->estPayee() ? 'Inscription payée' : 'Inscription enregistrée' }}
        </h1>
        <p class="text-[var(--ochre-light)] mt-1 font-medium">{{ $inscription->edition->nom }}</p>
    </div>

    <div class="max-w-lg mx-auto px-6 py-10">

        <p class="text-center text-sm text-gray-500 mb-3">Votre code d'inscription</p>

        <div class="code-box mb-3" x-data="copier('{{ $inscription->code_inscription }}')">
            <span class="text-3xl font-bold text-[var(--night)]">{{ $inscription->code_inscription }}</span>
        </div>
        <div class="text-center mb-6" x-data="copier('{{ $inscription->code_inscription }}')">
            <button type="button" x-on:click="copierTexte()" class="text-[var(--night)] font-semibold text-sm">
                <span x-show="!copie">Copier le code</span>
                <span x-show="copie" x-cloak>Copié !</span>
            </button>
        </div>

        @unless ($inscription->estPayee())
            <div class="notice mb-6 text-sm">
                Notez ce code et gardez-le. Il est le seul moyen de reprendre votre dossier, de payer plus tard
                ou de retélécharger votre récépissé.
            </div>
        @endunless

        <div class="mb-6">
            <div class="row">
                <span class="text-gray-500">Participant</span>
                <span class="font-semibold">{{ $inscription->participant->prenom }} {{ $inscription->participant->nom }}</span>
            </div>
            <div class="row">
                <span class="text-gray-500">{{ $inscription->estPayee() ? 'Montant payé' : 'Montant à régler' }}</span>
                <span class="font-display text-xl font-semibold text-[var(--night)]">
                    {{ number_format($inscription->montant, 0, ',', ' ') }} FCFA
                </span>
            </div>
        </div>

        @if ($inscription->estPayee())
            <a href="{{ route('recu.telecharger', $inscription->code_inscription) }}" class="cta cta-download block text-center">
                Télécharger mon récépissé
            </a>
        @else
            <a href="{{ route('paiement.initier', $inscription->code_inscription) }}" class="cta cta-pay block text-center">
                Payer maintenant par mobile money
            </a>
        @endif
    </div>
</div>
</x-public-layout>
