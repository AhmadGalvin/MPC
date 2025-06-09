@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">My Pets</h2>
                    <a href="{{ route('owner.pets.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Add New Pet
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @forelse($pets as $pet)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="relative h-48">
                                @if($pet->photo)
                                    <img src="{{ asset('storage/' . $pet->photo) }}" alt="{{ $pet->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">No Photo</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-xl font-semibold mb-2">{{ $pet->name }}</h3>
                                <div class="text-gray-600 space-y-1">
                                    <p><span class="font-medium">Species:</span> {{ $pet->species }}</p>
                                    <p><span class="font-medium">Breed:</span> {{ $pet->breed }}</p>
                                    <p><span class="font-medium">Age:</span> {{ \Carbon\Carbon::parse($pet->birth_date)->age }} years</p>
                                    <p><span class="font-medium">Weight:</span> {{ $pet->weight }} kg</p>
                                </div>

                                <!-- Medical Records Section -->
                                <div class="mt-4">
                                    <h4 class="text-lg font-semibold mb-2">Medical Records</h4>
                                    @if($pet->medicalRecords->isNotEmpty())
                                        <div class="space-y-3">
                                            @foreach($pet->medicalRecords->sortByDesc('created_at') as $record)
                                                <div class="p-3 bg-gray-50 rounded border border-gray-200">
                                                    <div class="flex justify-between items-start">
                                                        <div>
                                                            <h5 class="font-medium">{{ $record->title }}</h5>
                                                            <p class="text-sm text-gray-600">{{ $record->description }}</p>
                                                        </div>
                                                        <p class="text-xs text-gray-500">{{ $record->created_at->format('M d, Y') }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-gray-500 text-sm">No medical records yet</p>
                                    @endif
                                </div>

                                <div class="mt-4 space-y-2">
                                    <p class="text-sm text-gray-500">
                                        <span class="font-medium">Last Consultation:</span>
                                        @if($pet->consultations->isNotEmpty())
                                            {{ $pet->consultations->sortByDesc('created_at')->first()->created_at->format('M d, Y') }}
                                        @else
                                            No consultations yet
                                        @endif
                                    </p>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('owner.pets.edit', $pet) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm">
                                            Edit
                                        </a>
                                        <form action="{{ route('owner.pets.destroy', $pet) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm" onclick="return confirm('Are you sure you want to delete this pet?')">
                                                Delete
                                            </button>
                                        </form>
                                        <a href="{{ route('owner.consultations.create', ['pet' => $pet->id]) }}" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                            Book Consultation
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <h3 class="text-lg font-medium text-gray-500">No pets registered yet</h3>
                            <p class="mt-2 text-gray-500">Click "Add New Pet" to register your first pet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 