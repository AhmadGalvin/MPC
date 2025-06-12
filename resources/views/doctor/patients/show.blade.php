@extends('layouts.doctor')

@section('title', 'Patient Details')
@section('header', 'Patient Details')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Pet Details</h2>
            <a href="{{ route('doctor.patients.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                Back to List
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Pet Photo -->
                <div class="md:col-span-1">
                    @if($patient->photo)
                        <img src="{{ Storage::url($patient->photo) }}" alt="{{ $patient->name }}" class="w-full h-auto rounded-lg">
                    @else
                        <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-paw text-gray-400 text-6xl"></i>
                        </div>
                    @endif
                </div>

                <!-- Pet Information -->
                <div class="md:col-span-2">
                    <h3 class="text-2xl font-bold mb-4">{{ $patient->name }}</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Species:</p>
                            <p class="font-medium">{{ $patient->species }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Breed:</p>
                            <p class="font-medium">{{ $patient->breed }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Age:</p>
                            <p class="font-medium">{{ $patient->birth_date ? $patient->birth_date->diffInYears(now()) . ' years' : 'Not specified' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Weight:</p>
                            <p class="font-medium">{{ $patient->weight }} kg</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Birth Date:</p>
                            <p class="font-medium">{{ $patient->birth_date ? $patient->birth_date->format('M d, Y') : 'Not specified' }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-lg font-semibold mb-2">Owner Information</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-600">Name:</p>
                                <p class="font-medium">{{ $patient->owner->name }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Email:</p>
                                <p class="font-medium">{{ $patient->owner->email }}</p>
                            </div>
                            @if($patient->owner->phone)
                            <div>
                                <p class="text-gray-600">Phone:</p>
                                <p class="font-medium">{{ $patient->owner->phone }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Medical Records Form -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Add Medical Record</h2>
            
            <!-- Success Message -->
            <div id="successMessage" class="hidden mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">Medical record added successfully!</span>
            </div>

            <form id="medicalRecordForm" class="space-y-4">
                @csrf
                <input type="hidden" name="pet_id" value="{{ $patient->id }}">
                
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis</label>
                        <textarea name="diagnosis" id="diagnosis" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('diagnosis') }}</textarea>
                        <p class="mt-1 text-sm text-red-600 hidden" id="diagnosisError"></p>
                    </div>

                    <div>
                        <label for="treatment" class="block text-sm font-medium text-gray-700">Treatment</label>
                        <textarea name="treatment" id="treatment" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('treatment') }}</textarea>
                        <p class="mt-1 text-sm text-red-600 hidden" id="treatmentError"></p>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('notes') }}</textarea>
                        <p class="mt-1 text-sm text-red-600 hidden" id="notesError"></p>
                    </div>

                    <div>
                        <label for="next_visit_date" class="block text-sm font-medium text-gray-700">Next Visit Date</label>
                        <input type="date" name="next_visit_date" id="next_visit_date"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                               value="{{ old('next_visit_date') }}">
                        <p class="mt-1 text-sm text-red-600 hidden" id="nextVisitDateError"></p>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="bg-primary text-white px-4 py-2 rounded-md hover:bg-primary-dark">
                            Save Medical Record
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Medical History -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-xl font-semibold mb-4">Medical History</h2>
            @if($patient->medicalRecords->isNotEmpty())
                <div class="space-y-4">
                    @foreach($patient->medicalRecords->sortByDesc('created_at') as $record)
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
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('medicalRecordForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Reset error messages
    document.querySelectorAll('.text-red-600').forEach(el => el.classList.add('hidden'));
    document.getElementById('successMessage').classList.add('hidden');
    
    // Get form data
    const formData = new FormData(this);
    
    // Send AJAX request
    fetch('{{ route('doctor.medical-records.store') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success message
            document.getElementById('successMessage').classList.remove('hidden');
            
            // Clear form
            this.reset();
            
            // Refresh medical records section
            window.location.reload();
        } else {
            // Handle validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.getElementById(field + 'Error');
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.classList.remove('hidden');
                    }
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>
@endpush 