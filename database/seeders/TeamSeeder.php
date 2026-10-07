<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

/** Sélections de Martinique. Les effectifs sont à compléter dans l'admin après chaque convocation. */
class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            'senior_m' => ['name' => 'Sélection de Martinique Seniors Masculins', 'photo' => null,
                'description' => "La sélection senior masculine représente la Martinique dans les compétitions caribéennes et lors des rencontres face à la Guadeloupe et à la Guyane."],
            'senior_f' => ['name' => 'Sélection de Martinique Seniors Féminines', 'photo' => null,
                'description' => "La sélection senior féminine réunit les meilleures joueuses des clubs martiniquais pour les compétitions régionales et caribéennes."],
            'u18_f' => ['name' => 'Sélection de Martinique Jeunes Féminines (U19 / U23)', 'coach' => 'Eddy Erialc',
                'description' => "Championnes de la Caraïbe ! En juillet 2025 à Maloney (Trinité-et-Tobago), les U23 martiniquaises ont remporté le championnat CAZOVA en battant le Suriname, tenant du titre, 3 sets à 1 en finale (23-25, 25-15, 25-21, 25-19), après un parcours sans défaite. La capitaine Maelyss Melinard-Chanteur a été élue meilleure joueuse et meilleure attaquante du tournoi."],
            'u18_m' => ['name' => 'Sélection de Martinique Jeunes Masculins (U19 / U23)',
                'description' => "Médaille de bronze au championnat CAZOVA U23 2025 à Trinité-et-Tobago, décrochée 3-1 face à la Guadeloupe (18-25, 25-22, 25-19, 25-21) dans le match pour la troisième place."],
            'u16_m' => ['name' => 'Sélection de Martinique U16 Masculins', 'description' => 'Sélection régionale des moins de 16 ans, vivier des futures équipes de Martinique.'],
            'u16_f' => ['name' => 'Sélection de Martinique U16 Féminines', 'description' => 'Sélection régionale des moins de 16 ans, vivier des futures équipes de Martinique.'],
        ];

        foreach ($teams as $category => $data) {
            $team = Team::firstOrNew(['category' => $category]);
            $team->fill(array_filter($data, fn ($v) => $v !== null) + ['active' => true]);
            $team->slug = \Illuminate\Support\Str::slug($data['name']);
            $team->save();
        }
    }
}
