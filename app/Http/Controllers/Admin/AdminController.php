<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\Product;
use App\Models\User;
use App\Models\ClinicUpdate;
use App\Models\MedicalRecord;
use App\Models\Pet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        try {
            // Get basic statistics
            $data = [
                'doctors' => User::where('role', 'doctor')->get(),
                'products' => Product::latest()->take(6)->get(),
                'pendingConsultations' => Consultation::where('status', 'pending')->count(),
                'completedToday' => Consultation::whereDate('completed_at', Carbon::today())->count(),
                'recentActivities' => ClinicUpdate::with('user:id,name')
                    ->latest()
                    ->take(10)
                    ->get()
            ];

            return view('admin.dashboard', $data);
        } catch (\Exception $e) {
            \Log::error('Dashboard error: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to load dashboard data'
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to load dashboard data. Please try again.');
        }
    }

    public function activities(): JsonResponse
    {
        try {
            $clinic = Auth::user()->ownedClinic()->firstOrFail();
            $lastWeek = Carbon::now()->subWeek();

            // Use a single query to get all activities
            $activities = collect();

            // Get clinic updates with user info
            $clinicUpdates = ClinicUpdate::where('clinic_id', $clinic->id)
                ->with('user:id,name')
                ->where('created_at', '>=', $lastWeek)
                ->get()
                ->map(function ($update) {
                    return [
                        'type' => 'clinic_update',
                        'description' => $update->changes,
                        'user_name' => $update->user->name,
                        'created_at' => $update->created_at,
                    ];
                });
            $activities = $activities->concat($clinicUpdates);

            // Get consultations with related data
            $consultations = Consultation::where('clinic_id', $clinic->id)
                ->with(['doctor:id,name', 'pet:id,name'])
                ->where(function ($query) use ($lastWeek) {
                    $query->where('created_at', '>=', $lastWeek)
                        ->orWhere(function ($q) use ($lastWeek) {
                            $q->where('status', 'completed')
                                ->where('updated_at', '>=', $lastWeek);
                        });
                })
                ->get();

            // Map new consultations
            $newConsultations = $consultations
                ->where('created_at', '>=', $lastWeek)
                ->map(function ($consultation) {
                    return [
                        'type' => 'new_consultation',
                        'description' => "New consultation scheduled with Dr. {$consultation->doctor->name} for {$consultation->pet->name}",
                        'created_at' => $consultation->created_at,
                    ];
                });
            $activities = $activities->concat($newConsultations);

            // Map completed consultations
            $completedConsultations = $consultations
                ->where('status', 'completed')
                ->where('updated_at', '>=', $lastWeek)
                ->map(function ($consultation) {
                    return [
                        'type' => 'completed_consultation',
                        'description' => "Consultation completed by Dr. {$consultation->doctor->name} for {$consultation->pet->name}",
                        'created_at' => $consultation->updated_at,
                    ];
                });
            $activities = $activities->concat($completedConsultations);

            // Get medical records with consultation and pet info
            $newRecords = MedicalRecord::whereHas('consultation', function ($query) use ($clinic) {
                    $query->where('clinic_id', $clinic->id);
                })
                ->with(['consultation.pet:id,name', 'doctor:id,name'])
                ->where('created_at', '>=', $lastWeek)
                ->get()
                ->map(function ($record) {
                    return [
                        'type' => 'medical_record',
                        'description' => "New medical record added by Dr. {$record->doctor->name} for {$record->consultation->pet->name}",
                        'created_at' => $record->created_at,
                    ];
                });
            $activities = $activities->concat($newRecords);

            // Sort all activities by date and take latest 50
            $activities = $activities->sortByDesc('created_at')
                ->values()
                ->take(50);

            return response()->json([
                'status' => 'success',
                'data' => $activities
            ]);
        } catch (\Exception $e) {
            \Log::error('Activities error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load activities'
            ], 500);
        }
    }

    public function getClinicProfile(): JsonResponse
    {
        try {
            $clinic = Auth::user()->ownedClinic()
                ->with(['owner', 'doctors'])
                ->firstOrFail();

            return response()->json([
                'status' => 'success',
                'data' => $clinic
            ]);
        } catch (\Exception $e) {
            \Log::error('Clinic profile error: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load clinic profile'
            ], 500);
        }
    }
} 