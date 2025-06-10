<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Auth::user()
            ->ownerAppointments()
            ->with(['pet', 'doctor'])
            ->orderBy('scheduled_date', 'desc')
            ->orderBy('scheduled_time', 'desc')
            ->paginate(10);

        return view('owner.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $pets = Auth::user()->pets;
        $doctors = User::where('role', 'doctor')->get();

        return view('owner.appointments.create', compact('pets', 'doctors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'doctor_id' => 'required|exists:users,id',
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_time' => 'required',
            'notes' => 'nullable|string|max:1000',
        ]);

        $appointment = new Appointment($validated);
        $appointment->owner_id = Auth::id();
        $appointment->status = 'pending';
        $appointment->save();

        return redirect()
            ->route('owner.appointments.show', $appointment)
            ->with('success', 'Appointment scheduled successfully.');
    }

    public function show(Appointment $appointment)
    {
        if ($appointment->owner_id !== Auth::id()) {
            abort(403);
        }

        return view('owner.appointments.show', compact('appointment'));
    }

    public function cancel(Appointment $appointment)
    {
        if ($appointment->owner_id !== Auth::id()) {
            abort(403);
        }

        if ($appointment->status !== 'pending') {
            return back()->with('error', 'Only pending appointments can be cancelled by owner.');
        }

        $appointment->status = 'cancelled';
        $appointment->cancelled_at = now();
        $appointment->save();

        return back()->with('success', 'Appointment has been cancelled.');
    }
} 