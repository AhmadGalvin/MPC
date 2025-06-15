@extends('layouts.admin')

@section('header', 'Edit Pet')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div class="card-title" style="margin: 0;">Edit Pet Information</div>
    </div>

    <form action="{{ route('admin.pets.update', $pet) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <!-- Basic Information -->
            <div class="card" style="background-color: #f9fafb; margin: 0;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Basic Information</h3>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Owner</label>
                    <select name="owner_id" required style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                        <option value="">Select Owner</option>
                        @foreach($owners as $owner)
                            <option value="{{ $owner->id }}" {{ old('owner_id', $pet->owner_id) == $owner->id ? 'selected' : '' }}>
                                {{ $owner->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('owner_id')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Name</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $pet->name) }}" 
                           required
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    @error('name')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Species</label>
                    <input type="text" 
                           name="species" 
                           value="{{ old('species', $pet->species) }}" 
                           required
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    @error('species')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Breed</label>
                    <input type="text" 
                           name="breed" 
                           value="{{ old('breed', $pet->breed) }}" 
                           required
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    @error('breed')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card" style="background-color: #f9fafb; margin: 0;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0 0 1rem;">Additional Information</h3>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Birth Date</label>
                    <input type="date" 
                           name="birth_date" 
                           value="{{ old('birth_date', $pet->birth_date->format('Y-m-d')) }}" 
                           required
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    @error('birth_date')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Weight (kg)</label>
                    <input type="number" 
                           name="weight" 
                           value="{{ old('weight', $pet->weight) }}" 
                           step="0.1"
                           required
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    @error('weight')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-bottom: 1rem;">
                    <label style="font-weight: 500; display: block; margin-bottom: 0.5rem;">Photo</label>
                    @if($pet->photo)
                        <div style="margin-bottom: 1rem;">
                            <img src="{{ Storage::url($pet->photo) }}" 
                                 alt="{{ $pet->name }}" 
                                 style="width: 100px; height: 100px; object-fit: cover; border-radius: 0.375rem;">
                        </div>
                    @endif
                    <input type="file" 
                           name="photo" 
                           accept="image/*"
                           style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
                    @error('photo')
                        <p style="color: #dc2626; font-size: 0.875rem; margin-top: 0.5rem;">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.pets.show', $pet) }}" class="btn btn-secondary">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                Update Pet
            </button>
        </div>
    </form>
</div>
@endsection 