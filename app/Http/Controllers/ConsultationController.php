<?php

namespace App\Http\Controllers;

use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\ChatMessage;
use App\Enums\ConsultationStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConsultationController extends Controller
{
    /**
     * Display a listing of the consultations for owners.
     */
    public function ownerIndex(Request $request)
    {
        $user = $request->user();
        $consultations = Consultation::whereIn('pet_id', $user->pets->pluck('id'))
            ->with(['pet', 'doctor.user'])
            ->latest()
            ->paginate(10);

        return view('owner.appointments.index', compact('consultations'));
    }

    /**
     * Display a listing of the consultations for doctors.
     */
    public function doctorIndex(Request $request)
    {
        $user = $request->user();
        $consultations = Consultation::where('doctor_id', $user->doctor->id)
            ->with(['pet.owner', 'doctor.user'])
            ->latest()
            ->paginate(10);

        return view('doctor.consultations.index', compact('consultations'));
    }

    /**
     * Show the form for creating a new consultation.
     */
    public function create()
    {
        $doctors = Doctor::with('user')->get();
        $pets = auth()->user()->pets;
        
        return view('owner.appointments.create', compact('doctors', 'pets'));
    }

    /**
     * Store a newly created consultation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'doctor_id' => 'required|exists:doctors,id',
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // Verify pet ownership
            $pet = $request->user()->pets()->findOrFail($validated['pet_id']);

            $consultation = Consultation::create([
                'pet_id' => $pet->id,
                'doctor_id' => $validated['doctor_id'],
                'owner_id' => $request->user()->id,
                'scheduled_date' => $validated['scheduled_date'],
                'scheduled_time' => $validated['scheduled_time'],
                'reason' => $validated['reason'],
                'status' => ConsultationStatus::PENDING
            ]);

            DB::commit();

            return redirect()
                ->route('owner.appointments.show', $consultation)
                ->with('success', 'Appointment scheduled successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to schedule appointment. Please try again.');
        }
    }

    /**
     * Display the specified consultation.
     */
    public function show(Request $request, Consultation $consultation)
    {
        $user = $request->user();
        
        // Check access based on role
        if ($user->role === 'owner' && !$this->userOwnsConsultation($user, $consultation)) {
            return redirect()->route('owner.appointments.index')
                ->with('error', 'Unauthorized access to appointment');
        }
        
        if ($user->role === 'doctor' && $consultation->doctor_id !== $user->doctor->id) {
            return redirect()->route('doctor.consultations.index')
                ->with('error', 'Unauthorized access to consultation');
        }

        $consultation->load(['pet', 'doctor.user', 'owner', 'messages.sender']);
        
        return $user->role === 'owner' 
            ? view('owner.appointments.show', compact('consultation'))
            : view('doctor.consultations.show', compact('consultation'));
    }

    /**
     * Update the specified consultation.
     */
    public function update(Request $request, Consultation $consultation)
    {
        // Only doctors can update consultation details
        if (!$request->user()->isDoctor() || $consultation->doctor_id !== $request->user()->doctor->id) {
            return redirect()->route('doctor.consultations.index')
                ->with('error', 'Unauthorized to update consultation');
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:1000',
            'diagnosis' => 'required|string|max:500',
            'treatment' => 'required|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $consultation->update($validated);

            DB::commit();

            return redirect()
                ->route('doctor.consultations.show', $consultation)
                ->with('success', 'Consultation updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update consultation. Please try again.');
        }
    }

    /**
     * Complete the consultation
     */
    public function complete(Request $request, Consultation $consultation)
    {
        // Only doctors can complete consultations
        if (!$request->user()->isDoctor() || $consultation->doctor_id !== $request->user()->doctor->id) {
            return redirect()->route('doctor.consultations.index')
                ->with('error', 'Unauthorized to complete consultation');
        }

        try {
            DB::beginTransaction();

            $consultation->update([
                'status' => ConsultationStatus::COMPLETED,
                'completed_at' => now()
            ]);

            DB::commit();

            return redirect()
                ->route('doctor.consultations.show', $consultation)
                ->with('success', 'Consultation marked as completed');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to complete consultation. Please try again.');
        }
    }

    /**
     * Send a message in the consultation.
     */
    public function sendMessage(Request $request, Consultation $consultation)
    {
        $user = $request->user();
        
        // Check access based on role
        if ($user->role === 'owner' && !$this->userOwnsConsultation($user, $consultation)) {
            return back()->with('error', 'Unauthorized to send message');
        }
        
        if ($user->role === 'doctor' && $consultation->doctor_id !== $user->doctor->id) {
            return back()->with('error', 'Unauthorized to send message');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            ChatMessage::create([
                'consultation_id' => $consultation->id,
                'sender_id' => $user->id,
                'message' => $validated['message']
            ]);

            DB::commit();

            return back()->with('success', 'Message sent successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to send message. Please try again.');
        }
    }

    /**
     * Check if user owns the consultation through their pet.
     */
    private function userOwnsConsultation($user, $consultation)
    {
        return $user->pets()->where('id', $consultation->pet_id)->exists();
    }
} 