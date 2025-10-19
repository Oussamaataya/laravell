<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\User;

class TestChatCommand extends Command
{
    protected $signature = 'chat:test';
    protected $description = 'Test the chat system';

    public function handle()
    {
        $this->info('🧪 Test du système de chat...');
        
        try {
            // Test 1: Vérifier les rooms
            $rooms = ChatRoom::all();
            $this->info("✅ Rooms trouvées: " . $rooms->count());
            
            if ($rooms->count() > 0) {
                $room = $rooms->first();
                $this->info("   - Room: {$room->name} (ID: {$room->id})");
                
                // Test 2: Vérifier les messages
                $messages = $room->messages()->get();
                $this->info("✅ Messages dans cette room: " . $messages->count());
                
                // Test 3: Créer un message de test
                $user = User::first();
                if ($user) {
                    $testMessage = ChatMessage::create([
                        'chat_room_id' => $room->id,
                        'user_id' => $user->id,
                        'message' => 'Message de test - ' . now(),
                        'type' => 'text'
                    ]);
                    
                    $this->info("✅ Message de test créé: ID {$testMessage->id}");
                    
                    // Supprimer le message de test
                    $testMessage->delete();
                    $this->info("✅ Message de test supprimé");
                }
            }
            
            $this->info('🎉 Test terminé avec succès !');
            
        } catch (\Exception $e) {
            $this->error('❌ Erreur: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
