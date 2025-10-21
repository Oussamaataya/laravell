<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use App\Models\User;
use App\Services\OpenRouterService;

class ChatController extends Controller
{
    public function __construct1()
    {
        // Le middleware est déjà appliqué dans les routes
    }

    protected $openRouterService;

    public function __construct(OpenRouterService $openRouterService)
    {
        $this->openRouterService = $openRouterService;
    }

    public function handleRequest(Request $request)
    {
        $message = $request->input('message');
        $response = $this->openRouterService->getResponse($message);
        return response()->json(['response' => $response]);
    }

    // === GESTION DES ROOMS ===

    /**
     * Liste des rooms de chat
     */
    public function index()
    {
        $user = Auth::user();
        
        // Rooms publiques
        $publicRooms = ChatRoom::active()
            ->public()
            ->with(['creator', 'latestMessage.user'])
            ->withCount('activeParticipants')
            ->orderBy('last_activity', 'desc')
            ->take(20)
            ->get();

        // Rooms où l'utilisateur participe
        $userRooms = $user->chatRooms()
            ->with(['creator', 'latestMessage.user'])
            ->withCount('activeParticipants')
            ->orderBy('last_activity', 'desc')
            ->get();

        return view('chat.index', compact('publicRooms', 'userRooms'));
    }

