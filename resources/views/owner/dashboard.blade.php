@extends('layouts.owner')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <h2 class="text-2xl font-bold mb-6">Quick Actions</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <a href="{{ route('owner.pets.create') }}" class="bg-blue-50 p-6 rounded-lg hover:bg-blue-100">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Add New Pet</h3>
                        <p class="text-gray-600">Register a new pet</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('owner.appointments.create') }}" class="bg-green-50 p-6 rounded-lg hover:bg-green-100">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-900">Book Appointment</h3>
                        <p class="text-gray-600">Schedule a consultation</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Upcoming Appointments</h3>
                    <a href="{{ route('owner.appointments.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All →</a>
                </div>

                @php
                    $upcomingAppointments = auth()->user()->ownerAppointments()
                        ->with(['pet', 'doctor'])
                        ->where('scheduled_date', '>=', now()->format('Y-m-d'))
                        ->where('status', '!=', 'cancelled')
                        ->orderBy('scheduled_date')
                        ->orderBy('scheduled_time')
                        ->take(3)
                        ->get();
                @endphp

                @if($upcomingAppointments->count() > 0)
                    <div class="space-y-4">
                        @foreach($upcomingAppointments as $appointment)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($appointment->scheduled_date)->format('M d, Y') }}
                                        at {{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('h:i A') }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        Pet: {{ $appointment->pet->name }} ({{ $appointment->pet->species }})
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        Doctor: Dr. {{ $appointment->doctor->name }}
                                    </div>
                                </div>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $appointment->status === 'confirmed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mt-2">No upcoming appointments</p>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">My Pets</h3>
                    <a href="{{ route('owner.pets.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All →</a>
                </div>

                @if(auth()->user()->pets->count() > 0)
                    <div class="space-y-4">
                        @foreach(auth()->user()->pets->take(3) as $pet)
                            <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    @if($pet->photo)
                                        <img src="{{ asset('storage/' . $pet->photo) }}" alt="{{ $pet->name }}" class="h-12 w-12 rounded-full object-cover">
                                    @else
                                        <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $pet->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $pet->species }} - {{ $pet->breed }}</p>
                                </div>
                                <a href="{{ route('owner.pets.show', $pet) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                        <p class="mt-2">No pets registered yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 