@extends('layouts.owner')

@section('title', 'My Appointments')
@section('header', 'My Appointments')

@section('content')
<div class="py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">My Appointments</h2>
        <a href="{{ route('owner.appointments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Book New Appointment
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border">
        <div class="divide-y">
            @forelse($consultations as $consultation)
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-stethoscope text-blue-600"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold">{{ $consultation->pet->name }}</h3>
                                <p class="text-sm text-gray-600">With Dr. {{ $consultation->doctor->user->name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $consultation->scheduled_date->format('M d, Y') }} at {{ $consultation->scheduled_time->format('h:i A') }}
                                </p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $consultation->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($consultation->status) }}
                                </span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('owner.appointments.show', $consultation) }}" 
                               class="text-blue-600 hover:text-blue-800">
                                View Details →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-gray-500">
                    <i class="fas fa-calendar-times text-4xl mb-4"></i>
                    <p class="mb-2">No appointments found</p>
                    <a href="{{ route('owner.appointments.create') }}" class="text-blue-600 hover:text-blue-800">
                        Book your first appointment →
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <div class="mt-6">
        {{ $consultations->links() }}
    </div>
</div>
@endsection 