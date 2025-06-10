@extends('layouts.doctor')

@section('title', 'Edit Appointment')
@section('header', 'Edit Appointment')

@section('content')
<div class="py-6">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('doctor.appointments.show', $appointment) }}" 
           class="text-gray-600 hover:text-gray-900 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Appointment Details
        </a>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Appointment</h2>

            <form action="{{ route('doctor.appointments.update', $appointment) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Schedule Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900">Schedule Information</h3>
                        
                        <!-- Date -->
                        <div>
                            <label for="scheduled_date" class="block text-sm font-medium text-gray-700">Date</label>
                            <input type="date" name="scheduled_date" id="scheduled_date" 
                                   value="{{ old('scheduled_date', $appointment->scheduled_date->format('Y-m-d')) }}"
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
                                   value="{{ old('scheduled_time', \Carbon\Carbon::parse($appointment->scheduled_time)->format('H:i')) }}"
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
                                <option value="checkup" {{ old('type', $appointment->type) === 'checkup' ? 'selected' : '' }}>Check-up</option>
                                <option value="vaccination" {{ old('type', $appointment->type) === 'vaccination' ? 'selected' : '' }}>Vaccination</option>
                                <option value="surgery" {{ old('type', $appointment->type) === 'surgery' ? 'selected' : '' }}>Surgery</option>
                                <option value="grooming" {{ old('type', $appointment->type) === 'grooming' ? 'selected' : '' }}>Grooming</option>
                                <option value="emergency" {{ old('type', $appointment->type) === 'emergency' ? 'selected' : '' }}>Emergency</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" id="status" 
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                    required>
                                <option value="pending" {{ old('status', $appointment->status) === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ old('status', $appointment->status) === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="completed" {{ old('status', $appointment->status) === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $appointment->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
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
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('notes', $appointment->notes) }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fee -->
                        <div>
                            <label for="fee" class="block text-sm font-medium text-gray-700">Fee (₱)</label>
                            <input type="number" name="fee" id="fee" step="0.01"
                                   value="{{ old('fee', $appointment->fee) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                                   required>
                            @error('fee')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cancellation Reason (shown only when status is cancelled) -->
                        <div id="cancellationReasonContainer" class="hidden">
                            <label for="cancellation_reason" class="block text-sm font-medium text-gray-700">Cancellation Reason</label>
                            <textarea name="cancellation_reason" id="cancellation_reason" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('cancellation_reason', $appointment->cancellation_reason) }}</textarea>
                            @error('cancellation_reason')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('doctor.appointments.show', $appointment) }}" 
                       class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark">
                        Update Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide cancellation reason based on status
    const statusSelect = document.getElementById('status');
    const cancellationReasonContainer = document.getElementById('cancellationReasonContainer');
    const cancellationReasonTextarea = document.getElementById('cancellation_reason');

    function toggleCancellationReason() {
        if (statusSelect.value === 'cancelled') {
            cancellationReasonContainer.classList.remove('hidden');
            cancellationReasonTextarea.setAttribute('required', 'required');
        } else {
            cancellationReasonContainer.classList.add('hidden');
            cancellationReasonTextarea.removeAttribute('required');
        }
    }

    // Initial check
    toggleCancellationReason();

    // Listen for changes
    statusSelect.addEventListener('change', toggleCancellationReason);
</script>
@endpush
@endsection 