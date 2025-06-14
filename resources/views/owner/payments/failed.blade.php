@extends('layouts.owner')

@section('title', 'Payment Failed')
@section('header', 'Payment Failed')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <!-- Failed Message -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-times-circle text-red-500 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Payment Failed</h2>
                    <p class="text-gray-600 mt-2">We couldn't process your payment</p>
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
                            <p class="text-gray-600">Amount</p>
                            <p class="font-medium">Rp {{ number_format($consultation->fee, 0, ',', '.') }}</p>
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

                <!-- Error Details -->
                <div class="bg-red-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-red-800 mb-2">What went wrong?</h4>
                    <ul class="list-disc list-inside text-red-800 space-y-2">
                        <li>The payment process was not completed</li>
                        <li>This could be due to:
                            <ul class="list-disc list-inside ml-4 mt-2">
                                <li>Insufficient balance</li>
                                <li>Connection timeout</li>
                                <li>Payment cancelled</li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <!-- What to do next -->
                <div class="bg-blue-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-blue-800 mb-2">What to do next?</h4>
                    <ul class="list-disc list-inside text-blue-800 space-y-2">
                        <li>Check your e-wallet or bank account balance</li>
                        <li>Ensure you have a stable internet connection</li>
                        <li>Try the payment again</li>
                        <li>If the problem persists, contact our support</li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-center space-x-4">
                    <a href="{{ route('payments.process', $consultation) }}" 
                       class="bg-primary text-white px-6 py-2 rounded-md hover:bg-primary-dark">
                        Try Again
                    </a>
                    <a href="{{ route('owner.consultations.show', $consultation) }}" 
                       class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600">
                        Back to Consultation
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 