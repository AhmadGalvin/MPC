@extends('layouts.admin')

@section('header', 'Appointment Details')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div class="card-title" style="margin: 0;">Appointment Details</div>
        <div style="display: flex; gap: 1rem;">
            @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                <a href="{{ route('admin.appointments.edit', $appointment) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Edit Appointment
                </a>

                @if($appointment->status === 'scheduled')
                    <form action="{{ route('admin.appointments.confirm', $appointment) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Confirm Appointment
                        </button>
                    </form>
                @endif

                @if($appointment->status === 'confirmed')
                    <form action="{{ route('admin.appointments.complete', $appointment) }}" method="POST" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check-double"></i> Mark as Completed
                        </button>
                    </form>
                @endif

                <form action="{{ route('admin.appointments.cancel', $appointment) }}" 
                      method="POST" 
                      style="display: inline;"
                      onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Cancel Appointment
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <!-- Basic Information -->
        <div class="card" style="background-color: #f9fafb; margin: 0;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Basic Information</h3>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Status</label>
                <p>
                    @switch($appointment->status)
                        @case('scheduled')
                            <span class="badge bg-info">Scheduled</span>
                            @break
                        @case('confirmed')
                            <span class="badge bg-primary">Confirmed</span>
                            @break
                        @case('completed')
                            <span class="badge bg-success">Completed</span>
                            @break
                        @case('cancelled')
                            <span class="badge bg-danger">Cancelled</span>
                            @break
                        @default
                            <span class="badge bg-secondary">{{ ucfirst($appointment->status) }}</span>
                    @endswitch
                </p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Date & Time</label>
                <p>{{ \Carbon\Carbon::parse($appointment->scheduled_date)->format('M d, Y H:i') }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Pet</label>
                <p>
                    <a href="{{ route('admin.pets.show', $appointment->pet) }}" class="text-primary">
                        {{ $appointment->pet->name }}
                    </a>
                </p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Owner</label>
                <p>{{ $appointment->pet->owner->name }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Doctor</label>
                <p>{{ $appointment->doctor->name }}</p>
            </div>
        </div>

        <!-- Appointment Details -->
        <div class="card" style="background-color: #f9fafb; margin: 0;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Appointment Details</h3>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Reason for Visit</label>
                <p style="white-space: pre-wrap;">{{ $appointment->reason }}</p>
            </div>

            @if($appointment->notes)
                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Additional Notes</label>
                    <p style="white-space: pre-wrap;">{{ $appointment->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('admin.appointments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection 