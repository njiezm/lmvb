<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Game;
use Illuminate\Database\Seeder;

/**
 * Complète les fiches des clubs importés depuis la FFVolley avec l'annuaire
 * de config/lmvb.php (noms accentués, communes, réseaux) et des descriptions.
 * Ne remplace que les champs vides.
 */
class ClubDirectorySeeder extends Seeder
{
    private const DETAILS = [
        '9725554' => ['venue' => "Gymnase de l'UAG (campus de Schœlcher)", 'description' => "Section volley-ball du Martinique Université Club, installée sur le campus universitaire de Schœlcher. Champion de Martinique senior masculin 2026 après une finale de play-offs remportée 3-2 face au Racing Club Arlésien, le MUC a également remporté la Coupe de Martinique et représenté l'île en Coupe Antilles."],
        '9722869' => ['venue' => "Hall des Anses-d'Arlet", 'description' => "Club historique des Anses-d'Arlet fondé en 1947, le Racing Club Arlésien (RCA) est l'un des piliers du volley martiniquais. Finaliste des play-offs seniors masculins 2026 et finaliste de la Coupe féminine 2026."],
        '9727217' => ['venue' => "Hall des Anses-d'Arlet", 'description' => "Club de Petite Anse (Les Anses-d'Arlet), le Rayon domine le volley féminin martiniquais : champion de Martinique senior féminin pour la troisième saison consécutive en 2026 et représentant de l'île en Coupe Antilles."],
        '9722556' => ['venue' => 'Stade Alfred Marie-Jeanne', 'description' => 'Club omnisports de Rivière-Pilote, le Racing Club engage des équipes seniors et jeunes en championnat régional et s\'est qualifié pour les play-offs seniors masculins 2026.'],
        '9727670' => ['venue' => 'Stade Louis-Achille (Fort-de-France)', 'description' => 'Club foyalais engagé en championnat senior masculin avec plusieurs équipes.'],
        '9722828' => ['venue' => 'Gymnase de Sainte-Luce', 'description' => 'Club omnisports de Sainte-Luce, l\'Espoir engage des équipes seniors et jeunes en championnat régional.'],
        '9722553' => ['venue' => 'Gymnase Granvorka (Schœlcher)', 'description' => 'Club de volley-ball de Schœlcher engagé en championnat régional.'],
        '9722544' => ['venue' => 'Palais des sports de La Trinité', 'description' => 'Club de La Trinité, l\'Empire Volley-Ball Club a disputé la finale des play-offs seniors féminins 2026.'],
        '9722543' => ['description' => 'Club de Sainte-Marie (Morne-des-Esses).'],
    ];

    public function run(): void
    {
        foreach (config('lmvb.clubs') as $number => $info) {
            $club = Club::withTrashed()->where('ffvb_number', $number)->first();
            if (! $club) {
                continue;
            }

            $fill = array_merge($info, self::DETAILS[$number] ?? []);
            // Nom propre : remplace le libellé importé automatiquement si la fiche n'a jamais été modifiée à la main.
            if ($club->created_at?->eq($club->updated_at)) {
                $club->name = $fill['name'];
            }
            foreach (['short_name', 'city', 'venue', 'description', 'facebook', 'instagram', 'founded_year', 'color'] as $field) {
                if (isset($fill[$field]) && (blank($club->{$field}) || ($field === 'color' && $club->color === '#242868'))) {
                    $club->{$field} = $fill[$field];
                }
            }
            if (array_key_exists('active', $fill)) {
                $club->active = $fill['active'];
            }
            $club->save();
        }

        if (is_file(public_path('images/clubs/racing-club-arlesien.jpg'))) {
            Club::where('ffvb_number', '9722869')->whereNull('logo')->update(['logo' => 'images/clubs/racing-club-arlesien.jpg']);
        }

        // Logos fournis dans public/images/clubs/<numéro FFVolley>.png : appliqués si le club n'a pas encore de logo.
        foreach (glob(public_path('images/clubs/*.png')) as $file) {
            $number = pathinfo($file, PATHINFO_FILENAME);
            if (ctype_digit($number)) {
                Club::withTrashed()->where('ffvb_number', $number)->whereNull('logo')->update(['logo' => 'images/clubs/'.basename($file)]);
            }
        }

        $this->archiveDemoData();
    }

    /** Clubs et matchs fictifs de l'ancien jeu de test : archivés (réversible), jamais supprimés. */
    private function archiveDemoData(): void
    {
        $demo = Club::whereNull('ffvb_number')->whereIn('slug', ['espoir-ste-luce', 'rayon-de-lanse', 'good-luck'])->get();
        if ($demo->isEmpty()) {
            return;
        }

        Game::whereNull('competition_id')->whereNull('ffvb_code')
            ->where(fn ($q) => $q->whereIn('home_team_id', $demo->pluck('id'))->orWhereIn('away_team_id', $demo->pluck('id')))
            ->update(['status' => 'cancelled', 'locked' => true, 'notes' => 'Donnée de démonstration (ancien jeu de test) : peut être supprimée.']);

        $demo->each->delete(); // suppression douce (restaurable)
        $this->command?->info($demo->count().' club(s) de démonstration archivé(s).');
    }
}
