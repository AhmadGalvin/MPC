<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    /**
     * Display a listing of the doctors.
     */
    public function index()
    {
        $doctors = Doctor::with('user')->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $doctors
        ]);
    }

    /**
     * Store a newly created doctor.
     */
    public function store(StoreDoctorRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Create user with doctor role
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => UserRole::DOCTOR
            ]);

            // Create doctor profile
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'specialization' => $validated['specialization'],
                'sip_number' => $validated['sip_number'],
                'schedule' => $validated['schedule']
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Doctor created successfully',
                'data' => $doctor->load('user')
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create doctor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified doctor.
     */
    public function show(Doctor $doctor)
    {
        return response()->json([
            'status' => 'success',
            'data' => $doctor->load('user')
        ]);
    }

    /**
     * Update the specified doctor.
     */
    public function update(UpdateDoctorRequest $request, Doctor $doctor)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Update user information if provided
            if (isset($validated['name']) || isset($validated['email'])) {
                $doctor->user->update(array_filter([
                    'name' => $validated['name'] ?? null,
                    'email' => $validated['email'] ?? null,
                ]));
            }

            // Update doctor information
            $doctor->update(array_filter([
                'specialization' => $validated['specialization'] ?? null,
                'sip_number' => $validated['sip_number'] ?? null,
                'schedule' => $validated['schedule'] ?? null,
            ]));

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Doctor updated successfully',
                'data' => $doctor->load('user')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update doctor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified doctor.
     */
    public function destroy(Doctor $doctor)
    {
        try {
            DB::beginTransaction();

            // Delete the user (will cascade to doctor due to foreign key)
            $doctor->user->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Doctor deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete doctor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 