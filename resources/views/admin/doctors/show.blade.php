@extends('layouts.admin')

@section('header', 'Doctor Details')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div class="card-title" style="margin: 0;">Doctor Information</div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('admin.doctors.edit', $doctor) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Doctor
            </a>
            <form action="{{ route('admin.doctors.destroy', $doctor) }}" 
                  method="POST" 
                  style="display: inline;"
                  onsubmit="return confirm('Are you sure you want to delete this doctor?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn btn-primary" 
                        style="background-color: #dc2626;">
                    <i class="fas fa-trash"></i> Delete Doctor
                </button>
            </form>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <!-- Basic Information -->
        <div class="card" style="background-color: #f9fafb; margin: 0;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Basic Information</h3>
            
            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Name:</label>
                <p style="margin: 0;">{{ $doctor->user->name }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Email:</label>
                <p style="margin: 0;">{{ $doctor->user->email }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Specialization:</label>
                <p style="margin: 0;">{{ $doctor->specialization }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">SIP Number:</label>
                <p style="margin: 0;">{{ $doctor->sip_number }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Consultation Fee:</label>
                <p style="margin: 0;">Rp {{ number_format($doctor->consultation_fee, 0, ',', '.') }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Status:</label>
                <span class="badge {{ $doctor->is_available_for_consultation ? 'badge-success' : 'badge-danger' }}">
                    {{ $doctor->is_available_for_consultation ? 'Available for Consultation' : 'Not Available' }}
                </span>
            </div>
        </div>

        <!-- Schedule Information -->
        <div class="card" style="background-color: #f9fafb; margin: 0;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Schedule</h3>
            
            @if(is_array($doctor->schedule) && count($doctor->schedule) > 0)
                <div class="schedule-list">
                    @foreach($doctor->schedule as $schedule)
                        <div style="border: 1px solid #e5e7eb; padding: 1rem; border-radius: 0.375rem; margin-bottom: 0.5rem;">
                            <div style="font-weight: 500; text-transform: capitalize;">{{ $schedule['day'] }}</div>
                            <div style="color: #6b7280;">
                                {{ \Carbon\Carbon::parse($schedule['start_time'])->format('H:i') }} - 
                                {{ \Carbon\Carbon::parse($schedule['end_time'])->format('H:i') }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: #6b7280;">No schedule set</p>
            @endif
        </div>

        <!-- Consultation Statistics -->
        <div class="card" style="background-color: #f9fafb; margin: 0;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Consultation Statistics</h3>
            
            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Total Consultations:</label>
                <p style="margin: 0;">{{ $doctor->consultations->count() }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Recent Consultations:</label>
                @if($doctor->consultations->isNotEmpty())
                    <div class="consultation-list">
                        @foreach($doctor->consultations->take(5) as $consultation)
                            <div style="border: 1px solid #e5e7eb; padding: 1rem; border-radius: 0.375rem; margin-bottom: 0.5rem;">
                                <div style="font-weight: 500;">{{ $consultation->pet->name }}</div>
                                <div style="color: #6b7280;">
                                    {{ $consultation->scheduled_date ? \Carbon\Carbon::parse($consultation->scheduled_date)->format('d M Y') : 'Not scheduled' }}
                                </div>
                                <div style="color: #6b7280;">
                                    Status: <span class="badge badge-{{ $consultation->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($consultation->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: #6b7280;">No consultations yet</p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.badge {
    padding: 0.25rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}
.badge-success {
    background-color: #10b981;
    color: white;
}
.badge-danger {
    background-color: #ef4444;
    color: white;
}
.badge-warning {
    background-color: #f59e0b;
    color: white;
}
</style>
@endsection 