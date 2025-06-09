<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Enums\ConsultationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the consultations.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Consultation::query()->with(['pet', 'doctor.user', 'owner']);

        // Filter based on user role
        if ($user->role === 'owner') {
            $query->where('owner_id', $user->id);
        } elseif ($user->role === 'doctor') {
            $query->where('doctor_id', $user->doctor->id);
        }

        $consultations = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $consultations
        ]);
    }

    /**
     * Store a newly created consultation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'doctor_id' => 'required|exists:doctors,id'
        ]);

        try {
            DB::beginTransaction();

            // Verify pet ownership
            $pet = $request->user()->pets()->findOrFail($validated['pet_id']);

            $consultation = Consultation::create([
                'pet_id' => $pet->id,
                'doctor_id' => $validated['doctor_id'],
                'owner_id' => $request->user()->id,
                'status' => ConsultationStatus::PENDING
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Consultation created successfully',
                'data' => $consultation->load(['pet', 'doctor.user', 'owner'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create consultation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified consultation.
     */
    public function show(Request $request, Consultation $consultation)
    {
        // Check if user has access to this consultation
        if (!$this->userHasAccess($request->user(), $consultation)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to consultation'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $consultation->load(['pet', 'doctor.user', 'owner'])
        ]);
    }

    /**
     * Update the specified consultation.
     */
    public function update(Request $request, Consultation $consultation)
    {
        // Only doctors can update consultation status
        if ($request->user()->role !== 'doctor' || $consultation->doctor_id !== $request->user()->doctor->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to update consultation'
            ], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,completed'
        ]);

        try {
            DB::beginTransaction();

            $consultation->update([
                'status' => $validated['status']
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Consultation updated successfully',
                'data' => $consultation->load(['pet', 'doctor.user', 'owner'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update consultation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get messages for a consultation (polling endpoint).
     */
    public function getMessages(Request $request, Consultation $consultation)
    {
        // Check if user has access to this consultation
        if (!$this->userHasAccess($request->user(), $consultation)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to consultation messages'
            ], 403);
        }

        // Get messages after the last_id if provided
        $query = $consultation->messages()->with('sender');
        if ($request->has('last_id')) {
            $query->where('id', '>', $request->last_id);
        }

        $messages = $query->orderBy('created_at', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    /**
     * Check if user has access to the consultation.
     */
    private function userHasAccess($user, Consultation $consultation): bool
    {
        if ($user->role === 'owner') {
            return $consultation->owner_id === $user->id;
        } elseif ($user->role === 'doctor') {
            return $consultation->doctor_id === $user->doctor->id;
        }
        return false;
    }
} 