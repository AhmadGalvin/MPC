@extends('layouts.owner')

@section('title', 'Processing Payment')
@section('header', 'Processing Payment')

@section('content')
<div class="py-6">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <!-- Processing Message -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-spinner fa-spin text-blue-500 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Processing Your Payment</h2>
                    <p class="text-gray-600 mt-2">Please wait while we confirm your payment...</p>
                    <p class="text-gray-500 mt-4">You will be redirected automatically in <span id="countdown">5</span> seconds</p>
                </div>

                <!-- Transaction Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold mb-3">Transaction Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Transaction ID</p>
                            <p class="font-medium">{{ request('order_id') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Status</p>
                            <p class="font-medium">{{ ucfirst(request('transaction_status')) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Countdown timer
    let seconds = 5;
    const countdownElement = document.getElementById('countdown');
    
    const countdown = setInterval(() => {
        seconds--;
        countdownElement.textContent = seconds;
        
        if (seconds <= 0) {
            clearInterval(countdown);
            // Extract consultation ID from order_id (CONS-{id}-{timestamp})
            const orderId = '{{ request("order_id") }}';
            const consultationId = orderId.split('-')[1];
            // Redirect to consultation chat
            window.location.href = '/owner/chat/' + consultationId;
        }
    }, 1000);
</script>
@endpush

@endsection 