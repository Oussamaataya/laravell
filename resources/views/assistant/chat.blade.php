@extends('layouts.app')

@section('title', 'Assistant IA')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Assistant IA</h5>
                </div>
                <div class="card-body">
                    <div id="chat-messages" class="mb-4" style="height: 400px; overflow-y: auto;">
                        @foreach($messages as $message)
                            <div class="mb-3">
                                <div class="d-flex {{ $message->role === 'assistant' ? 'flex-row' : 'flex-row-reverse' }}">
                                    <div class="message {{ $message->role === 'assistant' ? 'bg-light' : 'bg-primary text-white' }} p-3 rounded-3 mw-75" style="max-width: 75%;">
                                        <div class="message-content">
                                            {!! nl2br(e($message->content)) !!}
                                        </div>
                                        <div class="message-timestamp text-muted small mt-1">
                                            {{ $message->created_at->format('H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form id="chat-form" class="mt-4">
                        @csrf
                        <div class="input-group">
                            <textarea id="message-input" name="message" class="form-control" placeholder="Tapez votre message ici..." rows="2" required></textarea>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');

    // Scroll to bottom initially
    chatMessages.scrollTop = chatMessages.scrollHeight;

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (!message) return;

        // Add user message to chat
        appendMessage(message, 'user');
        
        // Clear input
        messageInput.value = '';
        
        try {
            const response = await fetch('{{ route("assistant.send-message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            
            if (data.success) {
                appendMessage(data.message, 'assistant');
            } else {
                appendMessage('Désolé, une erreur est survenue.', 'assistant');
            }
        } catch (error) {
            console.error('Error:', error);
            appendMessage('Désolé, une erreur est survenue.', 'assistant');
        }
    });

    function appendMessage(content, role) {
        const timestamp = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        const html = `
            <div class="mb-3">
                <div class="d-flex ${role === 'assistant' ? 'flex-row' : 'flex-row-reverse'}">
                    <div class="message ${role === 'assistant' ? 'bg-light' : 'bg-primary text-white'} p-3 rounded-3" style="max-width: 75%;">
                        <div class="message-content">
                            ${content.replace(/\n/g, '<br>')}
                        </div>
                        <div class="message-timestamp text-muted small mt-1">
                            ${timestamp}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        chatMessages.insertAdjacentHTML('beforeend', html);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Optional: Add typing indicator
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });
});
</script>
@endpush

<style>
.message {
    border-radius: 15px;
}
.message.bg-primary {
    background-color: #0d6efd !important;
}
#chat-messages::-webkit-scrollbar {
    width: 5px;
}
#chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
}
#chat-messages::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 5px;
}
#message-input {
    resize: none;
    border-radius: 20px;
    padding-right: 59px;
}
</style>
@endsection