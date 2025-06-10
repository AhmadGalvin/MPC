@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold">Consultation Details</h2>
                    <a href="{{ route('owner.consultations.index') }}" class="text-blue-600 hover:text-blue-800">
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

                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Consultation Info -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Consultation Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($consultation->status === 'confirmed') bg-green-100 text-green-800
                                            @elseif($consultation->status === 'cancelled') bg-red-100 text-red-800
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
                                        <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                        <dd class="text-sm text-gray-900">{{ $consultation->notes }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        <!-- Pet Info -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Pet Information</h3>
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
                                        <dt class="text-sm font-medium text-gray-500">Name</dt>
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
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Age</dt>
                                        <dd class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($consultation->pet->birth_date)->age }} years old</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Doctor Info -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-4">Doctor Information</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="text-sm text-gray-900">Dr. {{ $consultation->doctor->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Specialization</dt>
                            <dd class="text-sm text-gray-900">{{ $consultation->doctor->specialization ?? 'General Practice' }}</dd>
                        </div>
                    </dl>
                </div>

                @if($consultation->status === 'pending')
                    <div class="mt-6 flex justify-end">
                        <form action="{{ route('owner.consultations.destroy', $consultation) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium" onclick="return confirm('Are you sure you want to cancel this consultation?')">
                                Cancel Consultation
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 