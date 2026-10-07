<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        // Ne remplace jamais une valeur déjà saisie dans l'admin.
        $values = [
            'president_name' => 'Maëva Antiste',
            'president_title' => 'Présidente de la Ligue Martiniquaise de Volley-Ball',
            // Photo publiée par Martinique la 1ère (03/12/2024, crédit « DR ») : usage à confirmer par la ligue.
            'president_photo' => 'images/board/maeva-antiste.jpg',
            // BROUILLON à faire relire et valider par la présidente avant mise en ligne.
            'president_message' => "Bienvenue sur le site de la Ligue Martiniquaise de Volley-Ball.\n\n"
                ."Notre ligue a traversé des années difficiles. Avec le comité directeur, nous avons fait le choix de la rigueur, de la transparence et du travail collectif pour remettre nos finances en ordre et redonner confiance aux clubs, aux licenciés et à nos partenaires.\n\n"
                ."Les résultats sont là : des championnats seniors et jeunes qui se jouent à nouveau toute la saison, des finales de play-offs disputées devant un public nombreux, nos sélections jeunes titrées dans la Caraïbe et des paires de beach-volley présentes sur la scène internationale.\n\n"
                ."Ce site est votre outil : résultats mis à jour automatiquement, classements, calendriers, actualités des clubs et des sélections. Joueuses, joueurs, bénévoles, arbitres, entraîneurs, parents et supporters : merci pour votre engagement. Le volley martiniquais, c'est vous.",
            'youtube_url' => 'https://www.youtube.com/@lmvb972liguemartiniquaised6',
            'facebook_url' => 'https://www.facebook.com/LMVB972/',
        ];

        foreach ($values as $key => $value) {
            if (Setting::get($key, '') === '' || Setting::get($key, '') === (Setting::DEFAULTS[$key] ?? null)) {
                Setting::put([$key => $value]);
            }
        }
    }
}
