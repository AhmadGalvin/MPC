<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Consultation;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Display a listing of available doctors for consultation.
     */
    public function chooseDoctors()
    {
        $doctors = User::role('doctor')
            ->join('doctors', 'users.id', '=', 'doctors.user_id')
            ->select('users.*', 'doctors.id as doctor_id', 'doctors.specialization', 'doctors.sip_number', 'doctors.consultation_fee')
            ->where('users.is_active', true)
            ->where('doctors.is_available_for_consultation', true)
            ->paginate(10);

        return view('owner.consultations.choose-doctor', compact('doctors'));
    }

    /**
     * Show the form for creating a new consultation.
     */
    public function create(Request $request)
    {
        $doctor = User::role('doctor')
            ->join('doctors', 'users.id', '=', 'doctors.user_id')
            ->select('users.*', 'doctors.specialization', 'doctors.sip_number', 'doctors.consultation_fee')
            ->where('users.id', $request->doctor)
            ->firstOrFail();

        return view('owner.consultations.create', compact('doctor'));
    }

    /**
     * Store a newly created consultation in storage.
     */
    public function store(Request $request)
    {
        \Log::info('Consultation Request Data:', [
            'all_data' => $request->all(),
            'doctor_id' => $request->doctor_id,
            'pet_id' => $request->pet_id,
            'fee' => $request->fee
        ]);

        try {
            $validated = $request->validate([
                'doctor_id' => 'required|exists:doctors,id',
                'pet_id' => 'required|exists:pets,id',
                'fee' => 'required|numeric|min:0'
            ], [
                'doctor_id.required' => 'Doctor ID is missing',
                'doctor_id.exists' => 'Selected doctor does not exist',
                'pet_id.required' => 'Pet ID is missing',
                'pet_id.exists' => 'Selected pet does not exist',
                'fee.required' => 'Consultation fee is missing',
                'fee.numeric' => 'Consultation fee must be a number',
                'fee.min' => 'Consultation fee cannot be negative'
            ]);

            DB::beginTransaction();

            // Create consultation
            $consultation = Consultation::create([
                'pet_id' => $validated['pet_id'],
                'doctor_id' => $validated['doctor_id'],
                'owner_id' => auth()->id(),
                'fee' => $validated['fee'],
                'status' => 'pending',
                'payment_status' => 'pending',
                'scheduled_date' => now()->toDateString(),
                'scheduled_time' => now()->toTimeString()
            ]);

            // Create payment transaction
            $result = $this->midtransService->createTransaction($consultation);

            if (!$result['success']) {
                DB::rollBack();
                \Log::error('Midtrans transaction failed:', ['error' => $result['message']]);
                return redirect()->back()->with('error', 'Failed to create payment: ' . $result['message']);
            }

            // Update consultation with payment URL
            $consultation->update([
                'payment_url' => $result['payment_url'],
                'transaction_id' => $result['transaction_id'] ?? null
            ]);

            DB::commit();

            // Redirect to payment page
            return redirect($result['payment_url']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Consultation creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create consultation. Error: ' . $e->getMessage());
        }
    }
} 