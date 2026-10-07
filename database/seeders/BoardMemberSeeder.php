<?php

namespace Database\Seeders;

use App\Models\BoardMember;
use Illuminate\Database\Seeder;

/** Comité directeur : seule la présidence est publique à ce jour, compléter dans l'admin. */
class BoardMemberSeeder extends Seeder
{
    public function run(): void
    {
        BoardMember::updateOrCreate(['role' => 'Présidente'], [
            'name' => 'Maëva Antiste',
            'photo' => is_file(public_path('images/board/maeva-antiste.jpg')) ? 'images/board/maeva-antiste.jpg' : null,
            'bio' => 'Élue à la tête de la ligue en 2024 et réélue pour quatre ans lors de l\'assemblée générale du 2 décembre 2024. Également arbitre de volley-ball.',
            'sort_order' => 1,
            'active' => true,
        ]);
    }
}
