<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $doctor = auth()->user();
        $today = Carbon::today();

        // Get today's appointments
        $todayAppointments = Appointment::where('doctor_id', $doctor->id)
            ->whereDate('scheduled_date', $today)
            ->count();

        // Get total unique patients
        $totalPatients = Pet::whereHas('appointments', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })->count();

        // Get total medical records
        $totalMedicalRecords = MedicalRecord::where('doctor_id', $doctor->id)->count();

        // Get total prescriptions
        $totalPrescriptions = Prescription::where('doctor_id', $doctor->id)->count();

        // Get upcoming appointments
        $upcomingAppointments = Appointment::with(['pet', 'pet.owner'])
            ->where('doctor_id', $doctor->id)
            ->where('scheduled_date', '>=', $today)
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->take(5)
            ->get()
            ->map(function ($appointment) {
                $appointment->date = Carbon::parse($appointment->scheduled_date);
                $appointment->time = Carbon::parse($appointment->scheduled_time);
                $appointment->status_color = match($appointment->status) {
                    'completed' => 'success',
                    'in_progress' => 'primary',
                    'cancelled' => 'danger',
                    default => 'warning'
                };
                return $appointment;
            });

        // Get recent medical records
        $recentMedicalRecords = MedicalRecord::with(['pet'])
            ->where('doctor_id', $doctor->id)
            ->latest()
            ->take(5)
            ->get();

        return view('doctor.dashboard', [
            'todayAppointments' => $todayAppointments,
            'totalPatients' => $totalPatients,
            'totalMedicalRecords' => $totalMedicalRecords,
            'totalPrescriptions' => $totalPrescriptions,
            'upcomingAppointments' => $upcomingAppointments,
            'recentMedicalRecords' => $recentMedicalRecords
        ]);
    }
} 