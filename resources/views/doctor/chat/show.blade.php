@extends('layouts.doctor')

@section('title', 'Chat with Patient')
@section('header', 'Chat with ' . $consultation->owner->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Pet Profile Sidebar -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold mb-4">Pet Information</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="w-20 h-20 bg-primary rounded-full mx-auto flex items-center justify-center text-white">
                                <i class="fas fa-paw text-3xl"></i>
                            </div>
                            <h4 class="text-center font-semibold mt-2">{{ $consultation->pet->name }}</h4>
                        </div>
                        
                        <div class="space-y-2">
                            <div>
                                <label class="text-sm text-gray-600">Species</label>
                                <p class="font-medium">{{ $consultation->pet->species }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Breed</label>
                                <p class="font-medium">{{ $consultation->pet->breed }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Age</label>
                                <p class="font-medium">{{ $consultation->pet->age }} years old</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Gender</label>
                                <p class="font-medium">{{ ucfirst($consultation->pet->gender) }}</p>
                            </div>
                            <div>
                                <label class="text-sm text-gray-600">Weight</label>
                                <p class="font-medium">{{ $consultation->pet->weight }} kg</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t">
                            <h4 class="font-semibold mb-2">Owner Information</h4>
                            <div class="space-y-2">
                                <div>
                                    <label class="text-sm text-gray-600">Name</label>
                                    <p class="font-medium">{{ $consultation->owner->name }}</p>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600">Email</label>
                                    <p class="font-medium">{{ $consultation->owner->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Section -->
            <div class="md:col-span-3">
                <div class="bg-white rounded-lg shadow-sm h-[600px] flex flex-col">
                    <!-- Chat Header -->
                    <div class="p-4 border-b">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center text-white">
                                    <i class="fas fa-user text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold">{{ $consultation->owner->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $consultation->pet->name }} ({{ $consultation->pet->species }})</p>
                                </div>
                            </div>
                            <a href="{{ route('doctor.consultations.index') }}" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Chat Messages -->
                    <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4">
                        <!-- Messages will be loaded here -->
                    </div>

                    <!-- Chat Input -->
                    <div class="border-t p-4">
                        <form id="chat-form" class="flex space-x-2">
                            <input type="text" 
                                   id="message-input" 
                                   class="flex-1 rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" 
                                   placeholder="Type your message...">
                            <button type="submit" 
                                    class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-dark">
                                <i class="fas fa-paper-plane mr-1"></i>
                                Send
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const consultationId = {{ $consultation->id }};
    let lastMessageId = 0;

    // Function to add a new message to the chat
    function addMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `flex ${message.sender_id == {{ auth()->id() }} ? 'justify-end' : 'justify-start'}`;
        
        messageDiv.innerHTML = `
            <div class="max-w-[70%] ${message.sender_id == {{ auth()->id() }} ? 'bg-primary text-white' : 'bg-gray-100'} rounded-lg px-4 py-2">
                <p class="text-sm">${message.message}</p>
                <p class="text-xs ${message.sender_id == {{ auth()->id() }} ? 'text-white/70' : 'text-gray-500'} mt-1">
                    ${new Date(message.created_at).toLocaleTimeString()}
                </p>
            </div>
        `;
        
        document.getElementById('chat-messages').appendChild(messageDiv);
        scrollToBottom();
    }

    // Function to scroll chat to bottom
    function scrollToBottom() {
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Load initial messages
    function loadMessages() {
        fetch(`/doctor/consultations/${consultationId}/messages`)
            .then(response => response.json())
            .then(messages => {
                document.getElementById('chat-messages').innerHTML = '';
                messages.forEach(addMessage);
                scrollToBottom();
            });
    }

    // Send message
    document.getElementById('chat-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const input = document.getElementById('message-input');
        const message = input.value.trim();
        
        if (message) {
            fetch(`/doctor/consultations/${consultationId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            })
            .then(response => response.json())
            .then(message => {
                addMessage(message);
                input.value = '';
                scrollToBottom();
            });
        }
    });

    // Poll for new messages
    setInterval(loadMessages, 3000);

    // Load initial messages
    loadMessages();
</script>
@endpush

@endsection 