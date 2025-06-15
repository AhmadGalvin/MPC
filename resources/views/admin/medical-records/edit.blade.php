@extends('layouts.admin')

@section('header', 'Edit Medical Record')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div class="card-title" style="margin: 0;">Edit Medical Record</div>
    </div>

    <form action="{{ route('admin.medical-records.update', $medicalRecord) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <!-- Basic Information -->
            <div class="card" style="background-color: #f9fafb; margin: 0;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Basic Information</h3>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Pet</label>
                    <select name="pet_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        <option value="">Select Pet</option>
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id', $medicalRecord->pet_id) == $pet->id ? 'selected' : '' }}>
                                {{ $pet->name }} (Owner: {{ $pet->owner->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('pet_id')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Doctor</label>
                    <select name="doctor_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        <option value="">Select Doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id', $medicalRecord->doctor_id) == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Next Visit Date</label>
                    <input type="date" 
                           name="next_visit_date" 
                           value="{{ old('next_visit_date', $medicalRecord->next_visit_date?->format('Y-m-d')) }}"
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    @error('next_visit_date')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Medical Information -->
            <div class="card" style="background-color: #f9fafb; margin: 0;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Medical Information</h3>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Diagnosis</label>
                    <textarea name="diagnosis" 
                              required
                              rows="3"
                              style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">{{ old('diagnosis', $medicalRecord->diagnosis) }}</textarea>
                    @error('diagnosis')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Treatment</label>
                    <textarea name="treatment" 
                              required
                              rows="3"
                              style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">{{ old('treatment', $medicalRecord->treatment) }}</textarea>
                    @error('treatment')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Additional Notes</label>
                    <textarea name="notes" 
                              rows="3"
                              style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">{{ old('notes', $medicalRecord->notes) }}</textarea>
                    @error('notes')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.medical-records.show', $medicalRecord) }}" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                Update Record
            </button>
        </div>
    </form>
</div>
@endsection 