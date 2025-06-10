<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctor = auth()->user();
        $patients = Pet::whereHas('appointments', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })->with('owner')->paginate(10);

        return view('doctor.patients.index', compact('patients'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Pet $patient)
    {
        $doctor = auth()->user();
        
        // Check if the doctor has ever treated this patient
        $hasAccess = $patient->appointments()
            ->where('doctor_id', $doctor->id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to this patient\'s records.');
        }

        $patient->load(['owner', 'appointments' => function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id)
                  ->with('medicalRecords');
        }]);

        return view('doctor.patients.show', compact('patient'));
    }
} 