@extends('layouts.doctor')

@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<div class="py-6">
    <!-- Quick Actions -->
    

    <!-- Statistics -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-4">Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Today's Appointments</p>
                        <p class="text-2xl font-semibold">{{ $todayAppointments }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Patients</p>
                        <p class="text-2xl font-semibold">{{ $totalPatients }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full mr-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Medical Records</p>
                        <p class="text-2xl font-semibold">{{ $totalMedicalRecords }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full mr-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 2v4M8 2v4M3 10h18"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Consultations</p>
                        <p class="text-2xl font-semibold">{{ $totalConsultations }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Upcoming Appointments -->
        <div>
            <h2 class="text-lg font-semibold mb-4">Upcoming Appointments</h2>
            <div class="bg-white rounded-lg shadow border">
                <div class="divide-y">
                    @forelse($upcomingAppointments as $appointment)
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="mr-4">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                </div>
                                <div class="flex-grow">
                                    <p class="text-sm font-medium text-gray-900">{{ $appointment->pet->name }} ({{ $appointment->pet->owner->name }})</p>
                                    <p class="text-sm text-gray-600">{{ $appointment->date->format('M d, Y') }} at {{ $appointment->time->format('H:i A') }}</p>
                                </div>
                                <div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $appointment->status_color }}-100 text-{{ $appointment->status_color }}-800">
                                        {{ $appointment->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-gray-500 text-center">No upcoming appointments</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Medical Records -->
        <div>
            <h2 class="text-lg font-semibold mb-4">Recent Medical Records</h2>
            <div class="bg-white rounded-lg shadow border">
                <div class="divide-y">
                    @forelse($recentMedicalRecords as $record)
                        <div class="p-4">
                            <div class="flex items-center">
                                <div class="mr-4">
                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $record->pet->name }}</p>
                                    <p class="text-sm text-gray-600">{{ Str::limit($record->diagnosis, 50) }}</p>
                                    <p class="text-xs text-gray-500">{{ $record->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-gray-500 text-center">No recent medical records</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 