@extends('layouts.admin')

@section('header', 'Manage Pet Owners')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div class="card-title" style="margin: 0;">All Pet Owners</div>
        <a href="{{ route('admin.owners.create-step-one') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Pet Owner
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Pets Count</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($owners as $owner)
                    <tr>
                        <td>{{ $owner->name }}</td>
                        <td>{{ $owner->email }}</td>
                        <td>{{ $owner->pets->count() }}</td>
                        <td>
                            <span class="badge {{ $owner->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $owner->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('admin.owners.show', $owner) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.owners.edit', $owner) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.owners.destroy', $owner) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this pet owner?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280;">No pet owners found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($owners->hasPages())
        <div class="mt-4">
            {{ $owners->links() }}
        </div>
    @endif
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
</style>
@endsection 