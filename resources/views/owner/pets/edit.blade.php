@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold">Edit Pet: {{ $pet->name }}</h2>
                </div>

                <form action="{{ route('owner.pets.update', $pet) }}" method="POST" enctype="multipart/form-data" class="max-w-2xl">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Pet Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $pet->name) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Species -->
                        <div>
                            <label for="species" class="block text-sm font-medium text-gray-700">Species</label>
                            <input type="text" name="species" id="species" value="{{ old('species', $pet->species) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('species')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Breed -->
                        <div>
                            <label for="breed" class="block text-sm font-medium text-gray-700">Breed</label>
                            <input type="text" name="breed" id="breed" value="{{ old('breed', $pet->breed) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('breed')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Birth Date -->
                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-gray-700">Birth Date</label>
                            <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', $pet->birth_date->format('Y-m-d')) }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('birth_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Weight -->
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                            <input type="number" name="weight" id="weight" value="{{ old('weight', $pet->weight) }}" step="0.1" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            @error('weight')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Photo -->
                        @if($pet->photo)
                            <div class="col-span-full">
                                <label class="block text-sm font-medium text-gray-700">Current Photo</label>
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $pet->photo) }}" alt="{{ $pet->name }}" class="w-32 h-32 object-cover rounded-lg">
                                </div>
                            </div>
                        @endif

                        <!-- New Photo -->
                        <div class="col-span-full">
                            <label for="photo" class="block text-sm font-medium text-gray-700">New Photo</label>
                            <input type="file" name="photo" id="photo" accept="image/*"
                                class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-sm text-gray-500">Upload a new photo of your pet (optional)</p>
                            @error('photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('owner.pets.index') }}" class="bg-gray-200 py-2 px-4 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 py-2 px-4 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Update Pet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 