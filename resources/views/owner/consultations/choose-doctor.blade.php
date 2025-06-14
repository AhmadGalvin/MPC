@extends('layouts.owner')

@section('title', 'Choose Doctor')
@section('header', 'Choose Doctor for Consultation')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Available Doctors</h2>
            <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                Back
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($doctors as $doctor)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white">
                            <i class="fas fa-user-md text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold">Dr. {{ $doctor->name }}</h3>
                            <p class="text-gray-600">{{ $doctor->specialization }}</p>
                        </div>
                    </div>

                    <div class="space-y-2 mb-4">
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-id-card mr-2"></i>
                            <span>SIP: {{ $doctor->sip_number }}</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <i class="fas fa-envelope mr-2"></i>
                            <span>{{ $doctor->email }}</span>
                        </div>
                        <div class="flex items-center text-primary font-semibold">
                            <i class="fas fa-money-bill-wave mr-2"></i>
                            <span>Consultation Fee: Rp {{ number_format($doctor->consultation_fee, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="mt-4">
                        @php
                            $activeConsultation = \App\Models\Consultation::where('doctor_id', $doctor->doctor_id)
                                ->where('owner_id', auth()->id())
                                ->where('payment_status', 'paid')
                                ->where('status', 'confirmed')
                                ->first();
                        @endphp

                        @if($activeConsultation)
                            <a href="{{ route('owner.chat.show', $activeConsultation) }}" 
                               class="block w-full bg-green-500 text-white text-center px-4 py-2 rounded-md hover:bg-green-600">
                                Chat with Doctor
                            </a>
                        @else
                            <form action="{{ route('owner.consultations.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="doctor_id" value="{{ $doctor->doctor_id }}">
                                <input type="hidden" name="pet_id" value="{{ request('pet') }}">
                                <input type="hidden" name="fee" value="{{ $doctor->consultation_fee }}">
                                <button type="submit" class="block w-full bg-primary text-white text-center px-4 py-2 rounded-md hover:bg-primary-dark">
                                    Book Consultation
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3">
                    <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                        <p class="text-gray-500">No doctors available for consultation at the moment.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $doctors->links() }}
        </div>
    </div>
</div>
@endsection 