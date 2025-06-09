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
        $data = [];

        if ($user->hasRole('owner')) {
            $data['pets'] = $user->pets()
                ->with(['consultations' => function($query) {
                    $query->latest();
                }])
                ->latest()
                ->get();

            $data['consultations'] = Consultation::whereIn('pet_id', $user->pets->pluck('id'))
                ->with(['pet', 'doctor'])
                ->latest()
                ->take(5)
                ->get();
        }

        if ($user->hasRole('doctor')) {
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
        }

        if ($user->hasRole('clinic_admin')) {
            $data['doctors'] = User::role('doctor')
                ->latest()
                ->get();

            $data['products'] = Product::latest()
                ->take(6)
                ->get();
        }

        return view('dashboard', $data);
    }
} 