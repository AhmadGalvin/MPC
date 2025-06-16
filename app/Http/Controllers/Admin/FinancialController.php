<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialController extends Controller
{
    public function index()
    {
        // Get total revenue from paid consultations
        $totalRevenue = Consultation::where('payment_status', 'paid')
            ->sum('fee');

        // Get pending payments
        $pendingPayments = Consultation::with(['doctor.user', 'pet.owner'])
            ->where('payment_status', 'pending')
            ->latest()
            ->get();

        // Get paid payments
        $paidPayments = Consultation::with(['doctor.user', 'pet.owner'])
            ->where('payment_status', 'paid')
            ->latest()
            ->get();

        // Get monthly revenue data for chart
        $monthlyRevenue = Consultation::where('payment_status', 'paid')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(fee) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('admin.financial.index', compact(
            'totalRevenue',
            'pendingPayments',
            'paidPayments',
            'monthlyRevenue'
        ));
    }
} 