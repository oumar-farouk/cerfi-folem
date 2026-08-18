<?php

/*
|--------------------------------------------------------------------------
| Contenus éditoriaux du site public
|--------------------------------------------------------------------------
| Tout ce qui ne varie pas d'une édition à l'autre est centralisé ici plutôt
| que codé en dur dans les vues : coordonnées, texte de présentation, grandes
| thématiques, secteurs d'activité, liens partenaires institutionnels.
| Les données propres à une édition (dates, lieu, thème, programme,
| intervenants, partenaires, tarifs) restent en base et sont pilotées
| depuis le back-office.
*/

return [

    'organisation' => [
        'nom' => 'CERFI',
        'nom_complet' => "Cercle d'Études, de Recherches et de Formation Islamiques",
        'site' => 'https://cerfi.bf/',
    ],

    'contact' => [
        'adresse' => '1200 Logements, Ouagadougou, Burkina Faso',
        'email' => env('FOLEM_CONTACT_EMAIL', 'contact@cerfi.bf'),
        'telephones' => ['+226 76 02 39 32', '+226 65 48 67 55', '+226 70 32 24 35'],
        'carte_url' => 'https://www.google.com/maps/d/embed?mid=1lFCRo-rt3nydWg2R9hqp_6QDKdk&ehbc=2E312F',
    ],

    'reseaux' => [
        'facebook' => env('FOLEM_FACEBOOK_URL'),
        'linkedin' => env('FOLEM_LINKEDIN_URL'),
        'youtube' => env('FOLEM_YOUTUBE_URL'),
        'whatsapp' => env('FOLEM_WHATSAPP_URL'),
    ],

    'liens_utiles' => [
        ['libelle' => 'CERFI', 'url' => 'https://cerfi.bf/'],
        ['libelle' => 'AEEMB', 'url' => 'https://aeemb.bf/'],
        ['libelle' => 'FAIB', 'url' => 'https://faib.bf/'],
        ['libelle' => 'OJEMAO', 'url' => 'https://www.ojemao.org'],
    ],

    'presentation' => [
        'cerfi' => "Le Cercle d'Études, de Recherches et de Formation Islamiques est une association "
            ."de développement de droit burkinabè, à caractère religieux, reconnue officiellement le "
            ."23 juin 1989. Il promeut l'étude et la recherche dans les divers domaines de l'Islam, "
            ."forme et informe celles et ceux qui le souhaitent, travaille à l'unité d'action et à la "
            ."tolérance entre les composantes de la communauté musulmane, et contribue au développement "
            .'social, culturel et économique du pays.',

        'folem' => "Le Forum du Leadership et de l'Entrepreneuriat Musulmans est né en 2022 d'une "
            ."volonté simple : offrir aux entrepreneurs et cadres musulmans un cadre national de "
            ."rencontre, de formation et d'affaires. La première édition a réuni près de 500 "
            ."participants venus des treize régions du Burkina Faso. Devant cet accueil, le CERFI a "
            .'institué le forum comme rendez-vous biennal.',
    ],

    /*
    | Valeurs mises en avant sur la page d'accueil. Le libellé arabe est repris
    | de la maquette validée par le comité d'organisation.
    */
    'valeurs' => [
        [
            'titre' => 'Amanah',
            'sous_titre' => 'Confiance',
            'texte' => "L'engagement tenu, la parole qui vaut contrat, la gestion honnête des ressources d'autrui.",
        ],
        [
            'titre' => 'Adl',
            'sous_titre' => 'Justice',
            'texte' => 'Des relations d\'affaires équilibrées, une rémunération juste, un partage équitable du risque.',
        ],
        [
            'titre' => 'Maslaha',
            'sous_titre' => 'Intérêt général',
            'texte' => "L'entreprise au service de la communauté, créatrice d'emplois et de valeur locale.",
        ],
    ],

    'thematiques' => [
        [
            'titre' => 'Business',
            'texte' => 'Les secteurs porteurs au Burkina Faso : agro-business, digital, services aux mines, gastronomie.',
        ],
        [
            'titre' => 'Digital',
            'texte' => 'Marketing digital, e-commerce, usages concrets de l\'intelligence artificielle dans la PME.',
        ],
        [
            'titre' => 'Financement',
            'texte' => 'Finance islamique, actionnariat, finance classique, fonds nationaux et autres leviers de financement.',
        ],
    ],

    /*
    | Secteurs d'activité proposés au participant lors de l'inscription.
    | La validation du formulaire s'appuie sur ces clés.
    */
    'secteurs' => [
        'Administration Publique' => 'Agents et cadres de la fonction publique',
        'Secteur Privé' => 'Salariés et dirigeants du secteur privé',
        'ONG' => 'Organisations non gouvernementales',
        'Association' => 'Associations de développement',
        'Entrepreneur' => "Chefs d'entreprise et hommes d'affaires",
        'Institution' => 'Institutions nationales ou internationales',
    ],

    /*
    | Canaux proposés dans « Comment avez-vous connu le FOLEM ? ».
    */
    'sources_connaissance' => [
        'Réseaux sociaux',
        'Bouche à oreille',
        'CERFI / AEEMB',
        'Mosquée ou association',
        'Presse / radio ou télévision',
        'Édition précédente du FOLEM',
        'Employeur ou partenaire',
        'Autre',
    ],

    /*
    | Niveaux de partenariat affichés dans la section sponsoring.
    */
    'packs_partenariat' => [
        [
            'niveau' => 'Platine',
            'texte' => 'Visibilité maximale, prise de parole en plénière, stand premium et mention sur tous les supports.',
        ],
        [
            'niveau' => 'Or',
            'texte' => 'Logo sur les supports officiels, stand d\'exposition et insertion dans le livret du forum.',
        ],
        [
            'niveau' => 'Argent',
            'texte' => 'Présence dans le livret, mention sur le site et sur les écrans du forum.',
        ],
    ],
];
