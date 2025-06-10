<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display the doctor's schedule.
     */
    public function index()
    {
        $doctor = auth()->user();
        $schedules = Schedule::where('doctor_id', $doctor->id)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('doctor.schedule.index', compact('schedules'));
    }
} 