<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Services\MidtransService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function process(Consultation $consultation)
    {
        // Check if consultation is already paid
        if ($consultation->payment_status === 'paid') {
            return redirect()->route('owner.consultations.show', $consultation)
                ->with('error', 'This consultation has already been paid.');
        }

        // Create Midtrans transaction
        $result = $this->midtransService->createTransaction($consultation);

        if (!$result['success']) {
            return redirect()->route('owner.consultations.show', $consultation)
                ->with('error', 'Failed to create payment: ' . $result['message']);
        }

        // Redirect to Midtrans payment page
        return redirect($result['payment_url']);
    }

    public function callback(Request $request)
    {
        $payload = $request->all();
        
        // Handle notification
        $result = $this->midtransService->handleCallback($payload);

        if (!$result['success']) {
            return response()->json(['message' => $result['message']], 404);
        }

        return response()->json(['message' => 'Payment processed successfully']);
    }

    public function success(Consultation $consultation)
    {
        // Update consultation status and payment status
        $consultation->update([
            'status' => 'confirmed',
            'payment_status' => 'paid',
            'paid_at' => now()
        ]);

        return redirect()->route('owner.consultations.choose-doctors')
            ->with('success', 'Payment successful! You can now start chatting with the doctor.');
    }

    public function failed(Consultation $consultation)
    {
        return redirect()->route('owner.consultations.show', $consultation)
            ->with('error', 'Payment failed. Please try again.');
    }
} 