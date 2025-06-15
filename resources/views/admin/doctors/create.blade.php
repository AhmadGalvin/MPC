@extends('layouts.admin')

@section('header', 'Add New Doctor')

@section('content')
<div class="card">
    <div class="card-title">Doctor Information</div>

    <form action="{{ route('admin.doctors.store') }}" method="POST">
        @csrf
        
        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Name</label>
            <input type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
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
                   value="{{ old('email') }}" 
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
                   required
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
            @error('password')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="margin-bottom: 1rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Specialization</label>
            <input type="text" 
                   name="specialization" 
                   value="{{ old('specialization') }}" 
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
                   value="{{ old('sip_number') }}" 
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
                   value="{{ old('consultation_fee') }}" 
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
                <div class="schedule-item" style="border: 1px solid #d1d5db; padding: 1rem; border-radius: 0.375rem; margin-bottom: 0.5rem;">
                    <div style="margin-bottom: 0.5rem;">
                        <label style="display: block; margin-bottom: 0.5rem;">Day</label>
                        <select name="schedule[0][day]" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
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
                                   name="schedule[0][start_time]" 
                                   required
                                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 0.5rem;">End Time</label>
                            <input type="time" 
                                   name="schedule[0][end_time]" 
                                   required
                                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" 
                    onclick="addSchedule()" 
                    style="margin-top: 0.5rem; padding: 0.5rem 1rem; background-color: #4b5563; color: white; border-radius: 0.375rem; border: none;">
                Add Schedule
            </button>
            @error('schedule')
                <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.doctors.index') }}" 
               class="btn btn-primary" 
               style="background-color: #6b7280;">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Doctor
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
            Remove
        </button>
    `;
    
    container.appendChild(newSchedule);
    scheduleCount++;
}
</script>
@endsection 