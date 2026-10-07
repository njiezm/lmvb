<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Contenu initial du site LMVB. Tous les seeders sont idempotents
 * (updateOrCreate) : ils peuvent être relancés sans créer de doublons.
 *
 * Les matchs, résultats, classements et clubs viennent de la FFVolley :
 *   php artisan lmvb:sync-ffvb --season=2025/2026
 *   php artisan lmvb:sync-ffvb
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
            ClubDirectorySeeder::class,
            TeamSeeder::class,
            BoardMemberSeeder::class,
            DocumentSeeder::class,
            NewsSeeder::class,
            GallerySeeder::class,
        ]);
    }
}
