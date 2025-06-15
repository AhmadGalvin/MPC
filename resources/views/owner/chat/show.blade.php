@extends('layouts.owner')

@section('title', 'Chat with Doctor')
@section('header', 'Chat with Dr. ' . $consultation->doctor->user->name)

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Pet Details Card -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold mb-4">Pet Details</h3>
                    <div class="space-y-4">
                        <div>
                            @if($consultation->pet->photo)
                                <img src="{{ Storage::url($consultation->pet->photo) }}" 
                                     alt="{{ $consultation->pet->name }}"
                                     class="w-full h-48 object-cover rounded-lg">
                            @else
                                <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-paw text-4xl text-gray-400"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700">Name</h4>
                            <p>{{ $consultation->pet->name }}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700">Species</h4>
                            <p>{{ $consultation->pet->species }}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700">Breed</h4>
                            <p>{{ $consultation->pet->breed }}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700">Age</h4>
                            <p>{{ $consultation->pet->birth_date ? \Carbon\Carbon::parse($consultation->pet->birth_date)->age . ' years' : 'Not specified' }}</p>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-700">Doctor</h4>
                            <p>Dr. {{ $consultation->doctor->user->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Section -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-lg shadow-sm h-full flex flex-col">
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
                    <div id="chat-messages" class="p-4 space-y-4 flex-1 overflow-y-auto" style="height: 400px;">
                        <!-- Messages will be loaded here -->
                    </div>

                    <!-- Chat Input -->
                    <div class="border-t p-4">
                        <form id="chat-form" class="flex gap-2">
                            @csrf
                            <input type="text" 
                                   id="message-input" 
                                   class="flex-1 rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" 
                                   placeholder="Type your message...">
                            <button type="submit" 
                                    class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-dark flex items-center">
                                Send
                                <i class="fas fa-paper-plane ml-2"></i>
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

    // Function to format time
    function formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
    }

    // Function to load messages
    function loadMessages() {
        fetch(`/chat/consultations/${consultationId}/messages`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const chatMessages = document.getElementById('chat-messages');
                chatMessages.innerHTML = '';

                data.data.forEach(message => {
                    const isOwn = message.sender_id === {{ auth()->id() }};
                    const messageHtml = `
                        <div class="flex ${isOwn ? 'justify-end' : 'justify-start'} mb-4">
                            ${!isOwn ? `
                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white mr-2">
                                <i class="fas fa-user-md text-sm"></i>
                            </div>
                            ` : ''}
                            <div class="max-w-[70%]">
                                <div class="${isOwn ? 'bg-primary text-white' : 'bg-gray-100 text-gray-800'} rounded-lg px-4 py-2">
                                    <p class="text-sm">${message.message}</p>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 ${isOwn ? 'text-right' : ''}">
                                    ${formatTime(message.created_at)}
                                </p>
                            </div>
                            ${isOwn ? `
                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white ml-2">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            ` : ''}
                        </div>
                    `;
                    chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                });

                // Scroll to bottom
                chatMessages.scrollTop = chatMessages.scrollHeight;

                // Mark messages as read
                fetch(`/chat/consultations/${consultationId}/messages/read`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
            }
        })
        .catch(error => console.error('Error loading messages:', error));
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
        const token = document.querySelector('input[name="_token"]').value;

        if (!message) return;

        fetch(`/chat/consultations/${consultationId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                input.value = '';
                loadMessages();
            } else {
                console.error('Error:', data.message);
                alert('Failed to send message. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message. Please try again.');
        });
    });
</script>
@endpush

@endsection 