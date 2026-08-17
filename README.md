# FOLEM — plateforme du Forum du Leadership et de l'Entrepreneuriat Musulmans

Application Laravel 12 qui gère les éditions du FOLEM : site public, inscriptions
en ligne, paiement mobile money via LigdiCash, génération des récépissés et
back-office de pilotage.

## Prérequis

- PHP 8.2 ou plus, avec les extensions `gd`, `zip`, `intl`, `sqlite3` ou `pdo_mysql`
- Composer 2
- Node.js 20 ou plus

## Mise en route

```bash
composer install
cp .env.example .env
php artisan key:generate

# SQLite par défaut
touch database/database.sqlite

php artisan migrate --seed
php artisan storage:link      # indispensable : photos, logos et récépissés

npm install
npm run build                 # ou npm run dev pendant le développement

php artisan serve
```

Le seeder crée un compte administrateur : `admin@cerfi.bf` / `password`.
**Changez ce mot de passe avant toute mise en ligne.**

Il crée également les treize régions du Burkina Faso plus « Diaspora », cinq
profils de participation, et trois éditions de démonstration.

## Configuration du paiement

Renseignez dans `.env` les clés récupérées sur le tableau de bord LigdiCash :

```
LIGDICASH_API_KEY=
LIGDICASH_AUTH_TOKEN=
LIGDICASH_PLATFORM=test
```

Le callback `POST /webhooks/ligdicash` doit être joignable publiquement. En
développement local, passez par un tunnel (ngrok, expose) et déclarez l'URL
correspondante côté LigdiCash. Sans callback joignable, les paiements ne
seront confirmés qu'au retour du navigateur sur `/paiement/succes`.

## Premiers pas dans le back-office

1. Connectez-vous sur `/login`, vous arrivez sur `/admin`.
2. Créez ou modifiez une édition, passez-la en statut **Active**. Une seule
   édition peut être active à la fois, les autres sont archivées automatiquement.
3. Renseignez la **grille tarifaire** : un montant par croisement région × profil.
   Une case vide retire la combinaison du formulaire d'inscription.
4. Ajoutez le **programme**, les **intervenants**, les **partenaires** et la
   **galerie**. Tout remonte automatiquement sur la page d'accueil.

## Parcours du participant

1. Formulaire en trois étapes sur `/editions/{slug}/inscription`.
2. Un code du type `FLM-XXXXXX` est remis à la fin, l'inscription est en attente.
3. Paiement mobile money immédiat ou plus tard, en saisissant le code depuis la
   page d'accueil.
4. Récépissé PDF téléchargeable dès la confirmation du paiement.

## Architecture

```
app/
  Http/Controllers/Admin/     back-office
  Http/Controllers/Site/      pages publiques
  Http/Middleware/            en-têtes de sécurité
  Livewire/Registration/      formulaire d'inscription en étapes
  Services/                   LigdiCash, génération des récépissés
config/folem.php              contenus éditoriaux du site public
resources/views/
  components/admin/           bibliothèque de composants du back-office
  components/site/            composants du site public
  partials/accueil/           sections de la page d'accueil
  admin/partials/             sidebar et header du back-office
```

Le fichier `config/folem.php` centralise ce qui ne dépend pas d'une édition :
coordonnées, texte de présentation, grandes thématiques, secteurs d'activité,
niveaux de partenariat. Modifiez-le sans toucher aux vues.

## Design

Charte construite à partir des logos officiels : vert `#00A650` et orange
`#EF6325`, déclinés en deux échelles complètes dans `resources/css/app.css`
(tokens `brand-*` et `accent-*`). Base neutre chaude `sand-*` pour le site
public, base froide type TailAdmin pour le back-office.

Polices auto-hébergées via `@fontsource` : Outfit pour l'interface, Amiri pour
les titres. Aucun appel à un CDN au chargement, ce qui compte sur une connexion
lente.

## Tests

```bash
php artisan test
```

## Mise en production

- `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` en `https`
- `SESSION_SECURE_COOKIE=true`
- `php artisan config:cache route:cache view:cache`
- `npm run build` puis déploiement du dossier `public/build`
- `php artisan storage:link` sur le serveur cible
- Un worker de file d'attente si vous activez les envois de courriel :
  `php artisan queue:work`
