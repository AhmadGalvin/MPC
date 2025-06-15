<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Enums\UserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('user')->latest()->get();
        return view('admin.doctors.index', compact('doctors'));
    }

    public function create()
    {
        return view('admin.doctors.create-step-one');
    }

    public function storeStepOne(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            DB::beginTransaction();
            
            // Create user with doctor role
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => UserRole::DOCTOR
            ]);

            DB::commit();
            return redirect()->route('admin.doctors.create.step.two', $user)
                           ->with('success', 'User account created successfully. Please complete the doctor profile.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create user account. Please try again.');
        }
    }

    public function createStepTwo(User $user)
    {
        // Check if user already has a doctor profile
        if ($user->doctor()->exists()) {
            return redirect()->route('admin.doctors.index')
                           ->with('error', 'This user already has a doctor profile.');
        }

        return view('admin.doctors.create-step-two', compact('user'));
    }

    public function store(Request $request, User $user)
    {
        $request->validate([
            'specialization' => 'required|string|max:255',
            'sip_number' => 'required|string|unique:doctors,sip_number',
            'schedule' => 'required|array',
            'schedule.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedule.*.start_time' => 'required|date_format:H:i',
            'schedule.*.end_time' => 'required|date_format:H:i|after:schedule.*.start_time',
            'consultation_fee' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Create doctor profile
            Doctor::create([
                'user_id' => $user->id,
                'specialization' => $request->specialization,
                'sip_number' => $request->sip_number,
                'schedule' => $request->schedule,
                'consultation_fee' => $request->consultation_fee,
                'is_available_for_consultation' => false // default value
            ]);

            DB::commit();
            return redirect()->route('admin.doctors.index')
                           ->with('success', 'Doctor profile created successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to create doctor profile. Please try again.');
        }
    }

    public function show(Doctor $doctor)
    {
        $doctor->load(['user', 'consultations']);
        return view('admin.doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor)
    {
        return view('admin.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($doctor->user_id)],
            'password' => 'nullable|string|min:8|confirmed',
            'specialization' => 'required|string|max:255',
            'sip_number' => ['required', 'string', Rule::unique('doctors')->ignore($doctor->id)],
            'schedule' => 'required|array',
            'schedule.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'schedule.*.start_time' => 'required|date_format:H:i',
            'schedule.*.end_time' => 'required|date_format:H:i|after:schedule.*.start_time',
            'consultation_fee' => 'required|numeric|min:0',
            'is_available_for_consultation' => 'boolean'
        ];

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Update user data
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $doctor->user->update($userData);

            // Update doctor profile
            $doctor->update([
                'specialization' => $request->specialization,
                'sip_number' => $request->sip_number,
                'schedule' => $request->schedule,
                'consultation_fee' => $request->consultation_fee,
                'is_available_for_consultation' => $request->boolean('is_available_for_consultation')
            ]);

            DB::commit();
            return redirect()->route('admin.doctors.index')
                           ->with('success', 'Doctor updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to update doctor. Please try again.');
        }
    }

    public function destroy(Doctor $doctor)
    {
        try {
            DB::beginTransaction();

            // Delete user (this will cascade delete the doctor profile)
            $doctor->user->delete();
            
            DB::commit();
            return redirect()->route('admin.doctors.index')
                           ->with('success', 'Doctor deleted successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to delete doctor. Please try again.');
        }
    }
} 