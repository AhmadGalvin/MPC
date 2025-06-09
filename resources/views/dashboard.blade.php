@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-semibold mb-6">Dashboard</h1>
                
                <!-- Debug Info -->
                @if(auth()->check())
                    <div class="mb-4 p-4 bg-gray-100 rounded">
                        <p>Logged in as: {{ auth()->user()->name }}</p>
                        <p>Role: {{ auth()->user()->role }}</p>
                    </div>
                @endif

                <!-- Owner Section -->
                @if(auth()->check() && auth()->user()->hasRole('owner'))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- My Pets Card -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-xl font-semibold mb-4">My Pets</h2>
                            @if(isset($pets) && $pets->count() > 0)
                                <div class="space-y-4">
                                    @foreach($pets as $pet)
                                        <div class="p-4 border rounded-lg">
                                            <div class="flex items-center">
                                                @if($pet->photo)
                                                    <img src="{{ Storage::url($pet->photo) }}" alt="{{ $pet->name }}" class="w-16 h-16 rounded-full object-cover mr-4">
                                                @else
                                                    <div class="w-16 h-16 rounded-full bg-gray-200 mr-4 flex items-center justify-center">
                                                        <span class="text-gray-500">No Photo</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h3 class="font-semibold">{{ $pet->name }}</h3>
                                                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($pet->birth_date)->age }} years old</p>
                                                    <p class="text-sm text-gray-500">{{ number_format($pet->weight, 2) }} kg</p>
                                                </div>
                                            </div>
                                            <div class="mt-2 flex space-x-2">
                                                <a href="{{ route('owner.pets.edit', $pet) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                                                <a href="{{ route('owner.pets.show', $pet) }}" class="text-green-500 hover:text-green-700">View</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600">No pets registered yet.</p>
                            @endif
                            <a href="{{ route('owner.pets.create') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Add New Pet
                            </a>
                        </div>

                        <!-- Recent Consultations Card -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-xl font-semibold mb-4">Recent Consultations</h2>
                            @if(isset($consultations) && $consultations->count() > 0)
                                <div class="space-y-4">
                                    @foreach($consultations as $consultation)
                                        <div class="p-4 border rounded-lg">
                                            <h3 class="font-semibold">{{ $consultation->pet->name }}</h3>
                                            <p class="text-gray-600">with Dr. {{ $consultation->doctor->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $consultation->scheduled_date->format('M d, Y') }}</p>
                                            <p class="text-sm text-gray-500">{{ $consultation->scheduled_time->format('h:i A') }}</p>
                                            <div class="mt-2">
                                                <a href="{{ route('owner.consultations.show', $consultation) }}" class="text-blue-500 hover:text-blue-700">View Details</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600">No recent consultations.</p>
                            @endif
                            <a href="{{ route('owner.appointments.create') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Book Consultation
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Doctor Section -->
                @if(auth()->check() && auth()->user()->hasRole('doctor'))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Today's Appointments -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-xl font-semibold mb-4">Today's Appointments</h2>
                            @if(isset($todayAppointments) && $todayAppointments->count() > 0)
                                <div class="space-y-4">
                                    @foreach($todayAppointments as $appointment)
                                        <div class="p-4 border rounded-lg">
                                            <h3 class="font-semibold">{{ $appointment->pet->name }}</h3>
                                            <p class="text-gray-600">Owner: {{ $appointment->pet->owner->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $appointment->scheduled_time->format('h:i A') }}</p>
                                            <div class="mt-2">
                                                <a href="{{ route('doctor.consultations.show', $appointment) }}" class="text-blue-500 hover:text-blue-700">View Details</a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600">No appointments for today.</p>
                            @endif
                        </div>

                        <!-- Recent Medical Records -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-xl font-semibold mb-4">Recent Medical Records</h2>
                            @if(isset($recentRecords) && $recentRecords->count() > 0)
                                <div class="space-y-4">
                                    @foreach($recentRecords as $record)
                                        <div class="p-4 border rounded-lg">
                                            <h3 class="font-semibold">{{ $record->pet->name }}</h3>
                                            <p class="text-gray-600">{{ Str::limit($record->diagnosis, 50) }}</p>
                                            <p class="text-sm text-gray-500">{{ $record->created_at->format('M d, Y') }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600">No recent medical records.</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Clinic Admin Section -->
                @if(auth()->check() && auth()->user()->hasRole('clinic_admin'))
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Doctors List -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-xl font-semibold mb-4">Doctors</h2>
                            @if(isset($doctors) && $doctors->count() > 0)
                                <div class="space-y-4">
                                    @foreach($doctors as $doctor)
                                        <div class="p-4 border rounded-lg">
                                            <h3 class="font-semibold">Dr. {{ $doctor->name }}</h3>
                                            <p class="text-gray-600">{{ $doctor->email }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600">No doctors registered yet.</p>
                            @endif
                        </div>

                        <!-- Products List -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-xl font-semibold mb-4">Products</h2>
                            @if(isset($products) && $products->count() > 0)
                                <div class="space-y-4">
                                    @foreach($products as $product)
                                        <div class="p-4 border rounded-lg">
                                            <h3 class="font-semibold">{{ $product->name }}</h3>
                                            <p class="text-gray-600">Stock: {{ $product->stock }}</p>
                                            <p class="text-gray-600">Price: ${{ number_format($product->price, 2) }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600">No products available.</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 