@extends('layouts.owner')

@section('title', 'Consultation Details')
@section('header', 'Consultation Details')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Consultation Status -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2">Status</h3>
                    <div class="flex space-x-4">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($consultation->status === 'confirmed') bg-green-100 text-green-800
                            @elseif($consultation->status === 'cancelled') bg-red-100 text-red-800
                            @elseif($consultation->status === 'completed') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($consultation->status) }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold
                            @if($consultation->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($consultation->payment_status === 'failed') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            Payment: {{ ucfirst($consultation->payment_status) }}
                        </span>
                    </div>
                </div>

                <!-- Payment Section -->
                @if($consultation->status === 'pending' && $consultation->payment_status !== 'paid')
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Payment Required</h3>
                        <p class="text-gray-600 mb-4">
                            Please complete the payment to confirm your consultation with Dr. {{ $consultation->doctor->name }}.
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold">
                                Rp {{ number_format($consultation->fee, 0, ',', '.') }}
                            </span>
                            <a href="{{ route('payments.process', $consultation) }}" 
                               class="bg-primary text-white px-6 py-2 rounded-md hover:bg-primary-dark">
                                Pay Now
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Consultation Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Consultation Info -->
                    <div>
                        <h3 class="text-lg font-semibold mb-4">Consultation Information</h3>
                        <dl class="grid grid-cols-1 gap-4">
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