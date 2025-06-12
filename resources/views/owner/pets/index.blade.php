@extends('layouts.owner')

@section('title', 'My Pets')
@section('header', 'My Pets')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">My Pets</h2>
                    <a href="{{ route('owner.pets.create') }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded">
                        Add New Pet
                    </a>
                </div>

                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @forelse($pets as $pet)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="relative h-48">
                                @if($pet->photo)
                                    <img src="{{ Storage::url($pet->photo) }}" alt="{{ $pet->name }}" class="w-full h-full object-cover">
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

                                <!-- Medical Records Summary -->
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500">
                                        <span class="font-medium">Medical Records:</span>
                                        {{ $pet->medicalRecords->count() }} records
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        <span class="font-medium">Last Consultation:</span>
                                        @if($pet->consultations->isNotEmpty())
                                            {{ $pet->consultations->sortByDesc('created_at')->first()->created_at->format('M d, Y') }}
                                        @else
                                            No consultations yet
                                        @endif
                                    </p>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a href="{{ route('owner.pets.show', $pet) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        View Details
                                    </a>
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
                                    <a href="{{ route('owner.appointments.create', ['pet' => $pet->id]) }}" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm">
                                        Book Consultation
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2">
                            <div class="text-center py-8 text-gray-500">
                                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-paw text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-lg font-medium">No pets registered yet</p>
                                <p class="mt-1">Add your first pet to get started</p>
                                <a href="{{ route('owner.pets.create') }}" class="mt-4 inline-block bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded">
                                    Add New Pet
                                </a>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if($pets->hasPages())
                    <div class="mt-6">
                        {{ $pets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 