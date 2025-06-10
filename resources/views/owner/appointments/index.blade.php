@extends('layouts.owner')

@section('title', 'My Appointments')
@section('header', 'My Appointments')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Appointments List</h2>
            <a href="{{ route('owner.appointments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                Book New Appointment
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border">
            <div class="divide-y">
                @forelse($appointments as $appointment)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="font-semibold">{{ $appointment->pet->name }}</h3>
                                    <p class="text-sm text-gray-600">With Dr. {{ $appointment->doctor->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($appointment->scheduled_date)->format('M d, Y') }} 
                                        at {{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('h:i A') }}
                                    </p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $appointment->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('owner.appointments.show', $appointment) }}" 
                                   class="text-blue-600 hover:text-blue-800">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="mb-2">No appointments found</p>
                        <a href="{{ route('owner.appointments.create') }}" class="text-blue-600 hover:text-blue-800">
                            Book your first appointment →
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="mt-6">
            {{ $appointments->links() }}
        </div>
    </div>
</div>
@endsection 