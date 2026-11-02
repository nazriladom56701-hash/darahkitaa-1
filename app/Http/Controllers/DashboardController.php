<?php

namespace App\Http\Controllers;

use App\Models\Donor;
use App\Models\DonorEvent;
use App\Models\BloodStock;
use App\Models\BloodStockMutation;
use App\Models\HospitalRequest;
use App\Models\ReminderNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        // 1. Stats Summary
        $totalStockUnits = (int) BloodStock::sum('total_units');
        $totalDonors = Donor::count();
        $verifiedDonors = Donor::where('verification_status', 'verified')->count();
        $pendingVerificationDonors = Donor::where('verification_status', 'pending')->count();
        
        $activeEventsCount = DonorEvent::whereIn('status', ['upcoming', 'ongoing'])->count();
        $pendingRequestsCount = HospitalRequest::where('status', 'pending')->count();
        $emergencyRequestsCount = HospitalRequest::where('status', 'pending')->where('urgency', 'emergency')->count();

        // 2. Blood Stock by Blood Group Aggregated
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        $stockOverview = [];

        foreach ($bloodGroups as $group) {
            $type = substr($group, 0, -1);
            $rhesus = substr($group, -1);
            
            $stocks = BloodStock::where('blood_type', $type)->where('rhesus', $rhesus)->get();
            $totalUnits = $stocks->sum('total_units');
            $minThreshold = $stocks->max('min_threshold') ?? 10;
            
            $status = 'safe';
            if ($totalUnits <= ($minThreshold / 2)) {
                $status = 'critical';
            } elseif ($totalUnits <= $minThreshold) {
                $status = 'warning';
            }

            $componentsBreakdown = [];
            foreach ($stocks as $stock) {
                $componentsBreakdown[$stock->component] = $stock->total_units;
            }

            $stockOverview[] = [
                'blood_type' => $type,
                'rhesus' => $rhesus,
                'blood_type_full' => $group,
                'total_units' => $totalUnits,
                'min_threshold' => $minThreshold,
                'status' => $status,
                'components' => $componentsBreakdown,
            ];
        }

        // 3. Recent Pending Hospital Requests
        $recentRequests = HospitalRequest::latest()
            ->take(5)
            ->get();

        // 4. Upcoming / Ongoing Events
        $recentEvents = DonorEvent::whereIn('status', ['ongoing', 'upcoming'])
            ->orderBy('event_date', 'asc')
            ->take(3)
            ->get();

        // 5. Recent Stock Mutations
        $recentMutations = BloodStockMutation::latest()
            ->take(5)
            ->get();

        // 6. Donors Ready for Recall (> 60 days)
        $eligibleForRecallCount = Donor::where('verification_status', 'verified')
            ->where(function ($q) {
                $q->whereNull('last_donation_date')
                  ->orWhere('last_donation_date', '<=', now()->subDays(60));
            })->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'total_stock_units' => $totalStockUnits,
                'total_donors' => $totalDonors,
                'verified_donors' => $verifiedDonors,
                'pending_verification_donors' => $pendingVerificationDonors,
                'active_events_count' => $activeEventsCount,
                'pending_requests_count' => $pendingRequestsCount,
                'emergency_requests_count' => $emergencyRequestsCount,
                'eligible_recall_count' => $eligibleForRecallCount,
            ],
            'stockOverview' => $stockOverview,
            'recentRequests' => $recentRequests,
            'recentEvents' => $recentEvents,
            'recentMutations' => $recentMutations,
        ]);
    }
}

