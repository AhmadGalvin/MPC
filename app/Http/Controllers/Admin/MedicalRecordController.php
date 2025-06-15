<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $records = MedicalRecord::with(['pet', 'pet.owner', 'doctor'])
            ->latest()
            ->paginate(10);
        return view('admin.medical-records.index', compact('records'));
    }

    public function create(Request $request)
    {
        $pet = null;
        if ($request->has('pet_id')) {
            $pet = Pet::with('owner')->findOrFail($request->pet_id);
        }
        $pets = Pet::with('owner')->get();
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        
        return view('admin.medical-records.create', compact('pets', 'doctors', 'pet'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'diagnosis' => ['required', 'string'],
            'treatment' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'next_visit_date' => ['nullable', 'date', 'after:today'],
        ]);

        $record = MedicalRecord::create($validated);

        if ($request->has('from_pet')) {
            return redirect()
                ->route('admin.pets.show', $record->pet)
                ->with('success', 'Medical record added successfully!');
        }

        return redirect()
            ->route('admin.medical-records.index')
            ->with('success', 'Medical record added successfully!');
    }

    public function show(MedicalRecord $medicalRecord)
    {
        $medicalRecord->load(['pet', 'pet.owner', 'doctor']);
        return view('admin.medical-records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord)
    {
        $pets = Pet::with('owner')->get();
        $doctors = User::where('role', 'doctor')->where('is_active', true)->get();
        return view('admin.medical-records.edit', compact('medicalRecord', 'pets', 'doctors'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        $validated = $request->validate([
            'pet_id' => ['required', 'exists:pets,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'diagnosis' => ['required', 'string'],
            'treatment' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'next_visit_date' => ['nullable', 'date', 'after:today'],
        ]);

        $medicalRecord->update($validated);

        return redirect()
            ->route('admin.medical-records.show', $medicalRecord)
            ->with('success', 'Medical record updated successfully!');
    }

    public function destroy(MedicalRecord $medicalRecord)
    {
        $medicalRecord->delete();

        return back()->with('success', 'Medical record deleted successfully!');
    }
} 