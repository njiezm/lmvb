<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Crée un super administrateur s'il n'en existe aucun.
     * Le mot de passe est généré aléatoirement et affiché une seule fois.
     */
    public function run(): void
    {
        if (User::where('role', 'super_admin')->exists()) {
            $this->command?->info('Super administrateur déjà présent : aucun compte créé.');

            return;
        }

        $password = Str::password(16, symbols: false);
        User::create([
            'name' => 'Administrateur LMVB',
            'email' => 'admin@lmvb972.fr',
            'password' => $password,
            'role' => 'super_admin',
            'active' => true,
        ]);

        $this->command?->warn("Super admin créé : admin@lmvb972.fr / {$password}  (à changer dès la première connexion)");
    }
}
