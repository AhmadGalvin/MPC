@extends('layouts.admin')

@section('title', 'Pet Details')
@section('header', 'Pet Details')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div class="card-title" style="margin: 0;">Pet Information</div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('admin.pets.edit', $pet) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Pet
            </a>
            <form action="{{ route('admin.pets.destroy', $pet) }}" 
                  method="POST" 
                  style="display: inline;"
                  onsubmit="return confirm('Are you sure you want to delete this pet?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete Pet
                </button>
            </form>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <!-- Basic Information -->
        <div class="card" style="background-color: #f9fafb; margin: 0;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Basic Information</h3>
            
            <div style="margin-bottom: 1.5rem;">
                @if($pet->photo)
                    <img src="{{ Storage::url($pet->photo) }}" 
                         alt="{{ $pet->name }}" 
                         style="width: 150px; height: 150px; object-fit: cover; border-radius: 0.375rem; margin-bottom: 1rem;">
                @endif
            </div>

            <div style="display: grid; gap: 1rem;">
                <div>
                    <label style="font-weight: 500; color: #6b7280;">Name</label>
                    <p style="margin-top: 0.25rem;">{{ $pet->name }}</p>
                </div>

                <div>
                    <label style="font-weight: 500; color: #6b7280;">Species</label>
                    <p style="margin-top: 0.25rem;">{{ $pet->species }}</p>
                </div>

                <div>
                    <label style="font-weight: 500; color: #6b7280;">Breed</label>
                    <p style="margin-top: 0.25rem;">{{ $pet->breed }}</p>
                </div>

                <div>
                    <label style="font-weight: 500; color: #6b7280;">Owner</label>
                    <p style="margin-top: 0.25rem;">
                        <a href="{{ route('admin.owners.show', $pet->owner) }}" 
                           style="color: #4f46e5; text-decoration: none; hover:text-decoration: underline;">
                            {{ $pet->owner->name }}
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="card" style="background-color: #f9fafb; margin: 0;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Additional Information</h3>

            <div style="display: grid; gap: 1rem;">
                <div>
                    <label style="font-weight: 500; color: #6b7280;">Birth Date</label>
                    <p style="margin-top: 0.25rem;">{{ $pet->birth_date->format('F j, Y') }}</p>
                </div>

                <div>
                    <label style="font-weight: 500; color: #6b7280;">Age</label>
                    <p style="margin-top: 0.25rem;">{{ $pet->birth_date->age }} years old</p>
                </div>

                <div>
                    <label style="font-weight: 500; color: #6b7280;">Weight</label>
                    <p style="margin-top: 0.25rem;">{{ $pet->weight }} kg</p>
                </div>

                <div>
                    <label style="font-weight: 500; color: #6b7280;">Medical Records</label>
                    <p style="margin-top: 0.25rem;">{{ $pet->medicalRecords->count() }} records</p>
                </div>

                <div>
                    <label style="font-weight: 500; color: #6b7280;">Appointments</label>
                    <p style="margin-top: 0.25rem;">{{ $pet->appointments->count() }} appointments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical Records -->
    <div style="margin-top: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0;">Medical Records</h3>
            <a href="{{ route('admin.medical-records.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Record
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Doctor</th>
                        <th>Diagnosis</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pet->medicalRecords as $record)
                        <tr>
                            <td>{{ $record->created_at->format('F j, Y') }}</td>
                            <td>{{ $record->doctor->name }}</td>
                            <td>{{ $record->diagnosis}}</td>
                            <td>{{ Str::limit($record->notes, 50) }}</td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.medical-records.show', $record) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.medical-records.edit', $record) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.medical-records.destroy', $record) }}" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #6b7280;">No medical records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Appointments -->
    <div style="margin-top: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0;">Appointments</h3>
            <a href="{{ route('admin.appointments.create', ['pet_id' => $pet->id]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Appointment
            </a>
        </div>

        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Doctor</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pet->appointments as $appointment)
                        <tr>
                            <td>{{ $appointment->scheduled_date->format('F j, Y') }}</td>
                            <td>{{ $appointment->scheduled_time->format('g:i A') }}</td>
                            <td>{{ $appointment->doctor->name }}</td>
                            <td>
                                <span class="badge badge-{{ $appointment->status }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('admin.appointments.show', $appointment) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.appointments.edit', $appointment) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.appointments.destroy', $appointment) }}" 
                                          method="POST" 
                                          style="display: inline;"
                                          onsubmit="return confirm('Are you sure you want to delete this appointment?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: #6b7280;">No appointments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 