@extends('layouts.doctor')

@section('title', 'Create Appointment')
@section('header', 'Create Appointment')

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

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Create New Appointment</h2>

            <form action="{{ route('doctor.appointments.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Schedule Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Schedule Information</h3>
                        
                        <!-- Pet Selection -->
                        <div>
                            <label for="pet_id" class="block text-sm font-medium text-gray-700">Select Patient</label>
                            <select name="pet_id" id="pet_id" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                    required>
                                <option value="">Select a patient</option>
                                @foreach($pets as $pet)
                                    <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                        {{ $pet->name }} ({{ $pet->owner->name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('pet_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div>
                            <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="scheduled_date" id="scheduled_date" 
                                   value="{{ old('scheduled_date', now()->format('Y-m-d')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                   required>
                            @error('scheduled_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time -->
                        <div>
                            <label for="scheduled_time" class="block text-sm font-medium text-gray-700">Time</label>
                            <input type="time" name="scheduled_time" id="scheduled_time" 
                                   value="{{ old('scheduled_time', now()->format('H:i')) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                   required>
                            @error('scheduled_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">Appointment Type</label>
                            <select name="type" id="type" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                    required>
                                <option value="checkup" {{ old('type') === 'checkup' ? 'selected' : '' }}>Check-up</option>
                                <option value="vaccination" {{ old('type') === 'vaccination' ? 'selected' : '' }}>Vaccination</option>
                                <option value="surgery" {{ old('type') === 'surgery' ? 'selected' : '' }}>Surgery</option>
                                <option value="grooming" {{ old('type') === 'grooming' ? 'selected' : '' }}>Grooming</option>
                                <option value="emergency" {{ old('type') === 'emergency' ? 'selected' : '' }}>Emergency</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Additional Information</h3>

                        <!-- Notes -->
                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="4"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fee -->
                        <div>
                            <label for="fee" class="block text-sm font-medium text-gray-700">Fee (₱)</label>
                            <input type="number" name="fee" id="fee" step="0.01"
                                   value="{{ old('fee', 0) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                   required>
                            @error('fee')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('doctor.appointments.index') }}" 
                       class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">
                        Create Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 