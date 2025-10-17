<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer un utilisateur admin par défaut
        User::updateOrCreate(
            ['email' => 'admin@ecoevent.com'],
            [
                'name' => 'Administrateur EcoEvent',
                'email' => 'admin@ecoevent.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Créer un utilisateur normal pour les tests
        User::updateOrCreate(
            ['email' => 'user@ecoevent.com'],
            [
                'name' => 'Utilisateur Test',
                'email' => 'user@ecoevent.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Utilisateurs de test créés avec succès !');
        $this->command->info('Admin: admin@ecoevent.com / password123');
        $this->command->info('User: user@ecoevent.com / password123');
    }
}
