<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the appointments.
     */
    public function index()
    {
        $doctor = auth()->user();
        $today = Carbon::today();

        $appointments = Appointment::with(['pet', 'owner'])
            ->where('doctor_id', $doctor->id)
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->paginate(10);

        return view('doctor.appointments.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new appointment.
     */
    public function create()
    {
        $pets = Pet::with('owner')->get();
        return view('doctor.appointments.create', compact('pets'));
    }

    /**
     * Store a newly created appointment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required',
            'type' => 'required|string',
            'notes' => 'nullable|string',
            'fee' => 'required|numeric|min:0'
        ]);

        $doctor = auth()->user();
        $pet = Pet::findOrFail($validated['pet_id']);

        $appointment = Appointment::create([
            'doctor_id' => $doctor->id,
            'pet_id' => $pet->id,
            'owner_id' => $pet->owner_id,
            'clinic_id' => $doctor->clinic_id,
            'scheduled_date' => $validated['scheduled_date'],
            'scheduled_time' => $validated['scheduled_time'],
            'type' => $validated['type'],
            'notes' => $validated['notes'],
            'fee' => $validated['fee'],
            'status' => 'pending'
        ]);

        return redirect()->route('doctor.appointments.show', $appointment)
            ->with('success', 'Appointment created successfully.');
    }

    /**
     * Display the specified appointment.
     */
    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        return view('doctor.appointments.show', compact('appointment'));
    }

    /**
     * Show the form for editing the specified appointment.
     */
    public function edit(Appointment $appointment)
    {
        $this->authorize('update', $appointment);
        $pets = Pet::with('owner')->get();
        return view('doctor.appointments.edit', compact('appointment', 'pets'));
    }

    /**
     * Update the specified appointment in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $this->authorize('update', $appointment);

        $validated = $request->validate([
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required',
            'type' => 'required|string',
            'notes' => 'nullable|string',
            'fee' => 'required|numeric|min:0',
            'status' => 'required|in:pending,confirmed,completed,cancelled'
        ]);

        if ($validated['status'] === 'completed' && $appointment->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        if ($validated['status'] === 'cancelled' && $appointment->status !== 'cancelled') {
            $validated['cancelled_at'] = now();
            $validated['cancellation_reason'] = $request->input('cancellation_reason');
        }

        $appointment->update($validated);

        return redirect()->route('doctor.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified appointment from storage.
     */
    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);
        
        $appointment->delete();

        return redirect()->route('doctor.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }
} 