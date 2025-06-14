@extends('layouts.owner')

@section('title', 'Payment Successful')
@section('header', 'Payment Successful')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <!-- Success Message -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-500 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Payment Successful!</h2>
                    <p class="text-gray-600 mt-2">Your consultation has been confirmed</p>
                </div>

                <!-- Payment Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold mb-3">Payment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Transaction ID</p>
                            <p class="font-medium">{{ $consultation->transaction_id }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Amount Paid</p>
                            <p class="font-medium">Rp {{ number_format($consultation->fee, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Payment Method</p>
                            <p class="font-medium">QRIS</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Payment Date</p>
                            <p class="font-medium">{{ $consultation->paid_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Consultation Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold mb-3">Consultation Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Doctor</p>
                            <p class="font-medium">Dr. {{ $consultation->doctor->name }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Specialization</p>
                            <p class="font-medium">{{ $consultation->doctor->specialization }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Date</p>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($consultation->scheduled_date)->format('l, M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Time</p>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($consultation->scheduled_time)->format('h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-blue-800 mb-2">Next Steps:</h4>
                    <ul class="list-disc list-inside text-blue-800 space-y-2">
                        <li>Your consultation schedule has been confirmed</li>
                        <li>Please be online 5 minutes before the consultation time</li>
                        <li>Make sure you have a stable internet connection</li>
                        <li>Prepare any relevant medical records or questions</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('owner.consultations.show', $consultation) }}" 
                       class="bg-primary text-white px-6 py-2 rounded-md hover:bg-primary-dark">
                        View Consultation Details
                    </a>
                    <a href="{{ route('owner.consultations.index') }}" 
                       class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600">
                        Back to Consultations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 