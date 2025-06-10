@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Consultation Details</h2>
                    <a href="{{ route('doctor.consultations.index') }}" class="text-blue-600 hover:text-blue-800">
                        &larr; Back to Consultations
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Consultation Info -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Consultation Information</h3>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($consultation->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($consultation->status === 'cancelled') bg-red-100 text-red-800
                                        @elseif($consultation->status === 'completed') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($consultation->status) }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date</dt>
                                <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($consultation->scheduled_date)->format('l, M d, Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Time</dt>
                                <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($consultation->scheduled_time)->format('h:i A') }}</dd>
                            </div>
                            @if($consultation->notes)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Notes from Owner</dt>
                                    <dd class="text-sm text-gray-900">{{ $consultation->notes }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <!-- Pet & Owner Info -->
                    <div class="bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Pet & Owner Information</h3>
                        <div class="flex items-start space-x-4">
                            @if($consultation->pet->photo)
                                <img src="{{ asset('storage/' . $consultation->pet->photo) }}" alt="{{ $consultation->pet->name }}" class="w-24 h-24 rounded-lg object-cover">
                            @else
                                <div class="w-24 h-24 rounded-lg bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">No Photo</span>
                                </div>
                            @endif
                            <dl class="flex-1">
                                <div class="mb-2">
                                    <dt class="text-sm font-medium text-gray-500">Pet Name</dt>
                                    <dd class="text-sm text-gray-900">{{ $consultation->pet->name }}</dd>
                                </div>
                                <div class="mb-2">
                                    <dt class="text-sm font-medium text-gray-500">Species</dt>
                                    <dd class="text-sm text-gray-900">{{ $consultation->pet->species }}</dd>
                                </div>
                                <div class="mb-2">
                                    <dt class="text-sm font-medium text-gray-500">Breed</dt>
                                    <dd class="text-sm text-gray-900">{{ $consultation->pet->breed }}</dd>
                                </div>
                                <div class="mb-2">
                                    <dt class="text-sm font-medium text-gray-500">Age</dt>
                                    <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($consultation->pet->birth_date)->age }} years old</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Owner</dt>
                                    <dd class="text-sm text-gray-900">{{ $consultation->pet->owner->name }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                @if($consultation->status === 'confirmed')
                    <!-- Medical Record Form -->
                    <div class="mt-6 bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Complete Consultation</h3>
                        <form action="{{ route('doctor.consultations.update-status', $consultation) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="completed">

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label for="diagnosis" class="block text-sm font-medium text-gray-700">Diagnosis</label>
                                    <textarea id="diagnosis" name="diagnosis" rows="3" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="Enter diagnosis details">{{ old('diagnosis') }}</textarea>
                                </div>

                                <div>
                                    <label for="treatment" class="block text-sm font-medium text-gray-700">Treatment</label>
                                    <textarea id="treatment" name="treatment" rows="3" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="Enter treatment details">{{ old('treatment') }}</textarea>
                                </div>

                                <div>
                                    <label for="prescription" class="block text-sm font-medium text-gray-700">Prescription (Optional)</label>
                                    <textarea id="prescription" name="prescription" rows="3"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                        placeholder="Enter prescription details">{{ old('prescription') }}</textarea>
                                </div>

                                <div>
                                    <label for="fee" class="block text-sm font-medium text-gray-700">Consultation Fee</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">Rp</span>
                                        </div>
                                        <input type="number" name="fee" id="fee" required
                                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md"
                                            placeholder="0.00"
                                            value="{{ old('fee') }}">
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3">
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Complete Consultation
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @elseif($consultation->status === 'completed')
                    <!-- Medical Record Display -->
                    <div class="mt-6 bg-gray-50 rounded-lg p-6">
                        <h3 class="text-lg font-semibold mb-4">Medical Record</h3>
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Diagnosis</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $consultation->diagnosis }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Treatment</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $consultation->treatment }}</dd>
                            </div>
                            @if($consultation->prescription)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Prescription</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $consultation->prescription }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Consultation Fee</dt>
                                <dd class="mt-1 text-sm text-gray-900">Rp {{ number_format($consultation->fee, 0, ',', '.') }}</dd>
                            </div>
                        </dl>
                    </div>
                @elseif($consultation->status === 'pending')
                    <div class="mt-6 flex justify-end">
                        <form action="{{ route('doctor.consultations.update-status', $consultation) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="confirmed">
                            <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Confirm Consultation
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 