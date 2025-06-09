<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorSchedule;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $doctors = auth()->user()->clinic->doctors;
        return view('admin.schedules.index', compact('doctors'));
    }

    public function events()
    {
        $schedules = auth()->user()->clinic->doctorSchedules()
            ->with('doctor')
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'title' => $schedule->doctor->name,
                    'start' => $schedule->date . ' ' . $schedule->start_time,
                    'end' => $schedule->date . ' ' . $schedule->end_time,
                    'color' => '#4F46E5', // Primary color
                    'extendedProps' => [
                        'doctor_id' => $schedule->doctor_id,
                        'notes' => $schedule->notes
                    ]
                ];
            });

        return response()->json($schedules);
    }

    public function show(DoctorSchedule $schedule)
    {
        $this->authorize('view', $schedule);
        return response()->json($schedule);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string'
        ]);

        $clinic = auth()->user()->clinic;
        $doctor = Doctor::findOrFail($validated['doctor_id']);

        // Check if doctor belongs to clinic
        if ($doctor->clinic_id !== $clinic->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Doctor does not belong to this clinic'
            ], 403);
        }

        // Check for schedule conflicts
        $hasConflict = $doctor->schedules()
            ->where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'status' => 'error',
                'message' => 'Schedule conflicts with existing appointments'
            ], 422);
        }

        $schedule = $doctor->schedules()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Schedule created successfully',
            'data' => $schedule
        ]);
    }

    public function update(Request $request, DoctorSchedule $schedule)
    {
        $this->authorize('update', $schedule);

        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string'
        ]);

        // Check for schedule conflicts excluding current schedule
        $hasConflict = $schedule->doctor->schedules()
            ->where('id', '!=', $schedule->id)
            ->where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                    ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'status' => 'error',
                'message' => 'Schedule conflicts with existing appointments'
            ], 422);
        }

        $schedule->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Schedule updated successfully',
            'data' => $schedule
        ]);
    }

    public function updateDate(Request $request, DoctorSchedule $schedule)
    {
        $this->authorize('update', $schedule);

        $validated = $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        $startDate = Carbon::parse($validated['start'])->format('Y-m-d');
        $startTime = Carbon::parse($validated['start'])->format('H:i:s');
        $endTime = Carbon::parse($validated['end'])->format('H:i:s');

        // Check for schedule conflicts excluding current schedule
        $hasConflict = $schedule->doctor->schedules()
            ->where('id', '!=', $schedule->id)
            ->where('date', $startDate)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'status' => 'error',
                'message' => 'Schedule conflicts with existing appointments'
            ], 422);
        }

        $schedule->update([
            'date' => $startDate,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Schedule date updated successfully',
            'data' => $schedule
        ]);
    }

    public function updateTime(Request $request, DoctorSchedule $schedule)
    {
        $this->authorize('update', $schedule);

        $validated = $request->validate([
            'start' => 'required|date',
            'end' => 'required|date|after:start'
        ]);

        $startTime = Carbon::parse($validated['start'])->format('H:i:s');
        $endTime = Carbon::parse($validated['end'])->format('H:i:s');

        // Check for schedule conflicts excluding current schedule
        $hasConflict = $schedule->doctor->schedules()
            ->where('id', '!=', $schedule->id)
            ->where('date', $schedule->date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'status' => 'error',
                'message' => 'Schedule conflicts with existing appointments'
            ], 422);
        }

        $schedule->update([
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Schedule time updated successfully',
            'data' => $schedule
        ]);
    }

    public function destroy(DoctorSchedule $schedule)
    {
        $this->authorize('delete', $schedule);
        
        $schedule->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Schedule deleted successfully'
        ]);
    }
} 