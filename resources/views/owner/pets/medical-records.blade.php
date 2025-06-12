@extends('layouts.owner')

@section('title', 'Pet Medical Records')
@section('header', 'Pet Medical Records')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Pet Information -->
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-semibold mb-4">Pet Information</h2>
                    <dl class="grid grid-cols-1 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Name</dt>
                            <dd class="text-sm text-gray-900">{{ $pet->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Species</dt>
                            <dd class="text-sm text-gray-900">{{ $pet->species }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Breed</dt>
                            <dd class="text-sm text-gray-900">{{ $pet->breed }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Birth Date</dt>
                            <dd class="text-sm text-gray-900">{{ $pet->birth_date->format('M d, Y') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Medical Records -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold mb-4">Medical Records</h2>
            @if($pet->medicalRecords->isNotEmpty())
                <div class="space-y-4">
                    @foreach($pet->medicalRecords->sortByDesc('created_at') as $record)
                        <div class="border rounded-lg p-4">
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