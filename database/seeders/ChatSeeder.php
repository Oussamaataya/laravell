<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\User;

class ChatSeeder extends Seeder
{
    public function run()
    {
        // Récupérer le premier utilisateur
        $user = User::first();
        
        if (!$user) {
            $this->command->info('Aucun utilisateur trouvé. Créez d\'abord un utilisateur.');
            return;
        }

        // Créer une room de test
        $room = ChatRoom::create([
            'name' => 'Salon Général',
            'description' => 'Bienvenue dans le salon général pour discuter de tout et de rien !',
            'type' => 'public',
            'created_by' => $user->id,
            'max_participants' => 50
        ]);

        // Ajouter l'utilisateur comme admin de la room
        $room->addParticipant($user, 'admin');

        // Créer un message de bienvenue
        ChatMessage::create([
            'chat_room_id' => $room->id,
            'user_id' => $user->id,
            'message' => 'Bienvenue dans le salon général ! 🎉',
            'type' => 'text'
        ]);

        // Créer une room privée
        $privateRoom = ChatRoom::create([
            'name' => 'Équipe Admin',
            'description' => 'Room privée pour les administrateurs',
            'type' => 'private',
            'created_by' => $user->id,
            'max_participants' => 10
        ]);

        $privateRoom->addParticipant($user, 'admin');

        $this->command->info('Données de chat créées avec succès !');
        $this->command->info("Room publique: {$room->name} (Code: {$room->room_code})");
        $this->command->info("Room privée: {$privateRoom->name} (Code: {$privateRoom->room_code})");
    }
}
