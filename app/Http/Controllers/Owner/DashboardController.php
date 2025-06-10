<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pets = $user->pets;
        $upcomingAppointments = $user->ownerAppointments()
            ->with(['pet', 'doctor'])
            ->where('scheduled_date', '>=', now()->format('Y-m-d'))
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_date')
            ->orderBy('scheduled_time')
            ->take(3)
            ->get();

        return view('owner.dashboard', compact('pets', 'upcomingAppointments'));
    }
} 