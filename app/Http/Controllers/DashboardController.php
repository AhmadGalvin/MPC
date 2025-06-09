<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\Product;
use App\Models\Consultation;
use App\Models\User;
use App\Models\MedicalRecord;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('clinic_admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('owner')) {
            return redirect()->route('owner.dashboard');
        }

        if ($user->hasRole('doctor')) {
            return redirect()->route('doctor.dashboard');
        }

        return redirect()->route('login')->with('error', 'Invalid user role');
    }

    public function ownerDashboard(Request $request)
    {
        $user = $request->user();
        
        if (!$user->hasRole('owner')) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access');
        }

        $data = [
            'pets' => $user->pets()
                ->with(['consultations' => function($query) {
                    $query->latest();
                }])
                ->latest()
                ->get(),

            'upcomingAppointments' => Consultation::whereIn('pet_id', $user->pets->pluck('id'))
                ->with(['pet', 'doctor'])
                ->where('status', 'pending')
                ->where('scheduled_date', '>=', now())
                ->orderBy('scheduled_date')
                ->orderBy('scheduled_time')
                ->take(5)
                ->get()
        ];

        return view('owner.dashboard', $data);
    }

    public function doctorDashboard(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        
        $data['todayAppointments'] = Consultation::where('doctor_id', $user->id)
            ->whereDate('scheduled_date', $today)
            ->with(['pet.owner'])
            ->orderBy('scheduled_time')
            ->get();

        $data['recentRecords'] = MedicalRecord::where('doctor_id', $user->id)
            ->with('pet')
            ->latest()
            ->take(5)
            ->get();

        return view('doctor.dashboard', $data);
    }
} 