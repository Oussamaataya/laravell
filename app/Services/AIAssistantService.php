<?php

namespace App\Services;

use OpenAI\Client;

use App\Models\ChatMessage;
use Illuminate\Support\Facades\Log;

class AIAssistantService
{
    protected $client;
    protected $model;
    protected $maxTokens;
    protected $temperature;

    public function __construct()
    {
        // Initialisation directe sans try-catch pour permettre la propagation des erreurs réelles
        $apiKey = config('openai.api_key');
        if (empty($apiKey)) {
            Log::error('OpenAI API Key is missing');
            return;
        }
        
        try {
            $this->client = \OpenAI::client($apiKey);
            $this->model = config('openai.model', 'gpt-3.5-turbo');
            $this->maxTokens = config('openai.max_tokens', 150);
            $this->temperature = config('openai.temperature', 0.7);
            
            Log::info('OpenAI client initialized successfully');
        } catch (\Exception $e) {
            Log::error('OpenAI Client Error: ' . $e->getMessage());
        }
    }

    public function sendMessage($message, $userId)
    {
        // Store user message first (toujours enregistrer le message de l'utilisateur)
        $userMessage = ChatMessage::create([
            'user_id' => $userId,
            'role' => 'user',
            'content' => $message
        ]);

        // Vérifier si le client OpenAI est initialisé
        if (!$this->client) {
            Log::error('OpenAI client not initialized - API key may be missing or invalid');
            
            return ChatMessage::create([
                'user_id' => $userId,
                'role' => 'assistant',
                'content' => 'Le service OpenAI n\'est pas disponible. Veuillez vérifier votre clé API dans le fichier .env.',
                'metadata' => [
                    'error' => true,
                    'message' => 'OpenAI client not initialized'
                ]
            ]);
        }

        try {
            // Get recent chat history
            $recentMessages = ChatMessage::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->reverse()
                ->map(function ($msg) {
                    return ['role' => $msg->role, 'content' => $msg->content];
                })
                ->toArray();

            // Send to OpenAI
            $response = $this->client->chat()->create([
                'model' => $this->model,
                'messages' => array_merge($recentMessages, [
                    ['role' => 'user', 'content' => $message]
                ]),
                'max_tokens' => $this->maxTokens,
                'temperature' => $this->temperature,
            ]);

            // Store AI response
            $assistantMessage = ChatMessage::create([
                'user_id' => $userId,
                'role' => 'assistant',
                'content' => $response->choices[0]->message->content,
                'metadata' => [
                    'finish_reason' => $response->choices[0]->finishReason,
                    'usage' => $response->usage->toArray()
                ]
            ]);

            return $assistantMessage;
        } catch (\Exception $e) {
            Log::error('AI Assistant Error: ' . $e->getMessage());
            
            // Message d'erreur plus spécifique pour aider l'utilisateur
            $errorContent = 'Désolé, une erreur est survenue lors de la communication avec OpenAI. ';
            
            // Ajouter des détails spécifiques selon le type d'erreur
            if (strpos($e->getMessage(), 'authentication') !== false || strpos($e->getMessage(), 'key') !== false) {
                $errorContent .= 'Problème d\'authentification avec l\'API OpenAI. Veuillez vérifier votre clé API.';
            } elseif (strpos($e->getMessage(), 'timeout') !== false || strpos($e->getMessage(), 'connect') !== false) {
                $errorContent .= 'Problème de connexion au serveur OpenAI. Veuillez vérifier votre connexion internet.';
            } elseif (strpos($e->getMessage(), 'rate limit') !== false) {
                $errorContent .= 'Limite de requêtes atteinte. Veuillez réessayer dans quelques instants.';
            } else {
                $errorContent .= 'Veuillez réessayer ultérieurement ou contacter l\'administrateur.';
            }
            
            $errorMessage = ChatMessage::create([
                'user_id' => $userId,
                'role' => 'assistant',
                'content' => $errorContent,
                'metadata' => [
                    'error' => true,
                    'message' => $e->getMessage()
                ]
            ]);
            
            return $errorMessage;
        }
    }

    public function getChatHistory($userId, $limit = 50)
    {
        return ChatMessage::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get()
            ->reverse();
    }
}