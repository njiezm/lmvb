<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['slug' => 'match', 'name' => 'Championnats', 'color' => '#3F63AF', 'description' => 'Résultats et temps forts des championnats régionaux.'],
            ['slug' => 'beach', 'name' => 'Beach-volley', 'color' => '#D97706', 'description' => 'Tournois et championnat de beach-volley.'],
            ['slug' => 'equipe', 'name' => 'Sélections', 'color' => '#9A1A26', 'description' => 'Les sélections de Martinique.'],
            ['slug' => 'evenement', 'name' => 'Vie de la ligue', 'color' => '#242868', 'description' => 'Assemblées, gouvernance, partenaires et annonces.'],
            ['slug' => 'jeunes', 'name' => 'Jeunes', 'color' => '#0D9488', 'description' => 'Championnats et événements jeunes (M13 à M21).'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
