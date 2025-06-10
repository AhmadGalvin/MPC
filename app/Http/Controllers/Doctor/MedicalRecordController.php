<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Pet;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $doctor = auth()->user();
        $records = MedicalRecord::where('doctor_id', $doctor->id)
            ->with(['pet', 'pet.owner'])
            ->latest()
            ->paginate(10);

        return view('doctor.medical-records.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $doctor = auth()->user();
        $patients = Pet::whereHas('appointments', function ($query) use ($doctor) {
            $query->where('doctor_id', $doctor->id);
        })->with('owner')->get();

        return view('doctor.medical-records.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'next_visit_date' => 'nullable|date|after:today',
        ]);

        $doctor = auth()->user();
        
        // Check if doctor has access to this patient
        $hasAccess = Pet::find($validated['pet_id'])
            ->appointments()
            ->where('doctor_id', $doctor->id)
            ->exists();

        if (!$hasAccess) {
            abort(403, 'You do not have access to create records for this patient.');
        }

        $record = MedicalRecord::create([
            'doctor_id' => $doctor->id,
            'pet_id' => $validated['pet_id'],
            'diagnosis' => $validated['diagnosis'],
            'treatment' => $validated['treatment'],
            'notes' => $validated['notes'],
            'next_visit_date' => $validated['next_visit_date'],
        ]);

        return redirect()->route('doctor.medical-records.show', $record)
            ->with('success', 'Medical record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalRecord $medicalRecord)
    {
        $this->authorize('view', $medicalRecord);
        
        $medicalRecord->load(['pet', 'pet.owner', 'doctor']);
        
        return view('doctor.medical-records.show', compact('medicalRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalRecord $medicalRecord)
    {
        $this->authorize('update', $medicalRecord);
        
        $medicalRecord->load(['pet', 'pet.owner']);
        
        return view('doctor.medical-records.edit', compact('medicalRecord'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $this->authorize('update', $medicalRecord);

        $validated = $request->validate([
            'diagnosis' => 'required|string',
            'treatment' => 'required|string',
            'notes' => 'nullable|string',
            'next_visit_date' => 'nullable|date|after:today',
        ]);

        $medicalRecord->update($validated);

        return redirect()->route('doctor.medical-records.show', $medicalRecord)
            ->with('success', 'Medical record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalRecord $medicalRecord)
    {
        $this->authorize('delete', $medicalRecord);
        
        $medicalRecord->delete();

        return redirect()->route('doctor.medical-records.index')
            ->with('success', 'Medical record deleted successfully.');
    }
} 