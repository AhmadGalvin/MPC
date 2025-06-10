<?php

namespace App\Http\Controllers;

use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    /**
     * Display the doctor's schedule.
     */
    public function index(): View
    {
        $user = auth()->user();
        $schedules = DoctorSchedule::where('doctor_id', $user->id)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        return view('doctor.schedule', compact('schedules'));
    }
} 