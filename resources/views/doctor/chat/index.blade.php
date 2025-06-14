@extends('layouts.doctor')

@section('title', 'Chat Consultations')
@section('header', 'Chat Consultations')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h2 class="text-xl font-semibold mb-4">Active Consultations</h2>

                <div class="space-y-4">
                    @forelse($consultations as $consultation)
                        <div class="border rounded-lg p-4 hover:bg-gray-50">
                            <a href="{{ route('doctor.chat.show', $consultation) }}" class="block">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white">
                                            <i class="fas fa-user text-xl"></i>
                                        </div>
                                        <div class="ml-4">
                                            <h3 class="font-semibold">{{ $consultation->owner->name }}</h3>
                                            <p class="text-sm text-gray-600">Pet: {{ $consultation->pet->name }} ({{ $consultation->pet->species }})</p>
                                            <p class="text-xs text-gray-500">Started: {{ $consultation->created_at->format('d M Y H:i') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-primary">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <p>No active consultations at the moment.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $consultations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 