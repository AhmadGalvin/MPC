<?php

namespace App\Services;

use App\Models\Consultation;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
        // Set your Merchant Server Key
        Config::$serverKey = config('services.midtrans.server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        Config::$isProduction = config('services.midtrans.is_production', false);
        // Set sanitization on (default)
        Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        Config::$is3ds = true;
    }

    public function createTransaction(Consultation $consultation)
    {
        $params = [
            'transaction_details' => [
                'order_id' => 'CONS-' . $consultation->id . '-' . time(),
                'gross_amount' => $consultation->fee,
            ],
            'customer_details' => [
                'first_name' => $consultation->owner->name,
                'email' => $consultation->owner->email,
            ],
            'enabled_payments' => ['gopay', 'bca_va'],
            'item_details' => [
                [
                    'id' => 'CONS-' . $consultation->id,
                    'price' => $consultation->fee,
                    'quantity' => 1,
                    'name' => 'Consultation with Dr. ' . $consultation->doctor->user->name,
                ]
            ],
        ];

        try {
            // Get Snap Payment Page URL
            $paymentUrl = Snap::createTransaction($params)->redirect_url;
            
            // Save the payment URL
            $consultation->update([
                'payment_url' => $paymentUrl,
                'transaction_id' => $params['transaction_details']['order_id'],
            ]);

            return [
                'success' => true,
                'payment_url' => $paymentUrl,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function handleCallback(array $payload)
    {
        $orderId = $payload['order_id'];
        $status = $payload['transaction_status'];
        $type = $payload['payment_type'];

        // Extract consultation ID from order ID (CONS-{id}-{timestamp})
        $consultationId = explode('-', $orderId)[1];
        $consultation = Consultation::find($consultationId);

        if (!$consultation) {
            return [
                'success' => false,
                'message' => 'Consultation not found',
            ];
        }

        // Update consultation based on transaction status
        switch ($status) {
            case 'capture':
            case 'settlement':
                $consultation->update([
                    'payment_status' => 'paid',
                    'payment_method' => $type,
                    'status' => 'confirmed',
                    'paid_at' => now(),
                ]);
                break;
            case 'pending':
                $consultation->update([
                    'payment_status' => 'pending',
                    'payment_method' => $type,
                ]);
                break;
            case 'deny':
            case 'expire':
            case 'cancel':
                $consultation->update([
                    'payment_status' => 'failed',
                    'payment_method' => $type,
                    'status' => 'cancelled',
                    'cancellation_reason' => 'Payment ' . $status,
                ]);
                break;
        }

        return [
            'success' => true,
            'consultation' => $consultation,
        ];
    }
} 