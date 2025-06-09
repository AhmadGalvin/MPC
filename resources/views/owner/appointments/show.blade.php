@extends('layouts.owner')

@section('title', 'Appointment Details')
@section('header', 'Appointment Details')

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Appointment Details Card -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-semibold">Appointment Details</h2>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $consultation->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($consultation->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pet Information -->
                <div>
                    <h3 class="text-lg font-medium mb-4">Pet Information</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($consultation->pet->photo)
                                    <img src="{{ Storage::url($consultation->pet->photo) }}" alt="{{ $consultation->pet->name }}" class="w-16 h-16 rounded-full object-cover">
                                @else
                                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-paw text-2xl text-blue-600"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-semibold">{{ $consultation->pet->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $consultation->pet->species }} - {{ $consultation->pet->breed }}</p>
                                <p class="text-sm text-gray-500">{{ $consultation->pet->birth_date->age }} years old</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor Information -->
                <div>
                    <h3 class="text-lg font-medium mb-4">Doctor Information</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-md text-2xl text-blue-600"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="font-semibold">Dr. {{ $consultation->doctor->user->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $consultation->doctor->specialization }}</p>
                                <p class="text-sm text-gray-500">{{ $consultation->doctor->years_of_experience }} years of experience</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Schedule Information -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium mb-4">Schedule Information</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Date</p>
                                <p class="font-semibold">{{ $consultation->scheduled_date->format('F d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Time</p>
                                <p class="font-semibold">{{ $consultation->scheduled_time->format('h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reason for Visit -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium mb-4">Reason for Visit</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-gray-700">{{ $consultation->reason }}</p>
                    </div>
                </div>

                @if($consultation->status === 'completed')
                    <!-- Diagnosis and Treatment -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-medium mb-4">Diagnosis & Treatment</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Diagnosis</p>
                                <p class="text-gray-700">{{ $consultation->diagnosis }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Treatment</p>
                                <p class="text-gray-700">{{ $consultation->treatment }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 mb-1">Notes</p>
                                <p class="text-gray-700">{{ $consultation->notes }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Chat Section -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b">
                <h3 class="text-lg font-medium">Messages</h3>
            </div>

            <!-- Messages List -->
            <div class="p-6 space-y-4 max-h-96 overflow-y-auto" id="messages-container">
                @forelse($consultation->messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-lg {{ $message->sender_id === auth()->id() ? 'bg-blue-100' : 'bg-gray-100' }} rounded-lg px-4 py-2">
                            <div class="text-sm {{ $message->sender_id === auth()->id() ? 'text-blue-800' : 'text-gray-800' }}">
                                {{ $message->message }}
                            </div>
                            <div class="text-xs {{ $message->sender_id === auth()->id() ? 'text-blue-600' : 'text-gray-500' }} mt-1">
                                {{ $message->sender->name }} • {{ $message->created_at->format('M d, h:i A') }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500">
                        No messages yet
                    </div>
                @endforelse
            </div>

            <!-- Message Input -->
            @if($consultation->status !== 'completed')
                <div class="p-6 border-t">
                    <form action="{{ route('owner.appointments.messages.store', $consultation) }}" method="POST">
                        @csrf
                        <div class="flex space-x-4">
                            <input type="text" name="message" 
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Type your message..."
                                   required>
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Send
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Scroll to bottom of messages container
    const messagesContainer = document.getElementById('messages-container');
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
</script>
@endpush 