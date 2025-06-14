@extends('layouts.owner')

@section('title', 'Chat with Doctor')
@section('header', 'Chat with Dr. ' . $consultation->doctor->user->name)

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-sm">
            <!-- Chat Header -->
            <div class="border-b p-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-user-md text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold">Dr. {{ $consultation->doctor->user->name }}</h3>
                        <p class="text-gray-600">{{ $consultation->doctor->specialization }}</p>
                    </div>
                </div>
            </div>

            <!-- Chat Messages -->
            <div id="chat-messages" class="p-4 space-y-4 h-[400px] overflow-y-auto">
                <!-- Messages will be loaded here -->
            </div>

            <!-- Chat Input -->
            <div class="border-t p-4">
                <form id="chat-form" class="flex gap-2">
                    <input type="text" id="message-input" class="flex-1 rounded-md border-gray-300" placeholder="Type your message...">
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-dark">
                        Send
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const consultationId = {{ $consultation->id }};
    let lastMessageId = 0;

    // Function to load messages
    function loadMessages() {
        fetch(`/owner/consultations/${consultationId}/messages`)
            .then(response => response.json())
            .then(messages => {
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.innerHTML = '';

                messages.forEach(message => {
                    const isOwn = message.sender_id === {{ auth()->id() }};
                    const messageHtml = `
                        <div class="flex ${isOwn ? 'justify-end' : 'justify-start'}">
                            <div class="max-w-[70%] ${isOwn ? 'bg-primary text-white' : 'bg-gray-100'} rounded-lg p-3">
                                <p class="text-sm">${message.message}</p>
                                <p class="text-xs ${isOwn ? 'text-primary-light' : 'text-gray-500'} mt-1">
                                    ${new Date(message.created_at).toLocaleTimeString()}
                                </p>
                            </div>
                        </div>
                    `;
                    chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                });

                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
    }

    // Load messages initially
    loadMessages();

    // Set up polling
    setInterval(loadMessages, 3000);

    // Handle form submission
    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('message-input');
        const message = input.value.trim();

        if (!message) return;

        fetch(`/owner/consultations/${consultationId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message })
        })
        .then(response => response.json())
        .then(() => {
            input.value = '';
            loadMessages();
        });
    });
</script>
@endpush 