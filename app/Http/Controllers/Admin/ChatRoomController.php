<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\User;
use App\Models\ChatMessage;
use App\Models\ChatParticipant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatRoomController extends Controller
{
    /**
     * Afficher la liste des chat rooms
     */
    public function index(Request $request)
    {
        $query = ChatRoom::with(['creator', 'participants'])
            ->withCount(['participants', 'messages']);

        // Filtres
        if ($request->filled('q')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('creator')) {
            $query->where('created_by', $request->creator);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Tri
        $sort = $request->get('sort', 'created_at_desc');
        switch ($sort) {
            case 'created_at_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'participants_desc':
                $query->orderBy('participants_count', 'desc');
                break;
            case 'messages_desc':
                $query->orderBy('messages_count', 'desc');
                break;
            case 'activity_desc':
                $query->orderBy('last_activity', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $chatRooms = $query->paginate(15)->appends($request->query());

        // Données pour les filtres
        $creators = User::whereIn('id', ChatRoom::distinct('created_by')->pluck('created_by'))
            ->pluck('name', 'id');

        return view('admin.chat-rooms.index', compact('chatRooms', 'creators'));
    }

    /**
     * Afficher les détails d'une chat room
     */
    public function show(ChatRoom $chatRoom)
    {
        $chatRoom->load([
            'creator',
            'participants.user',
            'messages' => function($query) {
                $query->with('user')->latest()->take(50);
            }
        ]);

        $stats = [
            'total_messages' => $chatRoom->messages()->count(),
            'active_participants' => $chatRoom->participants()->where('is_banned', false)->count(),
            'banned_participants' => $chatRoom->participants()->where('is_banned', true)->count(),
            'messages_today' => $chatRoom->messages()->whereDate('created_at', today())->count(),
            'messages_week' => $chatRoom->messages()->where('created_at', '>=', now()->subWeek())->count(),
        ];

        return view('admin.chat-rooms.show', compact('chatRoom', 'stats'));
    }

    /**
     * Créer une nouvelle chat room
     */
    public function create()
    {
        $users = User::orderBy('name')->pluck('name', 'id');
        return view('admin.chat-rooms.create', compact('users'));
    }

    /**
     * Enregistrer une nouvelle chat room
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:public,private',
            'created_by' => 'required|exists:users,id',
            'max_participants' => 'nullable|integer|min:2|max:1000',
            'is_active' => 'boolean'
        ]);

        $chatRoom = ChatRoom::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'created_by' => $request->created_by,
            'max_participants' => $request->max_participants,
            'is_active' => $request->boolean('is_active', true),
            'created_at' => now(),
            'last_activity' => now()
        ]);

        // Ajouter le créateur comme participant admin
        ChatParticipant::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => $request->created_by,
            'role' => 'admin',
            'joined_at' => now()
        ]);

        return redirect()->route('admin.chat-rooms.index')
            ->with('success', 'Chat room créée avec succès !');
    }

    /**
     * Éditer une chat room
     */
    public function edit(ChatRoom $chatRoom)
    {
        $users = User::orderBy('name')->pluck('name', 'id');
        return view('admin.chat-rooms.edit', compact('chatRoom', 'users'));
    }

    /**
     * Mettre à jour une chat room
     */
    public function update(Request $request, ChatRoom $chatRoom)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:public,private',
            'max_participants' => 'nullable|integer|min:2|max:1000',
            'is_active' => 'boolean'
        ]);

        $chatRoom->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'max_participants' => $request->max_participants,
            'is_active' => $request->boolean('is_active')
        ]);

        return redirect()->route('admin.chat-rooms.index')
            ->with('success', 'Chat room mise à jour avec succès !');
    }

    /**
     * Supprimer une chat room
     */
    public function destroy(ChatRoom $chatRoom)
    {
        $chatRoom->delete();

        return redirect()->route('admin.chat-rooms.index')
            ->with('success', 'Chat room supprimée avec succès !');
    }

    /**
     * Actions en masse
     */
    public function bulk(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'exists:chat_rooms,id'
        ]);

        $chatRooms = ChatRoom::whereIn('id', $request->ids);

        switch ($request->action) {
            case 'activate':
                $chatRooms->update(['is_active' => true]);
                $message = 'Chat rooms activées avec succès !';
                break;
            case 'deactivate':
                $chatRooms->update(['is_active' => false]);
                $message = 'Chat rooms désactivées avec succès !';
                break;
            case 'delete':
                $chatRooms->delete();
                $message = 'Chat rooms supprimées avec succès !';
                break;
        }

        return redirect()->route('admin.chat-rooms.index')
            ->with('success', $message);
    }

    /**
     * Gérer les participants d'une room
     */
    public function participants(ChatRoom $chatRoom)
    {
        $participants = $chatRoom->participants()
            ->with('user')
            ->orderBy('joined_at', 'desc')
            ->paginate(20);

        $availableUsers = User::whereNotIn('id', $chatRoom->participants->pluck('user_id'))
            ->orderBy('name')
            ->get();

        return view('admin.chat-rooms.participants', compact('chatRoom', 'participants', 'availableUsers'));
    }

    /**
     * Ajouter un participant
     */
    public function addParticipant(Request $request, ChatRoom $chatRoom)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:member,moderator,admin'
        ]);

        // Vérifier si l'utilisateur n'est pas déjà participant
        if ($chatRoom->participants()->where('user_id', $request->user_id)->exists()) {
            return back()->with('error', 'Cet utilisateur est déjà participant de cette room.');
        }

        ChatParticipant::create([
            'chat_room_id' => $chatRoom->id,
            'user_id' => $request->user_id,
            'role' => $request->role,
            'joined_at' => now()
        ]);

        return back()->with('success', 'Participant ajouté avec succès !');
    }

    /**
     * Supprimer un participant
     */
    public function removeParticipant(ChatRoom $chatRoom, User $user)
    {
        $participant = $chatRoom->participants()->where('user_id', $user->id)->first();
        
        if ($participant) {
            $participant->delete();
            return back()->with('success', 'Participant supprimé avec succès !');
        }

        return back()->with('error', 'Participant non trouvé.');
    }

    /**
     * Bannir/débannir un participant
     */
    public function toggleBan(ChatRoom $chatRoom, User $user)
    {
        $participant = $chatRoom->participants()->where('user_id', $user->id)->first();
        
        if ($participant) {
            $participant->update(['is_banned' => !$participant->is_banned]);
            $action = $participant->is_banned ? 'banni' : 'débanni';
            return back()->with('success', "Participant {$action} avec succès !");
        }

        return back()->with('error', 'Participant non trouvé.');
    }

    /**
     * Régénérer le code d'invitation
     */
    public function regenerateInviteCode(ChatRoom $chatRoom)
    {
        $chatRoom->update(['room_code' => strtoupper(Str::random(8))]);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'room_code' => $chatRoom->room_code,
            ]);
        }

        return back()->with('success', 'Code d\'invitation régénéré avec succès !');
    }

    /**
     * Basculer le statut actif/inactif
     */
    public function toggleStatus(ChatRoom $chatRoom)
    {
        $chatRoom->update(['is_active' => !$chatRoom->is_active]);
        
        $status = $chatRoom->is_active ? 'activée' : 'désactivée';
        return back()->with('success', "Chat room {$status} avec succès !");
    }

    /**
     * Exporter les messages d'une room
     */
    public function exportMessages(ChatRoom $chatRoom)
    {
        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'messages_' . Str::slug($chatRoom->name) . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($messages) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, ['ID', 'Auteur', 'Email', 'Message', 'Type', 'Date', 'Modifié']);
            
            foreach ($messages as $message) {
                fputcsv($file, [
                    $message->id,
                    $message->user->name ?? 'Utilisateur supprimé',
                    $message->user->email ?? '',
                    $message->message,
                    $message->type,
                    $message->created_at->format('d/m/Y H:i:s'),
                    $message->is_edited ? 'Oui' : 'Non'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Exporter les participants d'une room
     */
    public function exportParticipants(ChatRoom $chatRoom)
    {
        $participants = $chatRoom->participants()
            ->with('user')
            ->orderBy('joined_at', 'desc')
            ->get();

        $filename = 'participants_' . Str::slug($chatRoom->name) . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($participants) {
            $file = fopen('php://output', 'w');
            
            // En-têtes CSV
            fputcsv($file, ['ID', 'Nom', 'Email', 'Rôle', 'Date d\'adhésion', 'Dernière vue', 'Statut', 'Banni', 'Muet']);
            
            foreach ($participants as $participant) {
                fputcsv($file, [
                    $participant->user->id ?? '',
                    $participant->user->name ?? 'Utilisateur supprimé',
                    $participant->user->email ?? '',
                    $participant->role,
                    $participant->joined_at->format('d/m/Y H:i:s'),
                    $participant->last_seen ? $participant->last_seen->format('d/m/Y H:i:s') : 'Jamais',
                    $participant->is_banned ? 'Banni' : 'Actif',
                    $participant->is_banned ? 'Oui' : 'Non',
                    $participant->is_muted ? 'Oui' : 'Non'
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
