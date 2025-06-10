@extends('layouts.doctor')

@section('title', 'View Appointment')
@section('header', 'View Appointment')

@section('content')
<div class="py-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('doctor.appointments.index') }}" 
           class="text-gray-600 hover:text-gray-900 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Appointments
        </a>
    </div>

    <!-- Appointment Details -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <!-- Header -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Appointment Details</h2>
                    <p class="text-sm text-gray-500">Created {{ $appointment->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full 
                        @if($appointment->status === 'completed') bg-green-100 text-green-800
                        @elseif($appointment->status === 'confirmed') bg-blue-100 text-blue-800
                        @elseif($appointment->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-yellow-100 text-yellow-800 @endif">
                        {{ ucfirst($appointment->status) }}
                    </span>
                </div>
            </div>

            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Date & Time -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Schedule</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Date</label>
                            <p class="text-gray-900">{{ $appointment->scheduled_date->format('l, F j, Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Time</label>
                            <p class="text-gray-900">{{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('h:i A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Type</label>
                            <p class="text-gray-900">{{ ucfirst($appointment->type) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Pet Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Patient Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Pet Name</label>
                            <p class="text-gray-900">{{ $appointment->pet->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Species</label>
                            <p class="text-gray-900">{{ $appointment->pet->species }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Breed</label>
                            <p class="text-gray-900">{{ $appointment->pet->breed }}</p>
                        </div>
                    </div>
                </div>

                <!-- Owner Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Owner Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Name</label>
                            <p class="text-gray-900">{{ $appointment->owner->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="text-gray-900">{{ $appointment->owner->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Phone</label>
                            <p class="text-gray-900">{{ $appointment->owner->phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Notes</label>
                            <p class="text-gray-900">{{ $appointment->notes ?: 'No notes provided' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Fee</label>
                            <p class="text-gray-900">₱{{ number_format($appointment->fee, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 flex justify-end space-x-3">
                @if($appointment->status === 'pending')
                    <form action="{{ route('doctor.appointments.update', $appointment) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Confirm Appointment
                        </button>
                    </form>
                @endif

                @if($appointment->status === 'confirmed')
                    <form action="{{ route('doctor.appointments.update', $appointment) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            Mark as Completed
                        </button>
                    </form>
                @endif

                @if(in_array($appointment->status, ['pending', 'confirmed']))
                    <form action="{{ route('doctor.appointments.update', $appointment) }}" method="POST" class="inline">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" 
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                                onclick="return confirm('Are you sure you want to cancel this appointment?')">
                            Cancel Appointment
                        </button>
                    </form>
                @endif

                <a href="{{ route('doctor.appointments.edit', $appointment) }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    Edit Appointment
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 