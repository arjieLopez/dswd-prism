<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Get PR counts for the current user
        $totalPRs = $user->purchaseRequests()
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
        $approvedPRs = $user->purchaseRequests()
            ->whereIn('status', ['approved', 'po_generated'])
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
        $pendingPRs = $user->purchaseRequests()
            ->where('status', 'pending')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
        $rejectedPRs = $user->purchaseRequests()
            ->where('status', 'rejected')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();

        // Get last month's data for each status
        $lastMonthTotal = $user->purchaseRequests()
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $lastMonthApproved = $user->purchaseRequests()
            ->whereIn('status', ['approved', 'po_generated'])
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $lastMonthPending = $user->purchaseRequests()
            ->where('status', 'pending')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $lastMonthRejected = $user->purchaseRequests()
            ->where('status', 'rejected')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        // Calculate percentage changes for each status
        $totalPercentageChange = $this->calculatePercentageChange($totalPRs, $lastMonthTotal);
        $approvedPercentageChange = $this->calculatePercentageChange($approvedPRs, $lastMonthApproved);
        $pendingPercentageChange = $this->calculatePercentageChange($pendingPRs, $lastMonthPending);
        $rejectedPercentageChange = $this->calculatePercentageChange($rejectedPRs, $lastMonthRejected);

        // Monthly data for chart (last 6 months)
        $labels = [];
        $approvePR = [];
        $pendingPR = [];
        $rejectPR = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M');

            $approvePR[] = $user->purchaseRequests()
                ->whereIn('status', ['approved', 'po_generated'])
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $pendingPR[] = $user->purchaseRequests()
                ->where('status', 'pending')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();

            $rejectPR[] = $user->purchaseRequests()
                ->where('status', 'rejected')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }

        // Get recent activities (increase limit for notifications)
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(15) // Increased from 10 to 15
            ->get();

        return view('user.requestingUnit_dashboard', compact(
            'totalPRs',
            'approvedPRs',
            'pendingPRs',
            'rejectedPRs',
            'totalPercentageChange',
            'approvedPercentageChange',
            'pendingPercentageChange',
            'rejectedPercentageChange',
            'labels',
            'approvePR',
            'pendingPR',
            'rejectPR',
            'recentActivities'
        ));
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0 && $current > 0) {
            return 100;
        } elseif ($previous == 0 && $current == 0) {
            return 0;
        } elseif ($previous > 0 && $current == 0) {
            return -100;
        } else {
            return round((($current - $previous) / $previous) * 100);
        }
    }
}
