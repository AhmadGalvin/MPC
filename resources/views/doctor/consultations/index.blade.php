@extends('layouts.doctor')

@section('title', 'Consultations')
@section('header', 'Consultations')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Status Toggle Section -->
                <div class="mb-6 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-semibold">Consultation Status</h2>
                        <p class="text-gray-600 mt-1">
                            You are currently 
                            <span class="font-medium {{ $doctor->is_available_for_consultation ? 'text-green-600' : 'text-red-600' }}">
                                {{ $doctor->is_available_for_consultation ? 'accepting' : 'not accepting' }}
                            </span> 
                            consultations
                        </p>
                    </div>
                    <form action="{{ route('doctor.consultations.toggle-status') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="px-4 py-2 rounded-md {{ $doctor->is_available_for_consultation 
                                    ? 'bg-red-500 hover:bg-red-600' 
                                    : 'bg-green-500 hover:bg-green-600' }} text-white">
                            {{ $doctor->is_available_for_consultation ? 'Close Session' : 'Open Session' }}
                        </button>
                    </form>
                </div>

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Consultations List -->
                <div class="mt-6">
                    <h3 class="text-xl font-semibold mb-4">Scheduled Consultations</h3>
                    
                    @if($consultations->isEmpty())
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <p class="text-gray-500">No consultations scheduled at the moment.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date & Time
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pet
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Owner
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($consultations as $consultation)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ \Carbon\Carbon::parse($consultation->scheduled_date)->format('M d, Y') }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ \Carbon\Carbon::parse($consultation->scheduled_time)->format('h:i A') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $consultation->pet->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $consultation->pet->species }} - {{ $consultation->pet->breed }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $consultation->pet->owner->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $consultation->pet->owner->email }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $consultation->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                       ($consultation->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                                       'bg-yellow-100 text-yellow-800') }}">
                                                    {{ ucfirst($consultation->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="#" class="text-blue-600 hover:text-blue-900 mr-3">View Details</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $consultations->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 