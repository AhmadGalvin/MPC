@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-semibold mb-6">Doctor Dashboard</h1>

                <!-- Quick Actions -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('doctor.consultations.index') }}" class="block p-6 bg-blue-50 rounded-lg hover:bg-blue-100">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-full mr-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold">My Appointments</h3>
                                    <p class="text-sm text-gray-600">View and manage consultations</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('doctor.medical-records.index') }}" class="block p-6 bg-green-50 rounded-lg hover:bg-green-100">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-100 rounded-full mr-4">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold">Medical Records</h3>
                                    <p class="text-sm text-gray-600">View patient histories</p>
                                </div>
                            </div>
                        </a>

                        <a href="{{ route('doctor.schedule') }}" class="block p-6 bg-purple-50 rounded-lg hover:bg-purple-100">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-100 rounded-full mr-4">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold">My Schedule</h3>
                                    <p class="text-sm text-gray-600">View and manage availability</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Today's Appointments -->
                <div class="mb-8">
                    <h2 class="text-lg font-semibold mb-4">Today's Appointments</h2>
                    <div class="bg-white rounded-lg shadow border">
                        <div class="divide-y">
                            @forelse($todayAppointments as $appointment)
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="mr-4">
                                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold">{{ $appointment->pet->name }}</h3>
                                                <p class="text-sm text-gray-600">Owner: {{ $appointment->pet->owner->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $appointment->scheduled_time->format('h:i A') }}</p>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="{{ route('doctor.consultations.show', $appointment) }}" class="text-blue-600 hover:text-blue-800">View Details →</a>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-gray-500 text-center">No appointments scheduled for today</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Recent Medical Records -->
                <div>
                    <h2 class="text-lg font-semibold mb-4">Recent Medical Records</h2>
                    <div class="bg-white rounded-lg shadow border">
                        <div class="divide-y">
                            @forelse($recentRecords as $record)
                                <div class="p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="font-semibold">{{ $record->pet->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ Str::limit($record->diagnosis, 100) }}</p>
                                            <p class="text-xs text-gray-500">{{ $record->created_at->format('M d, Y') }}</p>
                                        </div>
                                        <div>
                                            <a href="{{ route('doctor.medical-records.show', $record) }}" class="text-blue-600 hover:text-blue-800">View Record →</a>
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
    </div>
</div>
@endsection 