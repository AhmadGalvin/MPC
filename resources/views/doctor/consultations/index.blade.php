@extends('layouts.doctor')

@section('title', 'Consultations')
@section('header', 'Consultations')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Status Toggle Section -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-semibold">Consultation Status</h2>
                    <p class="text-gray-600 mt-1">
                        You are currently 
                        <span class="font-medium {{ $doctor->is_available_for_consultation ? 'text-green-600' : 'text-red-600' }}">
                            {{ $doctor->is_available_for_consultation ? 'accepting' : 'not accepting' }}
                        </span> 
                        consultations
                    </p>
                </div>
                <form action="{{ route('doctor.consultations.toggle-status') }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-4 py-2 rounded-md {{ $doctor->is_available_for_consultation 
                                ? 'bg-red-500 hover:bg-red-600' 
                                : 'bg-green-500 hover:bg-green-600' }} text-white">
                        {{ $doctor->is_available_for_consultation ? 'Close Session' : 'Open Session' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Paid Consultations List -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">Active Consultations</h2>

                <div class="space-y-4">
                    @forelse($consultations as $consultation)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white">
                                        <i class="fas fa-user text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold">{{ $consultation->owner->name }}</h3>
                                        <p class="text-sm text-gray-600">Pet: {{ $consultation->pet->name }} ({{ $consultation->pet->species }})</p>
                                        <p class="text-xs text-gray-500">Started: {{ $consultation->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    @if($consultation->payment_status === 'paid')
                                        <a href="{{ route('doctor.chat.show', $consultation) }}" 
                                           class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-dark">
                                            <i class="fas fa-comments mr-1"></i> Chat
                                        </a>
                                    @endif
                                    <a href="{{ route('doctor.consultations.show', $consultation) }}" 
                                       class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                                        <i class="fas fa-eye mr-1"></i> Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <p>No active consultations at the moment.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $consultations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
@endsection 