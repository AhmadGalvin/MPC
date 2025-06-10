@extends('layouts.doctor')

@section('title', 'Appointment Details')
@section('header', 'Appointment Details')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="mb-6">
            <a href="{{ route('doctor.appointments.index') }}" class="text-blue-600 hover:text-blue-800">
                ← Back to Appointments
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-2xl font-bold mb-4">Appointment Information</h2>
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                    {{ $appointment->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $appointment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $appointment->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                    {{ $appointment->status === 'confirmed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Schedule</p>
                                <p class="font-medium">
                                    {{ \Carbon\Carbon::parse($appointment->scheduled_date)->format('l, M d, Y') }}
                                    at {{ \Carbon\Carbon::parse($appointment->scheduled_time)->format('h:i A') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Pet</p>
                                <p class="font-medium">{{ $appointment->pet->name }}</p>
                                <p class="text-sm text-gray-600">{{ $appointment->pet->species }} - {{ $appointment->pet->breed }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Owner</p>
                                <p class="font-medium">{{ $appointment->owner->name }}</p>
                                <p class="text-sm text-gray-600">{{ $appointment->owner->email }}</p>
                            </div>

                            @if($appointment->notes)
                                <div>
                                    <p class="text-sm text-gray-500">Notes</p>
                                    <p class="text-gray-700">{{ $appointment->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-3">
                        @if($appointment->status === 'pending')
                            <form action="{{ route('doctor.appointments.confirm', $appointment) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Confirm Appointment
                                </button>
                            </form>

                            <form action="{{ route('doctor.appointments.cancel', $appointment) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full bg-red-100 text-red-700 px-4 py-2 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    Cancel Appointment
                                </button>
                            </form>
                        @endif

                        @if($appointment->status === 'confirmed')
                            <form action="{{ route('doctor.appointments.complete', $appointment) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    Complete Appointment
                                </button>
                            </form>

                            <form action="{{ route('doctor.appointments.cancel', $appointment) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                @csrf
                                @method('PATCH')
                                <button type="submit" 
                                        class="w-full bg-red-100 text-red-700 px-4 py-2 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    Cancel Appointment
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 