<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\User;
use App\Models\Product;
use App\Models\Consultation;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    public function index(): JsonResponse
    {
        $statistics = [
            'status' => 'success',
            'totalPets' => Pet::count(),
            'activeDoctors' => User::role('doctor')->where('is_active', true)->count(),
            'totalProducts' => Product::count(),
            'todayAppointments' => Consultation::whereDate('scheduled_date', today())->count(),
        ];

        return response()->json($statistics);
    }
} 