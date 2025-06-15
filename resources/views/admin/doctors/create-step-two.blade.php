@extends('layouts.admin')

@section('header', 'Add New Doctor - Step 2')

@section('content')
<div class="card">
    <div class="card-title">Complete Doctor Profile</div>
    <p style="margin-bottom: 1.5rem; color: #6b7280;">Step 2 of 2 - Doctor Information for {{ $user->name }}</p>

    <form action="{{ route('admin.doctors.store', $user) }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Specialization</label>
            <input type="text" 
                   name="specialization" 
                   value="{{ old('specialization') }}" 
                   required
                   placeholder="e.g., Small Animal Medicine"
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            @error('specialization')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">SIP Number</label>
            <input type="text" 
                   name="sip_number" 
                   value="{{ old('sip_number') }}" 
                   required
                   placeholder="e.g., SIP-001-2024"
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            @error('sip_number')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Consultation Fee (Rp)</label>
            <input type="number" 
                   name="consultation_fee" 
                   value="{{ old('consultation_fee') }}" 
                   required
                   min="0"
                   step="1000"
                   placeholder="e.g., 100000"
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            @error('consultation_fee')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Schedule</label>
            <div id="schedule-container">
                <div class="schedule-item" style="border: 1px solid #d1d5db; padding: 1rem; border-radius: 0.375rem; margin-bottom: 0.5rem;">
                    <div style="margin-bottom: 0.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem;">Day</label>
                        <select name="schedule[0][day]" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                            <option value="">Select Day</option>
                            <option value="monday" {{ old('schedule.0.day') == 'monday' ? 'selected' : '' }}>Monday</option>
                            <option value="tuesday" {{ old('schedule.0.day') == 'tuesday' ? 'selected' : '' }}>Tuesday</option>
                            <option value="wednesday" {{ old('schedule.0.day') == 'wednesday' ? 'selected' : '' }}>Wednesday</option>
                            <option value="thursday" {{ old('schedule.0.day') == 'thursday' ? 'selected' : '' }}>Thursday</option>
                            <option value="friday" {{ old('schedule.0.day') == 'friday' ? 'selected' : '' }}>Friday</option>
                            <option value="saturday" {{ old('schedule.0.day') == 'saturday' ? 'selected' : '' }}>Saturday</option>
                            <option value="sunday" {{ old('schedule.0.day') == 'sunday' ? 'selected' : '' }}>Sunday</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 0.5rem;">Start Time</label>
                            <input type="time" 
                                   name="schedule[0][start_time]" 
                                   value="{{ old('schedule.0.start_time') }}"
                                   required
                                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 0.5rem;">End Time</label>
                            <input type="time" 
                                   name="schedule[0][end_time]" 
                                   value="{{ old('schedule.0.end_time') }}"
                                   required
                                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" 
                    onclick="addSchedule()" 
                    style="margin-top: 0.5rem; padding: 0.5rem 1rem; background-color: #4b5563; color: white; border-radius: 0.375rem; border: none;">
                <i class="fas fa-plus"></i> Add Schedule
            </button>
            @error('schedule')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
            @error('schedule.*')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem;">
            <a href="{{ route('admin.doctors.index') }}" 
               class="btn btn-secondary" 
               style="background-color: #6b7280;">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Doctor Profile
            </button>
        </div>
    </form>
</div>

<script>
let scheduleCount = 1;

function addSchedule() {
    const container = document.getElementById('schedule-container');
    const newSchedule = document.createElement('div');
    newSchedule.className = 'schedule-item';
    newSchedule.style = 'border: 1px solid #d1d5db; padding: 1rem; border-radius: 0.375rem; margin-bottom: 0.5rem;';
    
    newSchedule.innerHTML = `
        <div style="margin-bottom: 0.5rem;">
            <label style="display: block; margin-bottom: 0.5rem;">Day</label>
            <select name="schedule[${scheduleCount}][day]" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                <option value="">Select Day</option>
                <option value="monday">Monday</option>
                <option value="tuesday">Tuesday</option>
                <option value="wednesday">Wednesday</option>
                <option value="thursday">Thursday</option>
                <option value="friday">Friday</option>
                <option value="saturday">Saturday</option>
                <option value="sunday">Sunday</option>
            </select>
        </div>
        <div style="display: flex; gap: 1rem;">
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 0.5rem;">Start Time</label>
                <input type="time" 
                       name="schedule[${scheduleCount}][start_time]" 
                       required
                       style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            </div>
            <div style="flex: 1;">
                <label style="display: block; margin-bottom: 0.5rem;">End Time</label>
                <input type="time" 
                       name="schedule[${scheduleCount}][end_time]" 
                       required
                       style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            </div>
        </div>
        <button type="button" 
                onclick="this.parentElement.remove()" 
                style="margin-top: 0.5rem; padding: 0.5rem 1rem; background-color: #dc2626; color: white; border-radius: 0.375rem; border: none;">
            <i class="fas fa-trash"></i> Remove
        </button>
    `;
    
    container.appendChild(newSchedule);
    scheduleCount++;
}

// Re-add old schedule items if validation fails
@if (old('schedule'))
    @foreach (old('schedule') as $index => $schedule)
        @if ($index > 0) // Skip first schedule as it's already in the form
            document.addEventListener('DOMContentLoaded', function() {
                addSchedule();
            });
        @endif
    @endforeach
@endif
</script>

<style>
.card {
    max-width: 42rem;
    margin: 0 auto;
}

.btn {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background-color: #2563eb;
    color: white;
    border: none;
}

.btn-primary:hover {
    background-color: #1d4ed8;
}

.btn-secondary {
    background-color: #6b7280;
    color: white;
    border: none;
    text-decoration: none;
}

.btn-secondary:hover {
    background-color: #4b5563;
}
</style>
@endsection 