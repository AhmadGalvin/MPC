@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-semibold mb-6">Book a Consultation</h1>

                <form action="{{ route('consultations.store') }}" method="POST" class="max-w-lg">
                    @csrf

                    <div class="mb-4">
                        <label for="pet_id" class="block text-gray-700 text-sm font-bold mb-2">Select Pet</label>
                        <select name="pet_id" id="pet_id" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('pet_id') border-red-500 @enderror">
                            <option value="">Choose a pet</option>
                            @foreach($pets as $pet)
                                <option value="{{ $pet->id }}" {{ old('pet_id') == $pet->id ? 'selected' : '' }}>
                                    {{ $pet->name }} ({{ $pet->species }})
                                </option>
                            @endforeach
                        </select>
                        @error('pet_id')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="doctor_id" class="block text-gray-700 text-sm font-bold mb-2">Select Doctor</label>
                        <select name="doctor_id" id="doctor_id" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('doctor_id') border-red-500 @enderror">
                            <option value="">Choose a doctor</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    Dr. {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="scheduled_date" class="block text-gray-700 text-sm font-bold mb-2">Date</label>
                        <input type="date" name="scheduled_date" id="scheduled_date" value="{{ old('scheduled_date') }}" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('scheduled_date') border-red-500 @enderror">
                        @error('scheduled_date')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="scheduled_time" class="block text-gray-700 text-sm font-bold mb-2">Time</label>
                        <input type="time" name="scheduled_time" id="scheduled_time" value="{{ old('scheduled_time') }}" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('scheduled_time') border-red-500 @enderror">
                        @error('scheduled_time')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="reason" class="block text-gray-700 text-sm font-bold mb-2">Reason for Visit</label>
                        <textarea name="reason" id="reason" rows="4" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('reason') border-red-500 @enderror">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            Book Consultation
                        </button>
                        <a href="{{ route('dashboard') }}"
                            class="text-gray-600 hover:text-gray-800">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 