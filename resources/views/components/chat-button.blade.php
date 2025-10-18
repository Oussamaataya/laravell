<div>
    <!-- Chat Widget -->
    <div id="chat-widget" class="chat-widget chat-widget-hidden">
        <div class="chat-header bg-primary text-white p-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Chatbot</h5>
            <button type="button" class="btn-close btn-close-white" onclick="toggleChat()"></button>
        </div>

        <div class="chat-body" id="chat-messages">
            <div class="messages-container px-3 py-4" id="messages"></div>
</div>
        <div class="chat-footer p-3 bg-light border-top d-flex gap-2">
            <input type="text" id="userMessage" class="form-control" placeholder="Tapez votre message..." required>
            <button id="sendBtn" class="btn btn-primary">Envoyer</button>
        </div>
    </div>

    <!-- Chat Toggle Button -->
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
.chat-widget-hidden { transform: translateY(100%); opacity: 0; pointer-events: none; }
.chat-body { flex-grow: 1; overflow-y: auto; background: #f8f9fa; padding: 10px; }
.chat-toggle-button {
    position: fixed; bottom: 20px; right: 20px; width: 50px; height: 50px;
    border-radius: 25px; background: #667eea; color: white; border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2); cursor: pointer; z-index: 1000;
    transition: transform 0.2s ease;
}
.chat-toggle-button:hover { transform: scale(1.1); }

.messages-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.message {
    max-width: 70%;
    padding: 10px 15px;
    border-radius: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    word-wrap: break-word;
}

/* Messages utilisateur à droite */
.message.user {
    background-color: #667eea;
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 0;
}

/* Messages bot à gauche */
.message.bot {
    background-color: #e4e6eb;
    color: black;
    align-self: flex-start;
    border-bottom-left-radius: 0;
}

#userMessage { flex-grow: 1; padding: 10px; border-radius: 20px; border: 1px solid #ccc; }
#sendBtn { padding: 10px 15px; border: none; background: #667eea; color: white; border-radius: 20px; cursor: pointer; }

@media (max-width: 576px) {
    .chat-widget { width: 100%; height: 100%; bottom: 0; right: 0; border-radius: 0; }
    .chat-footer { flex-direction: column; gap: 5px; }
}
</style>

<script>
let chatVisible = false;

function toggleChat() {
    const chatWidget = document.getElementById('chat-widget');
    chatVisible = !chatVisible;
    chatWidget.classList.toggle('chat-widget-hidden', !chatVisible);
    if(chatVisible) document.getElementById('userMessage').focus();
}

document.addEventListener('DOMContentLoaded', function() {
    const sendBtn = document.getElementById('sendBtn');
    const userMessageInput = document.getElementById('userMessage');
    const messagesDiv = document.getElementById('messages');

    function appendMessage(content, role) {
        const html = `<div class="message ${role}">${content}</div>`;
        messagesDiv.insertAdjacentHTML('beforeend', html);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    sendBtn.addEventListener('click', async () => {
        const message = userMessageInput.value.trim();
        if(!message) return;

        appendMessage(message, 'user');
        userMessageInput.value = '';

        try {
            const response = await fetch('/chat', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message })
            });
            const data = await response.json();
            appendMessage(data.response || "Je n'ai pas compris.", 'bot');
        } catch {
            appendMessage("Une erreur est survenue.", 'bot');
        }

        userMessageInput.focus();
    });

    userMessageInput.addEventListener('keydown', (e) => {
        if(e.key === 'Enter') sendBtn.click();
    });
});
</script>
