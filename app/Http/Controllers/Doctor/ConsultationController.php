<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Doctor;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    /**
     * Display a listing of consultations for the doctor.
     */
    public function index()
    {
        $doctor = Doctor::where('user_id', auth()->id())->first();
        $consultations = Consultation::where('doctor_id', auth()->id())
            ->with(['pet', 'pet.owner'])
            ->orderBy('scheduled_date', 'asc')
            ->orderBy('scheduled_time', 'asc')
            ->paginate(10);

        return view('doctor.consultations.index', compact('doctor', 'consultations'));
    }

    /**
     * Toggle doctor's consultation availability status.
     */
    public function toggleStatus()
    {
        $doctor = Doctor::where('user_id', auth()->id())->first();
        $doctor->is_available_for_consultation = !$doctor->is_available_for_consultation;
        $doctor->save();

        $status = $doctor->is_available_for_consultation ? 'opened' : 'closed';
        return redirect()->route('doctor.consultations.index')
            ->with('success', "Consultation session has been {$status}.");
    }
} 