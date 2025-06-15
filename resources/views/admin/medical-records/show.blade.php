@extends('layouts.admin')

@section('header', 'Medical Record Details')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div class="card-title" style="margin: 0;">Medical Record Details</div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('admin.medical-records.edit', $medicalRecord) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Record
            </a>
            <form action="{{ route('admin.medical-records.destroy', $medicalRecord) }}" 
                  method="POST" 
                  style="display: inline;"
                  onsubmit="return confirm('Are you sure you want to delete this record?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete Record
                </button>
            </form>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
        <!-- Basic Information -->
        <div class="card" style="background-color: #f9fafb; margin: 0;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Basic Information</h3>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Date</label>
                <p>{{ $medicalRecord->created_at->format('M d, Y H:i') }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Pet</label>
                <p>
                    <a href="{{ route('admin.pets.show', $medicalRecord->pet) }}" class="text-primary">
                        {{ $medicalRecord->pet->name }}
                    </a>
                </p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Owner</label>
                <p>{{ $medicalRecord->pet->owner->name }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Doctor</label>
                <p>{{ $medicalRecord->doctor->name }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Next Visit Date</label>
                <p>
                    @if($medicalRecord->next_visit_date)
                        {{ \Carbon\Carbon::parse($medicalRecord->next_visit_date)->format('M d, Y') }}
                    @else
                        -
                    @endif
                </p>
            </div>
        </div>

        <!-- Medical Information -->
        <div class="card" style="background-color: #f9fafb; margin: 0;">
            <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Medical Information</h3>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Diagnosis</label>
                <p style="white-space: pre-wrap;">{{ $medicalRecord->diagnosis }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Treatment</label>
                <p style="white-space: pre-wrap;">{{ $medicalRecord->treatment }}</p>
            </div>

            @if($medicalRecord->notes)
                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Additional Notes</label>
                    <p style="white-space: pre-wrap;">{{ $medicalRecord->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <div style="margin-top: 2rem;">
        <a href="{{ route('admin.medical-records.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>
@endsection 