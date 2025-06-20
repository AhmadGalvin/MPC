@extends('layouts.owner')

@section('title', 'Pet Details')
@section('header', 'Pet Details')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6 flex justify-between items-center">
                    <h2 class="text-2xl font-semibold">Pet Details</h2>
                    <a href="{{ route('owner.pets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Back to List
                    </a>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Pet Information -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <div class="relative h-64">
                                @if($pet->photo)
                                    <img src="{{ Storage::url($pet->photo) }}" alt="{{ $pet->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <span class="text-gray-500">No Photo</span>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="text-xl font-semibold mb-4">{{ $pet->name }}</h3>
                                <div class="text-gray-600 space-y-2">
                                    <p><span class="font-medium">Species:</span> {{ $pet->species ?? 'Not specified' }}</p>
                                    <p><span class="font-medium">Breed:</span> {{ $pet->breed ?? 'Not specified' }}</p>
                                    <p><span class="font-medium">Age:</span> {{ $pet->birth_date ? \Carbon\Carbon::parse($pet->birth_date)->age . ' years' : 'Not specified' }}</p>
                                    <p><span class="font-medium">Weight:</span> {{ $pet->weight ? $pet->weight . ' kg' : 'Not specified' }}</p>
                                    <p><span class="font-medium">Birth Date:</span> {{ $pet->birth_date ? \Carbon\Carbon::parse($pet->birth_date)->format('M d, Y') : 'Not specified' }}</p>
                                </div>
                                <div class="mt-4 flex space-x-2">
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
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Records -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h3 class="text-xl font-semibold mb-4">Medical Records</h3>
                            @if($pet->medicalRecords && $pet->medicalRecords->count() > 0)
                                <div class="space-y-4">
                                    @foreach($pet->medicalRecords->sortByDesc('created_at') as $record)
                                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <p class="text-sm text-gray-500">{{ $record->created_at->format('M d, Y') }}</p>
                                                    <p class="font-medium">By Dr. {{ $record->doctor->name }}</p>
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <h4 class="text-sm font-medium text-gray-700">Diagnosis</h4>
                                                <p class="text-sm text-gray-900">{{ $record->diagnosis }}</p>
                                            </div>
                                            <div class="mt-4">
                                                <h4 class="text-sm font-medium text-gray-700">Treatment</h4>
                                                <p class="text-sm text-gray-900">{{ $record->treatment }}</p>
                                            </div>
                                            @if($record->notes)
                                                <div class="mt-4">
                                                    <h4 class="text-sm font-medium text-gray-700">Additional Notes</h4>
                                                    <p class="text-sm text-gray-900">{{ $record->notes }}</p>
                                                </div>
                                            @endif
                                            @if($record->next_visit_date)
                                                <div class="mt-4">
                                                    <h4 class="text-sm font-medium text-gray-700">Next Visit Date</h4>
                                                    <p class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($record->next_visit_date)->format('M d, Y') }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-500">No medical records available.</p>
                            @endif
                        </div>

                        <!-- Recent Consultations -->
                        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                            <h3 class="text-xl font-semibold mb-4">Consultations</h3>
                            <div class="mt-4">
                                <a href="{{ route('owner.consultations.choose-doctor', ['pet' => $pet->id]) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded inline-block">
                                    Book New Consultation
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 