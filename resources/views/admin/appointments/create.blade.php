@extends('layouts.admin')

@section('header', 'Add Appointment')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div class="card-title" style="margin: 0;">Add New Appointment</div>
    </div>

    <form action="{{ route('admin.appointments.store') }}" method="POST">
        @csrf

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <!-- Basic Information -->
            <div class="card" style="background-color: #f9fafb; margin: 0;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Basic Information</h3>

                <!-- Select Pet -->
                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Pet</label>
                    <select name="pet_id" 
                            required
                            style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;"
                            {{ request('pet_id') ? 'disabled' : '' }}>
                        <option value="">Select Pet</option>
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ (old('pet_id', request('pet_id')) == $pet->id) ? 'selected' : '' }}>
                                {{ $pet->name }} (Owner: {{ $pet->owner->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('pet_id')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Select Doctor -->
                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Doctor</label>
                    <select name="doctor_id" 
                            required
                            style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        <option value="">Select Doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                Dr. {{ $doctor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Appointment Details -->
            <div class="card" style="background-color: #f9fafb; margin: 0;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Appointment Details</h3>

                <!-- Date -->
                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Date</label>
                    <input type="date" 
                           name="scheduled_date" 
                           value="{{ old('scheduled_date') }}"
                           min="{{ date('Y-m-d') }}"
                           required
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    @error('scheduled_date')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Time -->
                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Time</label>
                    <input type="time" 
                           name="scheduled_time" 
                           value="{{ old('scheduled_time') }}"
                           required
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    @error('scheduled_time')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reason for Visit -->
                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Reason for Visit</label>
                    <textarea name="notes" 
                              rows="3"
                              style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;"
                              placeholder="Please describe the reason for your visit">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                Create Appointment
            </button>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    /* Add any additional custom styles here */
</style>
@endpush

@push('scripts')
<script>
    // Add any JavaScript functionality here
</script>
@endpush 