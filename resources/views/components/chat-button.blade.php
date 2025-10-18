<div>
    <div id="chat-widget" class="chat-widget {{ $show ? '' : 'chat-widget-hidden' }}">
        <div class="chat-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Assistant IA</h5>
            <button type="button" class="btn-close btn-close-white" onclick="toggleChat()"></button>
        </div>
        <div class="chat-body" id="chat-messages">
            <div class="messages-container px-3 py-4">
                @foreach($messages as $message)
                    <div class="message mb-3 {{ $message->role === 'assistant' ? '' : 'message-user' }}">
                        <div class="message-content {{ $message->role === 'assistant' ? 'bg-light' : 'bg-primary text-white' }} p-2 rounded">
                            {!! nl2br(e($message->content)) !!}
                        </div>
                        <small class="text-muted">{{ $message->created_at->format('H:i') }}</small>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="chat-footer p-3 bg-light border-top">
            <form id="chat-form" class="d-flex gap-2">
                @csrf
                <input type="text" id="message-input" class="form-control" placeholder="Tapez votre message..." required>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <button id="chat-button" onclick="toggleChat()" class="chat-toggle-button">
        <i class="fas fa-comments"></i>
    </button>
</div>

<style>
.chat-widget {
    position: fixed;
    bottom: 80px;
    right: 20px;
    width: 350px;
    height: 500px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease, opacity 0.3s ease;
    z-index: 1000;
}

.chat-widget-hidden {
    transform: translateY(100%);
    opacity: 0;
    pointer-events: none;
}

.chat-body {
    flex-grow: 1;
    overflow-y: auto;
    background: #f8f9fa;
}

.chat-toggle-button {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    border-radius: 25px;
    background: #667eea;
    color: white;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    cursor: pointer;
    z-index: 1000;
    transition: transform 0.2s ease;
}

.chat-toggle-button:hover {
    transform: scale(1.1);
}

.messages-container {
    display: flex;
    flex-direction: column;
}

.message {
    max-width: 80%;
    margin-bottom: 1rem;
}

.message-user {
    align-self: flex-end;
}

.message-content {
    padding: 0.75rem 1rem;
    border-radius: 15px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.chat-header {
    border-radius: 10px 10px 0 0;
}

#message-input {
    border-radius: 20px;
    padding-right: 50px;
}

@media (max-width: 576px) {
    .chat-widget {
        width: 100%;
        height: 100%;
        bottom: 0;
        right: 0;
        border-radius: 0;
    }

    .chat-header {
        border-radius: 0;
    }
}
</style>

<script>
let chatVisible = false;

function toggleChat() {
    const chatWidget = document.getElementById('chat-widget');
    const chatButton = document.getElementById('chat-button');
    
    chatVisible = !chatVisible;
    if (chatVisible) {
        chatWidget.classList.remove('chat-widget-hidden');
        document.getElementById('message-input')?.focus();
    } else {
        chatWidget.classList.add('chat-widget-hidden');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('chat-form');
    const input = document.getElementById('message-input');
    const messagesContainer = document.querySelector('.messages-container');

    form?.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = input.value.trim();
        if (!message) return;

        // Add user message immediately
        appendMessage(message, 'user');
        input.value = '';

        try {
            const response = await fetch('{{ route("assistant.send-message") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            
            if (data.success) {
                appendMessage(data.message, 'assistant');
            } else {
                throw new Error('Response error');
            }
        } catch (error) {
            appendMessage('Désolé, une erreur est survenue. Veuillez réessayer.', 'assistant');
        }
    });

    function appendMessage(content, role) {
        const time = new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        const html = `
            <div class="message mb-3 ${role === 'assistant' ? '' : 'message-user'}">
                <div class="message-content ${role === 'assistant' ? 'bg-light' : 'bg-primary text-white'} p-2 rounded">
                    ${content.replace(/\n/g, '<br>')}
                </div>
                <small class="text-muted">${time}</small>
            </div>
        `;
        
        messagesContainer.insertAdjacentHTML('beforeend', html);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
});
</script>