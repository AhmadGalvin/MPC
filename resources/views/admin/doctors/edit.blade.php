@extends('layouts.admin')

@section('header', 'Edit Doctor')

@section('content')
<div class="card">
    <div class="card-title">Edit Doctor Information</div>

    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Name</label>
            <input type="text" 
                   name="name" 
                   value="{{ old('name', $doctor->user->name) }}" 
                   required
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            @error('name')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Email</label>
            <input type="email" 
                   name="email" 
                   value="{{ old('email', $doctor->user->email) }}" 
                   required
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            @error('email')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Password</label>
            <input type="password" 
                   name="password" 
                   placeholder="Leave blank to keep current password"
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            @error('password')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Confirm Password</label>
            <input type="password" 
                   name="password_confirmation" 
                   placeholder="Leave blank to keep current password"
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Specialization</label>
            <input type="text" 
                   name="specialization" 
                   value="{{ old('specialization', $doctor->specialization) }}" 
                   required
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            @error('specialization')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">SIP Number</label>
            <input type="text" 
                   name="sip_number" 
                   value="{{ old('sip_number', $doctor->sip_number) }}" 
                   required
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            @error('sip_number')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Consultation Fee (Rp)</label>
            <input type="number" 
                   name="consultation_fee" 
                   value="{{ old('consultation_fee', $doctor->consultation_fee) }}" 
                   required
                   min="0"
                   step="1000"
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            @error('consultation_fee')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Schedule</label>
            <div id="schedule-container">
                @foreach($doctor->schedule as $index => $schedule)
                <div class="schedule-item" style="border: 1px solid #d1d5db; padding: 1rem; border-radius: 0.375rem; margin-bottom: 0.5rem;">
                    <div style="margin-bottom: 0.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem;">Day</label>
                        <select name="schedule[{{ $index }}][day]" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                            <option value="">Select Day</option>
                            @foreach(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day)
                                <option value="{{ $day }}" {{ old("schedule.{$index}.day", $schedule['day']) == $day ? 'selected' : '' }}>
                                    {{ ucfirst($day) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div style="display: flex; gap: 1rem;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 0.5rem;">Start Time</label>
                            <input type="time" 
                                   name="schedule[{{ $index }}][start_time]" 
                                   value="{{ old("schedule.{$index}.start_time", $schedule['start_time']) }}"
                                   required
                                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 0.5rem;">End Time</label>
                            <input type="time" 
                                   name="schedule[{{ $index }}][end_time]" 
                                   value="{{ old("schedule.{$index}.end_time", $schedule['end_time']) }}"
                                   required
                                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                    </div>
                    @if(!$loop->first)
                    <button type="button" 
                            onclick="this.parentElement.remove()" 
                            style="margin-top: 0.5rem; padding: 0.5rem 1rem; background-color: #dc2626; color: white; border-radius: 0.375rem; border: none;">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                    @endif
                </div>
                @endforeach
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

        <div style="margin-bottom: 1rem;">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" 
                       name="is_available_for_consultation" 
                       value="1"
                       {{ old('is_available_for_consultation', $doctor->is_available_for_consultation) ? 'checked' : '' }}
                       style="border-radius: 0.25rem;">
                <span style="font-weight: 500;">Available for Consultation</span>
            </label>
            @error('is_available_for_consultation')
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
                <i class="fas fa-save"></i> Update Doctor
            </button>
        </div>
    </form>
</div>

<script>
let scheduleCount = {{ count($doctor->schedule) }};

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