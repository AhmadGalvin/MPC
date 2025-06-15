@extends('layouts.admin')

@section('header', 'Pet Owner Details')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div class="card-title" style="margin: 0;">Pet Owner Information</div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('admin.owners.edit', $owner) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Pet Owner
            </a>
            <form action="{{ route('admin.owners.destroy', $owner) }}" 
                  method="POST" 
                  style="display: inline;"
                  onsubmit="return confirm('Are you sure you want to delete this pet owner?');">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="btn btn-primary" 
                        style="background-color: #dc2626;">
                    <i class="fas fa-trash"></i> Delete Pet Owner
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
                <p style="margin: 0;">{{ $owner->name }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Email:</label>
                <p style="margin: 0;">{{ $owner->email }}</p>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Status:</label>
                <span class="badge {{ $owner->is_active ? 'badge-success' : 'badge-danger' }}">
                    {{ $owner->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="font-weight: 500; display: block; margin-bottom: 0.25rem;">Registered On:</label>
                <p style="margin: 0;">{{ $owner->created_at->format('F j, Y') }}</p>
            </div>
        </div>

        <!-- Pets Information -->
        <div class="card" style="background-color: #f9fafb; margin: 0;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0;">Registered Pets</h3>
                <a href="{{ route('admin.pets.create') }}?owner_id={{ $owner->id }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Pet
                </a>
            </div>
            
            @if($owner->pets->count() > 0)
                <div class="table-responsive">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">Name</th>
                                <th style="text-align: left; padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">Species</th>
                                <th style="text-align: left; padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">Breed</th>
                                <th style="text-align: right; padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($owner->pets as $pet)
                                <tr>
                                    <td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">{{ $pet->name }}</td>
                                    <td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">{{ $pet->species }}</td>
                                    <td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">{{ $pet->breed }}</td>
                                    <td style="padding: 0.75rem; border-bottom: 1px solid #e5e7eb;">
                                        <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                                            <a href="{{ route('admin.pets.show', $pet) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.pets.edit', $pet) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.pets.destroy', $pet) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this pet?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="color: #6b7280;">No pets registered</p>
            @endif
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
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
</style>
@endsection 