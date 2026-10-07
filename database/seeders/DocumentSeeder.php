<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $docs = [
            ['title' => 'Calendriers, résultats et classements officiels (FFVolley)', 'category' => 'calendrier',
                'url' => 'https://www.ffvbbeach.org/ffvbapp/resu/vbspo_home.php?codent=LIMART',
                'description' => 'Plateforme officielle de gestion sportive de la FFVolley pour la ligue (code LIMART).'],
            ['title' => 'Liste des équipes engagées', 'category' => 'calendrier',
                'url' => 'https://www.ffvbbeach.org/ffvbapp/adressier/engag_division.php?codent=LIMART',
                'description' => 'Engagements des clubs martiniquais par division.'],
            ['title' => 'Règlements fédéraux et licences (FFVolley)', 'category' => 'reglement',
                'url' => 'https://www.ffvolley.org/',
                'description' => 'Règlements généraux, sportifs et d\'arbitrage de la Fédération Française de Volley.'],
        ];

        foreach ($docs as $doc) {
            Document::updateOrCreate(['title' => $doc['title']], $doc + ['active' => true]);
        }
    }
}
