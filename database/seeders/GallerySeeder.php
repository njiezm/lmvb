<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Gallery;
use Illuminate\Database\Seeder;

/**
 * Galerie : vidéos YouTube réelles (identifiants vérifiés). Les photos d'illustration
 * libres de droits servent uniquement de visuels de fond et ne sont pas présentées
 * comme des photos d'événements de la ligue.
 */
class GallerySeeder extends Seeder
{
    public function run(): void
    {
        $club = fn (string $number) => Club::where('ffvb_number', $number)->value('id');

        $videos = [
            ['UQkr4CXVuMQ', 'Finale des play-offs seniors masculins 2026 : MUC – RCA', 'match', 'LMVB', $club('9725554')],
            ['btv8XUvQVl4', 'Finale des play-offs seniors féminines 2026 : Rayon – Empire', 'match', 'LMVB', $club('9727217')],
            ['HpIOvcQlexE', 'Finale de la Coupe féminine 2026 : Rayon – RCA', 'match', 'LMVB', $club('9727217')],
            ['0MRevigVr3s', 'Le MUC champion de Martinique (reportage)', 'match', 'RCI Martinique', $club('9725554')],
            ['Zw7I6SjYgMI', 'Coupe Antilles 2026 : MUC – ASC Fumerolles (reportage)', 'match', 'RCI Martinique', $club('9725554')],
            ['4usXroqRTFw', 'Finale de la Coupe de Martinique : Rayon – Espoir de Sainte-Luce', 'match', 'Caraibesport', null],
            ['IDYQ0JFvKco', 'Championnat de beach-volley : 1re journée, finale', 'beach', 'LMVB', null],
            ['Q2MkWo96vns', 'Finale beach-volley jeunes, 13 juin 2026', 'beach', 'LMVB', null],
            ['9_IJs2pIUKE', 'Finale beach-volley femmes, juin 2026', 'beach', 'LMVB', null],
            ['6aXeauM9Ra8', 'Martinique Beach-volley', 'beach', 'Frantz Casimir', null],
            ['Pc4sOiNIdLQ', 'CAZOVA U19 féminin : finale Martinique – Îles Vierges américaines', 'selection', 'Trinidad and Tobago Volleyball Federation', null],
            ['v7VmBUQBMbY', 'Le volley-ball martiniquais vu par Martinique la 1ère', 'event', 'LMVB / Martinique la 1ère', null],
        ];

        foreach ($videos as [$id, $title, $category, $credit, $clubId]) {
            Gallery::updateOrCreate(['video_url' => 'https://www.youtube.com/watch?v='.$id], [
                'title' => $title,
                'type' => 'video',
                'category' => $category,
                'credit' => $credit,
                'club_id' => $clubId,
                'active' => true,
            ]);
        }

        // Anciennes entrées de démonstration sans image ni vidéo : masquées.
        Gallery::whereNull('image')->whereNull('video_url')->update(['active' => false]);
    }
}
