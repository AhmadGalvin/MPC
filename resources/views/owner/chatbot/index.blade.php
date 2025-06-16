@extends('layouts.owner')

@section('header', 'AI Pet Care Assistant')

@section('content')
<div class="card">
    <div class="card-title">Chat with AI Assistant</div>
    <div class="chat-container" style="height: 500px; display: flex; flex-direction: column;">
        <div id="chat-messages" style="flex: 1; overflow-y: auto; padding: 1rem; background-color: #f8fafc; border-radius: 0.5rem; margin-bottom: 1rem;">
            <div class="message system" style="margin-bottom: 1rem; padding: 1rem; background-color: #e8f5e9; border-radius: 0.5rem;">
                <p style="margin: 0; color: #1b5e20;">
                    Hello! I'm your AI Pet Care Assistant. I can help you with general questions about pet care, behavior, and wellness. 
                    Remember, while I can provide general advice, please consult a veterinarian for specific medical concerns.
                </p>
            </div>
            @if(session('response'))
            <div class="message ai">
                <strong>AI Response:</strong>
                <div class="markdown-content">{{ session('response') }}</div>
            </div>
            @endif
        </div>
        
        <form id="chat-form" class="chat-input" style="display: flex; gap: 1rem;">
            @csrf
            <input type="text" 
                   id="user-input" 
                   name="message"
                   style="flex: 1; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem; font-size: 1rem;" 
                   placeholder="Type your message here..."
                   required>
            <button type="submit" 
                    id="send-button" 
                    style="padding: 0.75rem 1.5rem; background-color: #1a56db; color: white; border: none; border-radius: 0.375rem; font-weight: 500; cursor: pointer;">
                Send
            </button>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
    .message {
        margin-bottom: 1rem;
        padding: 1rem;
        border-radius: 0.5rem;
        max-width: 80%;
    }

    .message.user {
        background-color: #e8f5e9;
        color: #1b5e20;
        margin-left: auto;
    }

    .message.ai {
        background-color: #f3f4f6;
        color: #374151;
        margin-right: auto;
    }

    .message p {
        margin: 0;
        line-height: 1.5;
    }

    .markdown-content {
        margin-top: 0.5rem;
        white-space: pre-wrap;
    }

    .markdown-content p {
        margin-bottom: 0.5rem;
    }

    .markdown-content ul, .markdown-content ol {
        margin-left: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .markdown-content code {
        background-color: #e5e7eb;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-family: monospace;
    }

    #send-button:hover {
        background-color: #1e40af;
    }

    #send-button:disabled {
        background-color: #9ca3af;
        cursor: not-allowed;
    }

    .typing-indicator {
        display: flex;
        padding: 1rem;
        background-color: #f3f4f6;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        width: fit-content;
    }

    .typing-indicator span {
        width: 8px;
        height: 8px;
        background-color: #6b7280;
        border-radius: 50%;
        margin: 0 2px;
        animation: typing 1s infinite;
    }

    .typing-indicator span:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-indicator span:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const userInput = document.getElementById('user-input');
    const sendButton = document.getElementById('send-button');
    let isProcessing = false;

    // Initialize markdown parser
    marked.setOptions({
        breaks: true,
        gfm: true,
        sanitize: true
    });

    function addMessage(content, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${type}`;
        
        if (type === 'ai') {
            messageDiv.innerHTML = `
                <strong>AI Response:</strong>
                <div class="markdown-content">${marked(content)}</div>
            `;
        } else {
            messageDiv.innerHTML = `<p>${content}</p>`;
        }
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function showTypingIndicator() {
        const indicator = document.createElement('div');
        indicator.className = 'typing-indicator';
        indicator.innerHTML = `
            <span></span>
            <span></span>
            <span></span>
        `;
        chatMessages.appendChild(indicator);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return indicator;
    }

    async function handleSubmit(e) {
        e.preventDefault();
        if (isProcessing || !userInput.value.trim()) return;

        const message = userInput.value.trim();
        userInput.value = '';
        addMessage(message, 'user');

        isProcessing = true;
        sendButton.disabled = true;
        const indicator = showTypingIndicator();

        try {
            const response = await fetch('{{ route("owner.chatbot.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message })
            });

            if (!response.ok) {
                const errorData = await response.json();
                console.error('Server error:', errorData);
                throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            indicator.remove();

            if (data.success) {
                addMessage(data.message, 'ai');
            } else {
                addMessage('Sorry, I encountered an error. Please try again.', 'ai');
            }
        } catch (error) {
            console.error('Error:', error);
            indicator.remove();
            addMessage('Sorry, I encountered an error. Please try again.', 'ai');
        } finally {
            isProcessing = false;
            sendButton.disabled = false;
        }
    }

    chatForm.addEventListener('submit', handleSubmit);

    // Convert any existing markdown content
    document.querySelectorAll('.markdown-content').forEach(element => {
        const content = element.textContent;
        element.innerHTML = marked(content);
    });
});
</script>
@endsection 