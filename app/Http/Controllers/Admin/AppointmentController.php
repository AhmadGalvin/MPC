<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['pet', 'pet.owner', 'doctor'])
            ->latest('scheduled_date')
            ->paginate(10);
        return view('admin.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $pets = Pet::with('owner')->get();
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        
        return view('admin.appointments.create', compact('pets', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'scheduled_date' => ['required', 'date', 'after_or_equal:today'],
            'scheduled_time' => ['required', 'date_format:H:i'],
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        // Get pet's owner ID
        $pet = Pet::findOrFail($validated['pet_id']);
        
        // Check if doctor is available at this time
        $existingAppointment = Appointment::where('doctor_id', $validated['doctor_id'])
            ->whereDate('scheduled_date', $validated['scheduled_date'])
            ->whereTime('scheduled_time', $validated['scheduled_time'])
            ->exists();

        if ($existingAppointment) {
            return back()
                ->withInput()
                ->withErrors(['scheduled_time' => 'Doctor is not available at this time. Please select a different time or doctor.']);
        }

        // Create the appointment
        $appointment = Appointment::create([
            'pet_id' => $validated['pet_id'],
            'doctor_id' => $validated['doctor_id'],
            'owner_id' => $pet->owner_id,
            'scheduled_date' => $validated['scheduled_date'],
            'scheduled_time' => $validated['scheduled_time'],
            'notes' => $validated['notes'],
            'status' => 'scheduled'
        ]);

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('success', 'Appointment booked successfully!');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['pet', 'pet.owner', 'doctor']);
        return view('admin.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        if ($appointment->status === 'completed' || $appointment->status === 'cancelled') {
            return back()->with('error', 'Cannot edit completed or cancelled appointments.');
        }

        $pets = Pet::with('owner')->get();
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        return view('admin.appointments.edit', compact('appointment', 'pets', 'doctors'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        if ($appointment->status === 'completed' || $appointment->status === 'cancelled') {
            return back()->with('error', 'Cannot update completed or cancelled appointments.');
        }

        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'scheduled_date' => ['required', 'date', 'after:today'],
            'scheduled_time' => ['required'],
            'reason' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        // Combine date and time
        $scheduledDateTime = Carbon::parse($validated['scheduled_date'])->setTimeFromTimeString($validated['scheduled_time']);
        
        // Check if doctor is available at this time (excluding current appointment)
        $existingAppointment = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('scheduled_date', $scheduledDateTime)
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($existingAppointment) {
            return back()
                ->withInput()
                ->withErrors(['scheduled_time' => 'Doctor is not available at this time.']);
        }

        $appointment->update([
            'pet_id' => $validated['pet_id'],
            'doctor_id' => $validated['doctor_id'],
            'scheduled_date' => $scheduledDateTime,
            'reason' => $validated['reason'],
            'notes' => $validated['notes']
        ]);

        return redirect()
            ->route('admin.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully!');
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->status === 'completed') {
            return back()->with('error', 'Cannot delete completed appointments.');
        }

        $appointment->delete();

        return back()->with('success', 'Appointment cancelled successfully!');
    }

    public function confirm(Appointment $appointment)
    {
        if ($appointment->status !== 'scheduled') {
            return back()->with('error', 'Can only confirm scheduled appointments.');
        }

        $appointment->update(['status' => 'confirmed']);

        return back()->with('success', 'Appointment confirmed successfully!');
    }

    public function complete(Appointment $appointment)
    {
        if ($appointment->status !== 'confirmed') {
            return back()->with('error', 'Can only complete confirmed appointments.');
        }

        $appointment->update(['status' => 'completed']);

        return back()->with('success', 'Appointment marked as completed!');
    }

    public function cancel(Appointment $appointment)
    {
        if ($appointment->status === 'completed') {
            return back()->with('error', 'Cannot cancel completed appointments.');
        }

        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled successfully!');
    }
} 