# Changements apportés

Refonte du back-office sous TailAdmin, redesign du site public et de la page
d'inscription, migration Tailwind 3 vers Tailwind 4, corrections de sécurité.

---

## 1. Chaîne de build

| Fichier | Ce qui change |
|---|---|
| `package.json` | Tailwind 4 via `@tailwindcss/vite`, Alpine 3 + plugin collapse, ApexCharts, polices `@fontsource` |
| `vite.config.js` | Plugin Tailwind 4 ajouté |
| `tailwind.config.js` | **Supprimé** (la configuration passe en `@theme` CSS) |
| `postcss.config.js` | **Supprimé** (plus nécessaire avec le plugin Vite) |
| `resources/css/app.css` | Réécrit : tokens de charte, base TailAdmin, utilitaires |
| `resources/js/app.js` | Réécrit : stores `theme` et `sidebar`, composants `graphique` et `copier` |

Le projet embarquait à la fois Tailwind 3 en PostCSS et un `@tailwindcss/vite`
v4 inutilisé. Cette contradiction est levée.

ApexCharts est chargé par import dynamique : la bibliothèque (620 Ko) n'est
téléchargée que sur le tableau de bord, jamais sur le site public. Le bundle
principal tombe à 100 Ko, 36 Ko compressés.

Les polices sont auto-hébergées. Seuls les sous-ensembles latins d'Amiri sont
importés, ce qui économise environ 700 Ko de jeux arabes inutiles ici.

## 2. Charte graphique

Couleurs extraites directement des logos fournis : vert `#00A650`, orange
`#EF6325`. Deux échelles complètes de 25 à 950 (`brand-*`, `accent-*`), une base
neutre chaude `sand-*` pour le public, la base froide TailAdmin pour l'admin.

Logos traités et installés dans `public/img/brand/` : fond blanc rendu
transparent, version blanche pour les fonds sombres, icône carrée pour le
favicon. Le motif moucharabieh de la maquette est conservé mais recoloré en vert
de marque.

## 3. Back-office

**Layout** (`components/admin-layout.blade.php`, `admin/partials/sidebar`,
`admin/partials/header`) : sidebar à trois états (déployée, rail d'icônes avec
ouverture au survol, tiroir mobile), header avec bascule clair/sombre et menu
utilisateur, fil d'Ariane, lien d'évitement, script anti-flash du thème sombre.

L'ancienne sidebar faisait 60 lignes et restait à 256 px sur téléphone.

**Composants** (`components/admin/`) : `card`, `table`, `stat`, `badge`, `alert`,
`button`, `field`, `input`, `select`, `textarea`, `breadcrumb`, `empty`,
`confirm`, `icone`. Les quatorze vues admin sont réécrites dessus.

**Tableau de bord** : nouveau `Admin/DashboardController`. Les requêtes étaient
écrites directement dans le Blade, ce qui rendait la page intestable. L'écran
passe de trois compteurs à une courbe des inscriptions sur trente jours, une
répartition par région, une par profil, un taux de conversion, un panier moyen
et les dernières inscriptions.

**Inscriptions** : recherche libre (nom, e-mail, téléphone, structure, code),
filtre par statut, et surtout export Excel qui porte désormais sur *exactement*
le même périmètre que l'écran affiché. L'export gagne les colonnes région,
profil, jours de présence, besoins particuliers et canal de découverte.

**Régions et profils** : édition en ligne dans le tableau, et refus de
suppression étendu aux entrées déjà utilisées par des inscriptions (la version
précédente ne vérifiait que la grille tarifaire).

## 4. Site public

Page d'accueil réécrite en sections (`partials/accueil/`) : hero avec carte
d'informations clés, présentation CERFI et FOLEM, valeurs, grandes thématiques,
secteurs concernés, programme en frise avec onglets par jour, intervenants,
galerie, sponsoring, appel à inscription avec la marche à suivre, récupération de
dossier par code, infos pratiques avec carte.

L'ancienne page faisait 65 lignes et n'exploitait ni le programme, ni les
intervenants, ni les partenaires, pourtant présents en base. Chaque section
dispose d'un état vide rédigé, pour que la page reste présentable avant que le
contenu ne soit saisi.

Les contenus qui ne dépendent pas d'une édition sont centralisés dans
`config/folem.php` : coordonnées, textes de présentation, thématiques, secteurs,
canaux de découverte, niveaux de partenariat. Modifiables sans toucher aux vues.

Ajouts : navigation collante avec menu mobile, pied de page complet, métadonnées
Open Graph, page d'édition archivée, pages de paiement réussi et annulé
retravaillées, layout de connexion en deux colonnes.

## 5. Inscription

Formulaire découpé en trois étapes avec barre de progression : identité,
participation, confirmation. Tarif calculé en direct dès que région et profil
sont choisis, récapitulatif complet avant validation, écran final avec le code
en évidence et un bouton de copie.

**Nouveaux champs** demandés (migration
`2026_08_17_000001_add_details_participation_to_inscriptions_table`) :

