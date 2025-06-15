<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Consultation;
use App\Models\Pet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total doctors
        $totalDoctors = User::where('role', 'doctor')->count();

        // Get total pet owners
        $totalOwners = User::where('role', 'owner')->count();

        // Get total pets
        $totalPets = Pet::count();

        // Get today's consultations
        $todayConsultations = Consultation::whereDate('created_at', Carbon::today())->count();

        // Get total revenue from consultations
        $totalRevenue = Consultation::where('payment_status', 'paid')
            ->sum('fee');

        // Get recent activities (last 10 consultations)
        $recentActivities = Consultation::with(['doctor.user', 'pet.owner'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($consultation) {
                return [
                    'description' => "Consultation between Dr. {$consultation->doctor->user->name} and {$consultation->pet->owner->name}'s pet {$consultation->pet->name}",
                    'status' => $consultation->status,
                    'created_at' => $consultation->created_at->diffForHumans()
                ];
            });

        // Get latest consultations
        $latestConsultations = Consultation::with(['doctor.user', 'pet.owner', 'pet'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalDoctors',
            'totalOwners',
            'totalPets',
            'todayConsultations',
            'totalRevenue',
            'recentActivities',
            'latestConsultations'
        ));
    }
} 