    /**
     * Créer une nouvelle room
     */
    public function createRoom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:public,private',
            'max_participants' => 'integer|min:2|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room = ChatRoom::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'created_by' => Auth::id(),
            'max_participants' => $request->max_participants ?? 50,
            'last_activity' => now()
        ]);

        // Ajouter le créateur comme admin
        $room->addParticipant(Auth::user(), 'admin');

        return response()->json([
            'success' => true,
            'room' => $room->load('creator'),
            'message' => 'Room créée avec succès!'
        ]);
    }

    /**
     * Rejoindre une room
     */
    public function joinRoom(Request $request, $roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        $user = Auth::user();

        if (!$room->canJoin($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible de rejoindre cette room.'
            ], 403);
        }

        // Vérifier si déjà participant
        if ($room->isParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous participez déjà à cette room.'
            ], 400);
        }

        $room->addParticipant($user);
        $room->updateLastActivity();

        // Message système
        ChatMessage::create([
            'chat_room_id' => $room->id,
            'user_id' => $user->id,
            'message' => "{$user->name} a rejoint la room",
            'type' => 'system'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vous avez rejoint la room avec succès!'
        ]);
    }

    /**
     * Quitter une room
     */
    public function leaveRoom($roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        $user = Auth::user();

        if (!$room->isParticipant($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Vous ne participez pas à cette room.'
            ], 400);
        }

        $room->removeParticipant($user);

        // Message système
        ChatMessage::create([
            'chat_room_id' => $room->id,
            'user_id' => $user->id,
            'message' => "{$user->name} a quitté la room",
            'type' => 'system'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Vous avez quitté la room.'
        ]);
    }

    /**
     * Rejoindre par code
     */
    public function joinByCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_code' => 'required|string|size:8'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room = ChatRoom::where('room_code', strtoupper($request->room_code))
                       ->active()
                       ->first();

        if (!$room) {
            return response()->json([
                'success' => false,
                'message' => 'Code de room invalide.'
            ], 404);
        }

        return $this->joinRoom($request, $room->id);
    }

    // === GESTION DES MESSAGES ===

    /**
     * Afficher une room de chat
     */
    public function showRoom($roomId)
    {
        $room = ChatRoom::with(['creator', 'participants.user'])
                       ->findOrFail($roomId);
        
        $user = Auth::user();

        if (!$room->isParticipant($user)) {
            return redirect()->route('chat.index')
                           ->with('error', 'Vous devez rejoindre cette room pour y accéder.');
        }

        // Marquer comme vu
        $participant = $room->participants()->where('user_id', $user->id)->first();
        $participant->updateLastSeen();

        // Charger les messages récents
        $messages = $room->messages()
                        ->with(['user', 'replyTo.user'])
                        ->notDeleted()
                        ->orderBy('created_at', 'desc')
                        ->take(50)
                        ->get()
                        ->reverse();

        return view('chat.room', compact('room', 'messages'));
    }

    /**
     * Récupérer les messages d'une room (AJAX)
     */
    public function getMessages($roomId, Request $request)
    {
        $room = ChatRoom::findOrFail($roomId);
        $user = Auth::user();

        if (!$room->isParticipant($user)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $lastMessageId = $request->get('last_message_id', 0);
        
        $messages = $room->messages()
                        ->with(['user', 'replyTo.user'])
                        ->notDeleted()
                        ->where('id', '>', $lastMessageId)
                        ->orderBy('created_at', 'asc')
                        ->get();

        return response()->json([
            'messages' => $messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name
                    ],
                    'message' => $message->message,
                    'type' => $message->type,
                    'file_url' => $message->getFileUrl(),
                    'file_name' => $message->file_name,
                    'file_size' => $message->getFileSizeFormatted(),
                    'reply_to' => $message->replyTo ? [
                        'id' => $message->replyTo->id,
                        'user_name' => $message->replyTo->user->name,
                        'message' => $message->replyTo->message
                    ] : null,
                    'is_edited' => $message->is_edited,
                    'created_at' => $message->created_at->format('H:i'),
                    'created_at_full' => $message->created_at->format('d/m/Y H:i:s')
                ];
            })
        ]);
    }

    /**
     * Envoyer un message
     */
    public function sendMessage(Request $request, $roomId)
    {
        $room = ChatRoom::findOrFail($roomId);
        $user = Auth::user();

        if (!$room->isParticipant($user)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        $participant = $room->participants()->where('user_id', $user->id)->first();
        if (!$participant->canSendMessages()) {
            return response()->json(['error' => 'Vous êtes muet dans cette room'], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required_without:file|string|max:2000',
            'file' => 'nullable|file|max:10240', // 10MB max
            'reply_to' => 'nullable|exists:chat_messages,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $messageData = [
            'chat_room_id' => $room->id,
            'user_id' => $user->id,
            'type' => 'text'
        ];

        // Gestion des fichiers
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('chat-files', 'public');
            
            $messageData['file_path'] = $path;
            $messageData['file_name'] = $file->getClientOriginalName();
            $messageData['file_size'] = $file->getSize();
            $messageData['type'] = $this->getFileType($file);
            $messageData['message'] = $request->message ?: 'Fichier partagé';
        } else {
            $messageData['message'] = $request->message;
        }

        if ($request->reply_to) {
            $messageData['reply_to'] = $request->reply_to;
        }

        $message = ChatMessage::create($messageData);
        $room->updateLastActivity();

        return response()->json([
            'success' => true,
            'message' => $message->load(['user', 'replyTo.user'])
        ]);
    }

    /**
     * Éditer un message
     */
    public function editMessage(Request $request, $messageId)
    {
        $message = ChatMessage::findOrFail($messageId);
        $user = Auth::user();

        if (!$message->canEdit($user)) {
            return response()->json(['error' => 'Impossible d\'éditer ce message'], 403);
        }

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message->edit($request->message);

        return response()->json([
            'success' => true,
            'message' => 'Message modifié avec succès'
        ]);
    }

    /**
     * Supprimer un message
     */
    public function deleteMessage($messageId)
    {
        $message = ChatMessage::findOrFail($messageId);
        $user = Auth::user();

        if (!$message->canDelete($user)) {
            return response()->json(['error' => 'Impossible de supprimer ce message'], 403);
        }

        $message->softDelete();

        return response()->json([
            'success' => true,
            'message' => 'Message supprimé avec succès'
        ]);
    }

    // === GESTION DES PARTICIPANTS ===

    /**
     * Gérer les participants (mute, ban, etc.)
     */
    public function manageParticipant(Request $request, $roomId, $userId)
    {
        $room = ChatRoom::findOrFail($roomId);
        $user = Auth::user();
        $targetUser = User::findOrFail($userId);

        // Vérifier les permissions
        $userParticipant = $room->participants()->where('user_id', $user->id)->first();
        if (!$userParticipant || !$userParticipant->canModerate()) {
            return response()->json(['error' => 'Permissions insuffisantes'], 403);
        }

        $targetParticipant = $room->participants()->where('user_id', $userId)->first();
        if (!$targetParticipant) {
            return response()->json(['error' => 'Utilisateur non trouvé dans cette room'], 404);
        }

        $action = $request->input('action');

        switch ($action) {
            case 'mute':
                $targetParticipant->mute();
                $message = "{$targetUser->name} a été muté";
                break;
            case 'unmute':
                $targetParticipant->unmute();
                $message = "{$targetUser->name} n'est plus muté";
                break;
            case 'ban':
                $targetParticipant->ban();
                $message = "{$targetUser->name} a été banni";
                break;
            case 'unban':
                $targetParticipant->unban();
                $message = "{$targetUser->name} n'est plus banni";
                break;
            default:
                return response()->json(['error' => 'Action invalide'], 400);
        }

        // Message système
        ChatMessage::create([
            'chat_room_id' => $room->id,
            'user_id' => $user->id,
            'message' => $message,
            'type' => 'system'
        ]);

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    // === MÉTHODES UTILITAIRES ===

    private function getFileType($file): string
    {
        $mimeType = $file->getMimeType();
        
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }
        
        return 'file';
    }

    /**
     * Rechercher des rooms
     */
    public function searchRooms(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json(['rooms' => []]);
        }

        $rooms = ChatRoom::active()
                        ->public()
                        ->where('name', 'LIKE', "%{$query}%")
                        ->with(['creator'])
                        ->withCount('activeParticipants')
                        ->take(10)
                        ->get();

        return response()->json(['rooms' => $rooms]);
    }

    /**
     * Obtenir les statistiques d'une room
     */
    public function getRoomStats($roomId)
    {
        $room = ChatRoom::withCount(['participants', 'messages'])
                       ->findOrFail($roomId);
        
        $user = Auth::user();
        if (!$room->isParticipant($user)) {
            return response()->json(['error' => 'Accès refusé'], 403);
        }

        return response()->json([
            'participants_count' => $room->participants_count,
            'messages_count' => $room->messages_count,
            'created_at' => $room->created_at->format('d/m/Y'),
            'last_activity' => $room->last_activity?->diffForHumans()
        ]);
    }
}
