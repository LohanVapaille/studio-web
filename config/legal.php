<?php

/*
|--------------------------------------------------------------------------
| Informations légales
|--------------------------------------------------------------------------
| Utilisées par la page « Mentions légales », le pied de page et les
| données structurées (SEO). Pour changer d'hébergeur : LEGAL_HOST=ovh
| dans le .env (ou ajoutez un nouvel hébergeur dans « hosts »).
*/

return [

    'company' => [
        'name' => 'Lohan Vapaille',
        'trade_name' => 'Lovaweb',
        'legal_form' => 'Entrepreneur individuel (EI)',
        'siren' => '132 744 673',
        'siret' => env('LEGAL_SIRET'), // ex : 132 744 673 00012 (voir « Établissement principal » sur le guichet unique)
        'ape' => '62.01Z — Programmation informatique',
        'registration' => 'Immatriculé au Registre national des entreprises (RNE) le 12/11/2026',
        'vat' => 'TVA non applicable, art. 293 B du CGI',
        'street' => '32 rue de Champ la Dame',
        'postal_code' => '77169',
        'city' => 'Chauffry',
        'region' => 'Seine-et-Marne',
        'country' => 'France',
        'email' => env('LEGAL_EMAIL', 'contact@lohanvapaille.fr'),
        'phone' => env('LEGAL_PHONE'),
        'publisher' => 'Lohan Vapaille',
    ],

    'domain_registrar' => [
        'name' => 'OVH SAS',
        'address' => '2 rue Kellermann, 59100 Roubaix, France',
        'website' => 'https://www.ovhcloud.com',
    ],

    'host' => env('LEGAL_HOST', 'o2switch'),

    'hosts' => [
        'o2switch' => [
            'name' => 'O2SWITCH SARL',
            'address' => '222-224 boulevard Gustave Flaubert, 63000 Clermont-Ferrand, France',
            'phone' => '04 44 44 60 40',
            'website' => 'https://www.o2switch.fr',
        ],
        'ovh' => [
            'name' => 'OVH SAS',
            'address' => '2 rue Kellermann, 59100 Roubaix, France',
            'phone' => '1007',
            'website' => 'https://www.ovhcloud.com',
        ],
    ],

];
