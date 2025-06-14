@extends('layouts.owner')

@section('title', 'Payment')
@section('header', 'Payment Details')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4">
        <div class="mb-6">
            <a href="{{ route('owner.consultations.show', $consultation) }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i>Back to Consultation
            </a>
        </div>

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <!-- Payment Header -->
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Complete Your Payment</h2>
                    <p class="text-gray-600 mt-2">Please complete the payment to confirm your consultation</p>
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

                <!-- Payment Amount -->
                <div class="text-center mb-6">
                    <p class="text-gray-600">Amount to Pay</p>
                    <p class="text-3xl font-bold text-primary">Rp {{ number_format($consultation->fee, 0, ',', '.') }}</p>
                </div>

                <!-- QRIS Code -->
                <div class="text-center mb-6">
                    <div class="inline-block border-2 border-gray-200 rounded-lg p-4">
                        <img src="{{ $consultation->payment_url }}" alt="QRIS Code" class="mx-auto max-w-xs">
                    </div>
                    <p class="text-gray-600 mt-4">Scan this QR code using your e-wallet or mobile banking app</p>
                </div>

                <!-- Payment Instructions -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-800 mb-2">Payment Instructions:</h4>
                    <ol class="list-decimal list-inside text-blue-800 space-y-2">
                        <li>Open your e-wallet or mobile banking app</li>
                        <li>Select the QRIS/Scan QR option</li>
                        <li>Scan the QR code above</li>
                        <li>Check the payment details and amount</li>
                        <li>Complete the payment</li>
                    </ol>
                    <p class="mt-4 text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-2"></i>
                        The page will automatically refresh once payment is confirmed
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Check payment status every 5 seconds
    setInterval(function() {
        $.get('{{ route("payments.check-status", $consultation) }}', function(response) {
            if (response.status === 'paid') {
                window.location.href = '{{ route("payments.success", $consultation) }}';
            }
        });
    }, 5000);
</script>
@endpush

@endsection 