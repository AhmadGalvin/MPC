@extends('layouts.admin')

@section('header', 'Add New Doctor - Step 1')

@section('content')
<div class="card">
    <div class="card-title">Create Doctor Account</div>
    <p style="margin-bottom: 1.5rem; color: #6b7280;">Step 1 of 2 - Create User Account</p>

    <form action="{{ route('admin.doctors.store.step.one') }}" method="POST">
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

        <div style="margin-bottom: 1.5rem;">
            <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Confirm Password</label>
            <input type="password" 
                   name="password_confirmation" 
                   required
                   style="width: 100%; padding: 0.5rem; border: 1px solid #d1d5db; border-radius: 0.375rem;">
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.doctors.index') }}" 
               class="btn btn-primary" 
               style="background-color: #6b7280;">
                Cancel
            </a>
            <button type="submit" class="btn btn-primary">
                Next <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

<style>
.card {
    max-width: 32rem;
    margin: 0 auto;
}
</style>
@endsection 