- `jours_participation` (JSON) — les journées sont déduites des dates de
  l'édition, cochées toutes par défaut
- `besoins_particuliers` (texte) — restrictions alimentaires, accessibilité
- `source_connaissance` (chaîne indexée) — pour la commission communication

Le modèle `Inscription` gagne les casts correspondants, les libellés de statut
centralisés (`STATUTS`), `libelleStatut()`, `badgeStatut()`, `joursFormates()` et
les scopes `payees()` / `enAttente()`. Ces libellés étaient recopiés dans chaque
vue.

Les catégories et villes de la maquette d'inscription sont rattachées aux tables
`profils` et `regions` plutôt que codées en dur : elles restent pilotables depuis
le back-office et continuent d'alimenter la grille tarifaire.

## 6. Sécurité et corrections

**Corrigé — `WebhookController`.** Le `orWhere('inscription_id', …)` n'était pas
groupé dans son `when()`. La condition s'échappait de la parenthèse, si bien
qu'un payload transmettant un `custom_data.inscription_id` arbitraire pouvait
faire remonter le paiement d'une autre inscription. Le paiement est désormais
retrouvé par son seul jeton de transaction. Le traitement devient idempotent :
un callback rejoué par LigdiCash ne remarque plus l'inscription ni ne relance la
génération du récépissé, et l'écriture passe en transaction.

**Corrigé — route `/dashboard` déclarée deux fois** dans `web.php`. La seconde
écrasait silencieusement la première.

**Corrigé — `bootstrap/app.php`** appelait `withMiddleware()` deux fois ; le
premier appel, vide, était perdu. Les deux sont fusionnés, avec en prime les
alias `permission` et `role_or_permission` et la confiance aux proxies (sans
quoi les URL de callback repassent en `http` derrière Nginx ou Cloudflare).

**Corrigé — route `profile` manquante.** La vue existait, la route n'était pas
déclarée : tout lien vers `route('profile')` levait une exception.

**Ajouté — `Http/Middleware/SecurityHeaders`** : CSP, HSTS en HTTPS, nosniff,
X-Frame-Options, Referrer-Policy, Permissions-Policy.

**Ajouté — limitation de fréquence** sur la recherche par code, l'initiation de
paiement et le téléchargement de récépissé (10 par minute), sur le webhook (60
par minute) et sur la soumission d'inscription (5 par dix minutes et par IP).
Ces routes exposent un dossier à partir d'un simple code : sans limite, les
codes sont énumérables.

**Ajouté — champ leurre** dans le formulaire d'inscription, et revalidation
intégrale à la soumission finale. Le découpage en étapes est un confort
d'affichage, il ne fait pas office de garantie.

**Ajouté — messages d'erreur uniformes** sur la recherche par code : un code
inexistant et un code mal formé renvoient la même réponse.

**Ajouté — protection des brouillons.** Une édition en statut `draft` renvoyait
sa page publique ; elle renvoie maintenant une 404.

**Ajouté** — le montant d'une inscription est systématiquement relu en base au
moment de l'enregistrement, jamais repris du navigateur. Une inscription en
double sur la même édition renvoie le dossier existant au lieu de lever une
erreur de contrainte unique.

**Ajouté** — vérification de l'existence du fichier avant téléchargement d'un
récépissé, qui renvoyait auparavant une erreur serveur si le PDF manquait.

## 7. Divers

- `.env.example` : locale et fuseau français, variables LigdiCash et contenus
  FOLEM documentés, `LOG_STACK=daily`, cookies de session commentés
- `config/app.php` : locale `fr`, fuseau `Africa/Ouagadougou`
- `AppServiceProvider` : locale Carbon, HTTPS forcé en production
- `ParametrageSeeder` : treize régions plus Diaspora, cinq profils
- Vue de pagination personnalisée aux couleurs du projet
- Composants Breeze (boutons, champs, libellés) repris à la charte
- Vues mortes de Breeze supprimées (`welcome`, `dashboard` public)
- Deux fichiers de tests ajoutés : parcours public d'inscription, accès au
  back-office et en-têtes de sécurité

---

## Points restés en suspens

- La **CSP** autorise encore `'unsafe-inline'` et `'unsafe-eval'`, nécessaires
  aux expressions Alpine et à Livewire. Un durcissement par nonce demanderait de
  revoir les expressions inline, à envisager une fois le site stabilisé.
- Le champ `montant_inscription` sur `editions` est conservé comme tarif de repli
  mais la grille région × profil fait autorité. À supprimer quand toutes les
  éditions auront leur grille.
- Le **webhook LigdiCash n'est pas authentifié** par signature : l'API n'en
  propose pas. La revérification systématique auprès de l'API compense, mais une
  restriction par IP au niveau du serveur web serait un cran de plus.
- Je n'ai pas pu exécuter l'application dans mon environnement (ni PHP ni
  Composer, Packagist bloqué). Le CSS et le JS sont compilés et vérifiés, les
  vues Blade relues et contrôlées par script (équilibre des directives,
  existence de tous les composants et includes référencés), mais la première
  exécution reste à faire chez vous.
