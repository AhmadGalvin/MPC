<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = auth()->user()->clinic->doctors;
        return view('admin.doctors.index', compact('doctors'));
    }

    public function show(Doctor $doctor)
    {
        $this->authorize('view', $doctor);
        return response()->json($doctor);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'specialization' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $clinic = auth()->user()->clinic;

        $doctor = $clinic->doctors()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'specialization' => $validated['specialization'],
            'is_active' => $validated['is_active'] ?? true,
            'role' => 'doctor'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Doctor created successfully',
            'data' => $doctor
        ]);
    }

    public function update(Request $request, Doctor $doctor)
    {
        $this->authorize('update', $doctor);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $doctor->id,
            'password' => 'nullable|string|min:8',
            'specialization' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $doctor->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Doctor updated successfully',
            'data' => $doctor
        ]);
    }

    public function destroy(Doctor $doctor)
    {
        $this->authorize('delete', $doctor);
        
        $doctor->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Doctor deleted successfully'
        ]);
    }
} 