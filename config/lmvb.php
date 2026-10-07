<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identité de la Ligue (valeurs par défaut, modifiables dans Admin > Réglages)
    |--------------------------------------------------------------------------
    */
    'name' => 'Ligue Martiniquaise de Volley-Ball',
    'short_name' => 'LMVB',

    /*
    |--------------------------------------------------------------------------
    | Synchronisation des résultats FFVolley
    |--------------------------------------------------------------------------
    | La ligue publie ses compétitions sur la plateforme FFVolley sous le code
    | d'entité LIMART. L'import utilise l'export CSV officiel (matchs) et la page
    | calendrier (classements).
    */
    'ffvb' => [
        'enabled' => env('FFVB_SYNC_ENABLED', true),
        'base_url' => 'https://www.ffvbbeach.org/ffvbapp/resu',
        'entity' => env('FFVB_ENTITY', 'LIMART'),
        // Déclenchement en arrière-plan lors d'une visite, au plus toutes les N minutes.
        'sync_on_visit' => env('FFVB_SYNC_ON_VISIT', true),
        'interval_minutes' => (int) env('FFVB_SYNC_INTERVAL', 30),
        'timeout' => 20,
        'user_agent' => 'LMVB-Site/1.0 (+https://lmvb972.fr)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Clubs connus (clé = numéro d'affiliation FFVolley)
    |--------------------------------------------------------------------------
    | Utilisé à la création automatique d'un club par l'import pour avoir un nom
    | propre (accents, casse), un nom court et la commune. Les valeurs déjà
    | saisies dans l'admin ne sont jamais écrasées.
    */
    'clubs' => [
        '9725554' => ['name' => 'Martinique Université Club', 'short_name' => 'MUC', 'city' => 'Schœlcher', 'color' => '#1F3A93'],
        '9722869' => ['name' => 'Racing Club Arlésien', 'short_name' => 'RCA', 'city' => "Les Anses-d'Arlet", 'color' => '#C2185B', 'founded_year' => 1947, 'facebook' => 'https://www.facebook.com/RacingClubArlesienPageOfficiel', 'instagram' => 'https://www.instagram.com/racing.club.arlesien'],
        '9722556' => ['name' => 'Racing Club de Rivière-Pilote', 'short_name' => 'RCRP', 'city' => 'Rivière-Pilote', 'color' => '#6A1B9A'],
        '9727217' => ['name' => 'Le Rayon de Petite Anse', 'short_name' => 'Le Rayon', 'city' => "Les Anses-d'Arlet", 'color' => '#F9A825'],
        '9727670' => ['name' => 'Good-Luck Volley-Ball', 'short_name' => 'Good-Luck', 'city' => 'Fort-de-France', 'color' => '#2E7D32', 'instagram' => 'https://www.instagram.com/goodluck_volleyball'],
        '9722828' => ['name' => 'Espoir de Sainte-Luce', 'short_name' => 'Espoir', 'city' => 'Sainte-Luce', 'color' => '#0277BD', 'facebook' => 'https://www.facebook.com/Espoir.ste.Luce'],
        '9722553' => ['name' => "T'Impulz", 'short_name' => "T'Impulz", 'city' => 'Schœlcher', 'color' => '#EF6C00'],
        '9722544' => ['name' => 'Empire Volley-Ball Club', 'short_name' => 'Empire', 'city' => 'La Trinité', 'color' => '#212121', 'facebook' => 'https://www.facebook.com/evbc972'],
        '9722543' => ['name' => 'AS Racing Club du Morne-des-Esses', 'short_name' => 'Morne-des-Esses', 'city' => 'Sainte-Marie', 'color' => '#AD1457'],
        '9722555' => ['name' => 'Pôle Performance Martinique', 'short_name' => 'Pôle', 'color' => '#242868'],
        '9722549' => ['name' => 'FEP Monésie Volley-Ball', 'short_name' => 'FEP Monésie', 'color' => '#00838F'],
        '9722545' => ['name' => 'Entente FEP Monésie / Éclair VB Saléen', 'short_name' => 'Entente FEP/Éclair', 'color' => '#00838F'],
        '9722552' => ['name' => 'Volley-Ball Club Pilotin', 'short_name' => 'VBC Pilotin', 'city' => 'Rivière-Pilote', 'color' => '#4527A0'],
        '9722871' => ['name' => 'Jeunesse Sportive Franciscaine', 'short_name' => 'JSF', 'city' => 'Le François', 'color' => '#C62828'],
        '9722535' => ['name' => 'Aiglon Volley-Ball Club', 'short_name' => 'Aiglon', 'color' => '#37474F'],
        '9722536' => ['name' => 'Star Ball Club', 'short_name' => 'Star Ball', 'color' => '#FBC02D'],
        '9722541' => ['name' => 'Mairie Sportive', 'short_name' => 'Mairie Sportive', 'color' => '#5D4037'],
        // Clubs de Guadeloupe rencontrés en Tournoi des champion(ne)s Antilles : hors ligue.
        '9714510' => ['name' => 'ASC Fumerolles', 'short_name' => 'Fumerolles', 'city' => 'Guadeloupe', 'color' => '#455A64', 'active' => false],
        '9718076' => ['name' => 'Arsenal Club de Petit-Bourg', 'short_name' => 'Arsenal', 'city' => 'Guadeloupe', 'color' => '#455A64', 'active' => false],
    ],

    // Salles connues → commune (affichage et liens Google Maps).
    'venues' => [
        "HALL DES ANSES D'ARLET" => "Les Anses-d'Arlet",
        'GYMNASE DE SAINTE LUCE' => 'Sainte-Luce',
        "GYMNASE DE L'UAG" => 'Schœlcher',
        'GYMNASE DE CORIDON' => 'Fort-de-France',
        'LOUIS ACHILLE' => 'Fort-de-France',
        'STADE ALFRED MARIE-JEANNE' => 'Rivière-Pilote',
        'HALL DE RIV SALEE' => 'Rivière-Salée',
        'TRINITE' => 'La Trinité',
    ],
];
