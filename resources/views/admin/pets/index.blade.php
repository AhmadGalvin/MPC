@extends('layouts.admin')

@section('header', 'Manage Pets')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <div class="card-title" style="margin: 0;">All Pets</div>
        <a href="{{ route('admin.pets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Pet
        </a>
    </div>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Pet</th>
                    <th>Species</th>
                    <th>Breed</th>
                    <th>Age</th>
                    <th>Weight</th>
                    <th>Owner</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pets as $pet)
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center;">
                                @if($pet->photo)
                                    <img src="{{ Storage::url($pet->photo) }}" 
                                         alt="{{ $pet->name }}" 
                                         style="width: 2.5rem; height: 2.5rem; border-radius: 9999px; object-fit: cover;">
                                @endif
                                <span style="margin-left: 1rem;">{{ $pet->name }}</span>
                            </div>
                        </td>
                        <td>{{ $pet->species }}</td>
                        <td>{{ $pet->breed }}</td>
                        <td>{{ $pet->birth_date->age }} years</td>
                        <td>{{ $pet->weight }} kg</td>
                        <td>
                            <a href="{{ route('admin.owners.show', $pet->owner) }}" 
                               style="color: #4f46e5; text-decoration: none; hover:text-decoration: underline;">
                                {{ $pet->owner->name }}
                            </a>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
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
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: #6b7280;">No pets found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pets->hasPages())
        <div class="mt-4">
            {{ $pets->links() }}
        </div>
    @endif
</div>
@endsection 