<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the medical records.
     */
    public function index(Request $request)
    {
        // Get the authenticated doctor's ID
        $doctorId = $request->user()->doctor->id;
        
        $records = MedicalRecord::where('doctor_id', $doctorId)
            ->with(['pet', 'diagnosis', 'doctor.user'])
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $records
        ]);
    }

    /**
     * Store a newly created medical record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'diagnosis_id' => 'required|exists:diagnoses,id',
            'notes' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('medical-records', 'public');
            }

            $record = MedicalRecord::create([
                'pet_id' => $validated['pet_id'],
                'doctor_id' => $request->user()->doctor->id,
                'diagnosis_id' => $validated['diagnosis_id'],
                'notes' => $validated['notes'],
                'file' => $filePath
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Medical record created successfully',
                'data' => $record->load(['pet', 'diagnosis', 'doctor.user'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create medical record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified medical record.
     */
    public function show(Request $request, MedicalRecord $medicalRecord)
    {
        // Check if the authenticated doctor owns this record
        if ($medicalRecord->doctor_id !== $request->user()->doctor->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to medical record'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'data' => $medicalRecord->load(['pet', 'diagnosis', 'doctor.user'])
        ]);
    }

    /**
     * Update the specified medical record.
     */
    public function update(Request $request, MedicalRecord $medicalRecord)
    {
        // Check if the authenticated doctor owns this record
        if ($medicalRecord->doctor_id !== $request->user()->doctor->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to medical record'
            ], 403);
        }

        $validated = $request->validate([
            'diagnosis_id' => 'sometimes|required|exists:diagnoses,id',
            'notes' => 'sometimes|required|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048'
        ]);

        try {
            DB::beginTransaction();

            if ($request->hasFile('file')) {
                // Delete old file if exists
                if ($medicalRecord->file) {
                    Storage::disk('public')->delete($medicalRecord->file);
                }
                $validated['file'] = $request->file('file')->store('medical-records', 'public');
            }

            $medicalRecord->update($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Medical record updated successfully',
                'data' => $medicalRecord->load(['pet', 'diagnosis', 'doctor.user'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update medical record',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified medical record.
     */
    public function destroy(Request $request, MedicalRecord $medicalRecord)
    {
        // Check if the authenticated doctor owns this record
        if ($medicalRecord->doctor_id !== $request->user()->doctor->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access to medical record'
            ], 403);
        }

        try {
            DB::beginTransaction();

            // Delete file if exists
            if ($medicalRecord->file) {
                Storage::disk('public')->delete($medicalRecord->file);
            }

            $medicalRecord->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Medical record deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete medical record',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 