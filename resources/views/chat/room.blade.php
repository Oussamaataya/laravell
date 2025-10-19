@extends('layouts.app')

@section('title', $room->name . ' - Chat')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        {{-- Sidebar participants --}}
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ $room->name }}</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                            <i class="fas fa-cog"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" id="roomInfoBtn"><i class="fas fa-info"></i> Infos</a></li>
                            <li><a class="dropdown-item" href="#" id="leaveRoomBtn"><i class="fas fa-sign-out-alt"></i> Quitter</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom">
                        <small class="text-muted">Code: <strong>{{ $room->room_code }}</strong></small><br>
                        <small class="text-muted">{{ $room->participants->count() }} participants</small>
                    </div>
                    
                    <div class="participants-list">
                        @foreach($room->participants as $participant)
                        <div class="participant-item p-2 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm me-2">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        <small class="text-white">{{ strtoupper(substr($participant->user->name, 0, 1)) }}</small>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $participant->user->name }}</div>
                                    <small class="text-muted">{{ ucfirst($participant->role) }}</small>
                                </div>
                                @if($participant->is_muted)
                                <i class="fas fa-microphone-slash text-danger" title="Muet"></i>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Zone de chat --}}
        <div class="col-md-9">
            <div class="card h-100 d-flex flex-column">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">{{ $room->name }}</h5>
                            <small class="text-muted">{{ $room->description }}</small>
                        </div>
                        <a href="{{ route('chat.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                
                {{-- Messages --}}
                <div class="card-body flex-grow-1 d-flex flex-column p-0">
                    <div id="messagesContainer" class="flex-grow-1 p-3" style="overflow-y: auto; max-height: 500px;">
                        @foreach($messages as $message)
                        <div class="message-item mb-3" data-message-id="{{ $message->id }}">
                            @if($message->type === 'system')
                            <div class="text-center">
                                <small class="text-muted bg-light px-2 py-1 rounded">{{ $message->message }}</small>
                            </div>
                            @else
                            <div class="d-flex {{ $message->user_id === Auth::id() ? 'justify-content-end' : '' }}">
                                <div class="message-bubble {{ $message->user_id === Auth::id() ? 'bg-primary text-white' : 'bg-light' }} p-2 rounded" style="max-width: 70%;">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <small class="fw-bold">{{ $message->user->name }}</small>
                                        <small class="text-muted">{{ $message->created_at->format('H:i') }}</small>
                                    </div>
                                    @if($message->reply_to)
                                    <div class="reply-to bg-secondary bg-opacity-25 p-1 rounded mb-1">
                                        <small>Réponse à {{ $message->replyTo->user->name }}: {{ Str::limit($message->replyTo->message, 50) }}</small>
                                    </div>
                                    @endif
                                    <div>{{ $message->message }}</div>
                                    @if($message->is_edited)
                                    <small class="text-muted">(modifié)</small>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Zone de saisie --}}
                    <div class="border-top p-3">
                        <form id="messageForm" enctype="multipart/form-data">
                            <div class="input-group">
                                <input type="text" class="form-control" id="messageInput" name="message" placeholder="Tapez votre message..." maxlength="2000">
                                <input type="file" id="fileInput" name="file" style="display: none;" accept="image/*,.pdf,.doc,.docx,.txt">
                                <button type="button" class="btn btn-outline-secondary" id="fileBtn">
                                    <i class="fas fa-paperclip"></i>
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomId = {{ $room->id }};
    const userId = {{ Auth::id() }};
    let lastMessageId = {{ $messages->last()?->id ?? 0 }};
    
    const messagesContainer = document.getElementById('messagesContainer');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');
    const fileBtn = document.getElementById('fileBtn');
    
    // Auto-scroll to bottom
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
    
    // File upload
    fileBtn.addEventListener('click', () => fileInput.click());
    
    // Send message
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const message = messageInput.value.trim();
        const file = fileInput.files[0];
        
        if (!message && !file) return;
        
        fetch(`/chat/rooms/${roomId}/messages`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageInput.value = '';
                fileInput.value = '';
                loadNewMessages();
            }
        })
        .catch(error => console.error('Erreur:', error));
    });
    
    // Load new messages
    function loadNewMessages() {
        fetch(`/chat/rooms/${roomId}/messages?last_message_id=${lastMessageId}`)
        .then(response => response.json())
        .then(data => {
            data.messages.forEach(message => {
                addMessageToUI(message);
                lastMessageId = Math.max(lastMessageId, message.id);
            });
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        })
        .catch(error => console.error('Erreur:', error));
    }
    
    function addMessageToUI(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message-item mb-3';
        messageDiv.dataset.messageId = message.id;
        
        if (message.type === 'system') {
            messageDiv.innerHTML = `
                <div class="text-center">
                    <small class="text-muted bg-light px-2 py-1 rounded">${message.message}</small>
                </div>
            `;
        } else {
            const isOwn = message.user.id === userId;
            messageDiv.innerHTML = `
                <div class="d-flex ${isOwn ? 'justify-content-end' : ''}">
                    <div class="message-bubble ${isOwn ? 'bg-primary text-white' : 'bg-light'} p-2 rounded" style="max-width: 70%;">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <small class="fw-bold">${message.user.name}</small>
                            <small class="text-muted">${message.created_at}</small>
                        </div>
                        <div>${message.message}</div>
                        ${message.is_edited ? '<small class="text-muted">(modifié)</small>' : ''}
                    </div>
                </div>
            `;
        }
        
        messagesContainer.appendChild(messageDiv);
    }
    
    // Poll for new messages
    setInterval(loadNewMessages, 3000);
    
    // Leave room
    document.getElementById('leaveRoomBtn').addEventListener('click', function() {
        if (confirm('Êtes-vous sûr de vouloir quitter cette room ?')) {
            fetch(`/chat/rooms/${roomId}/leave`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '/chat';
                }
            });
        }
    });
});
</script>
@endpush
