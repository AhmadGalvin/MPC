@extends('layouts.owner')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="py-6">
    <!-- Quick Actions -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('owner.pets.create') }}" class="block p-6 bg-blue-50 rounded-lg hover:bg-blue-100">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Add New Pet</h3>
                        <p class="text-sm text-gray-600">Register a new pet</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('owner.appointments.create') }}" class="block p-6 bg-green-50 rounded-lg hover:bg-green-100">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Book Appointment</h3>
                        <p class="text-sm text-gray-600">Schedule a consultation</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('products.index') }}" class="block p-6 bg-purple-50 rounded-lg hover:bg-purple-100">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Shop Products</h3>
                        <p class="text-sm text-gray-600">Browse pet products</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Appointments -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Upcoming Appointments</h2>
                <a href="{{ route('owner.appointments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All →</a>
            </div>
            <div class="space-y-4">
                @forelse($upcomingAppointments as $appointment)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-blue-600"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ $appointment->pet->name }}</h3>
                                <p class="text-sm text-gray-600">With Dr. {{ $appointment->doctor->name }}</p>
                                <p class="text-xs text-gray-500">{{ $appointment->scheduled_date->format('M d, Y') }} at {{ $appointment->scheduled_time->format('h:i A') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('owner.appointments.show', $appointment) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-calendar-times text-4xl mb-2"></i>
                        <p>No upcoming appointments</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pet Summary -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">My Pets</h2>
                <a href="{{ route('owner.pets.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All →</a>
            </div>
            <div class="space-y-4">
                @forelse($pets as $pet)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                @if($pet->photo)
                                    <img src="{{ Storage::url($pet->photo) }}" alt="{{ $pet->name }}" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-paw text-blue-600"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ $pet->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $pet->species }} - {{ $pet->breed }}</p>
                                <p class="text-xs text-gray-500">
                                    @if($pet->consultations->isNotEmpty())
                                        Last visit: {{ $pet->consultations->first()->created_at->format('M d, Y') }}
                                    @else
                                        No visits yet
                                    @endif
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('owner.pets.show', $pet) }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                @empty
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-paw text-4xl mb-2"></i>
                        <p>No pets registered</p>
                        <a href="{{ route('owner.pets.create') }}" class="text-blue-600 hover:text-blue-800 text-sm">Add your first pet →</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection 