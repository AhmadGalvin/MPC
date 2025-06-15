@extends('layouts.admin')

@section('header', 'Appointments')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div class="card-title" style="margin: 0;">Appointments List</div>
        <a href="{{ route('admin.appointments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Schedule Appointment
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Pet</th>
                    <th>Owner</th>
                    <th>Doctor</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($appointments as $appointment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($appointment->scheduled_date)->format('M d, Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.pets.show', $appointment->pet) }}" class="text-primary">
                                {{ $appointment->pet->name }}
                            </a>
                        </td>
                        <td>{{ $appointment->pet->owner->name }}</td>
                        <td>{{ $appointment->doctor->name }}</td>
                        <td>{{ Str::limit($appointment->reason, 30) }}</td>
                        <td>
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
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('admin.appointments.show', $appointment) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                    <a href="{{ route('admin.appointments.edit', $appointment) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endif

                                @if($appointment->status === 'scheduled')
                                    <form action="{{ route('admin.appointments.confirm', $appointment) }}" 
                                          method="POST" 
                                          style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-primary btn-sm" title="Confirm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($appointment->status === 'confirmed')
                                    <form action="{{ route('admin.appointments.complete', $appointment) }}" 
                                          method="POST" 
                                          style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" title="Mark as Completed">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($appointment->status !== 'completed' && $appointment->status !== 'cancelled')
                                    <form action="{{ route('admin.appointments.cancel', $appointment) }}" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to cancel this appointment?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Cancel">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">No appointments found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1rem;">
        {{ $appointments->links() }}
    </div>
</div>
@endsection 