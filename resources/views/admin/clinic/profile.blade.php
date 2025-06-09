@extends('layouts.admin')

@section('title', 'Clinic Profile')
@section('header', 'Clinic Profile')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Clinic Profile Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-6">Edit Clinic Profile</h2>
        
        <form action="{{ route('admin.clinic.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Logo Preview -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Current Logo</label>
                <div class="w-32 h-32 bg-gray-100 rounded-lg overflow-hidden">
                    @if($clinic->logo_path)
                        <img src="{{ asset('storage/' . $clinic->logo_path) }}" alt="Clinic Logo" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <i class="fas fa-clinic-medical text-4xl"></i>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Logo Upload -->
            <div class="mb-6">
                <label for="logo" class="block text-gray-700 text-sm font-bold mb-2">Upload New Logo</label>
                <input type="file" name="logo" id="logo" accept="image/*" 
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error('logo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Clinic Name -->
            <div class="mb-6">
                <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Clinic Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $clinic->name) }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div class="mb-6">
                <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                <textarea name="address" id="address" rows="3" 
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('address', $clinic->address) }}</textarea>
                @error('address')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div class="mb-6">
                <label for="phone_number" class="block text-gray-700 text-sm font-bold mb-2">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $clinic->phone_number) }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('phone_number')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $clinic->email) }}" 
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea name="description" id="description" rows="4" 
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('description', $clinic->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Update History -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-6">Update History</h2>
        <div class="space-y-4" id="updateHistory">
            <!-- Update history will be loaded here -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Load update history
    $.get('/api/admin/clinic/history', function(response) {
        if (response.status === 'success') {
            const historyHtml = response.data.map(update => `
                <div class="border-l-4 border-primary pl-4 py-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold">${update.changes}</p>
                            <p class="text-sm text-gray-500">Updated by: ${update.user.name}</p>
                        </div>
                        <span class="text-sm text-gray-500">${new Date(update.created_at).toLocaleDateString()}</span>
                    </div>
                </div>
            `).join('') || '<p class="text-gray-500">No update history available.</p>';
            
            $('#updateHistory').html(historyHtml);
        }
    });
});
</script>
@endpush 