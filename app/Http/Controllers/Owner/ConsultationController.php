<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Display a listing of available doctors for consultation.
     */
    public function chooseDoctors()
    {
        $doctors = User::role('doctor')
            ->join('doctors', 'users.id', '=', 'doctors.user_id')
            ->select('users.*', 'doctors.specialization', 'doctors.sip_number', 'doctors.consultation_fee')
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
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required',
            'notes' => 'required|string|max:1000',
        ]);

        $consultation = auth()->user()->consultations()->create($validated);

        return redirect()->route('owner.consultations.show', $consultation)
            ->with('success', 'Consultation scheduled successfully.');
    }
} 