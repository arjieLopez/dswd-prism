<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PODocument;
use App\Models\UserActivity;
use Carbon\Carbon;

class GSODashboardController extends Controller
{
    public function show(Request $request)
    {
        // Get filter type and dates
        $filterType = $request->get('filter_type', 'this_month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Set date range based on filter type
        if ($filterType === 'this_month') {
            $currentMonth = Carbon::now();
            $startDate = $currentMonth->copy()->startOfMonth();
            $endDate = $currentMonth->copy()->endOfMonth();
        } elseif ($filterType === 'previous_month') {
            $currentMonth = Carbon::now()->subMonth();
            $startDate = $currentMonth->copy()->startOfMonth();
            $endDate = $currentMonth->copy()->endOfMonth();
        } elseif ($filterType === 'custom' && $dateFrom && $dateTo) {
            $startDate = Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay();
            $endDate = Carbon::createFromFormat('Y-m-d', $dateTo)->endOfDay();
            $currentMonth = $startDate; // For reference
        } else {
            // Default to this month
            $currentMonth = Carbon::now();
            $startDate = $currentMonth->copy()->startOfMonth();
            $endDate = $currentMonth->copy()->endOfMonth();
        }

        $lastMonth = $currentMonth->copy()->subMonth();

        // Get PR statistics for selected date range
        $pendingPRs = PurchaseRequest::where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $pendingTotal = PurchaseRequest::where('status', 'pending')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $approvedPRs = PurchaseRequest::where('status', 'approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $approvedTotal = PurchaseRequest::where('status', 'approved')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $poGenerated = PurchaseRequest::where('status', 'po_generated')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();
        $poGeneratedTotal = PurchaseRequest::where('status', 'po_generated')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum('total');

        $completedPRs = PurchaseRequest::where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->count();
        $completedTotal = PurchaseRequest::where('status', 'completed')
            ->whereBetween('updated_at', [$startDate, $endDate])
            ->sum('total');

        // Get PR statistics for last month (for percentage calculation)
        $lastMonthStartDate = $lastMonth->copy()->startOfMonth();
        $lastMonthEndDate = $lastMonth->copy()->endOfMonth();

        $lastMonthPendingPRs = PurchaseRequest::where('status', 'pending')
            ->whereBetween('created_at', [$lastMonthStartDate, $lastMonthEndDate])
            ->count();

        $lastMonthApprovedPRs = PurchaseRequest::where('status', 'approved')
            ->whereBetween('created_at', [$lastMonthStartDate, $lastMonthEndDate])
            ->count();

        $lastMonthPOGenerated = PurchaseRequest::where('status', 'po_generated')
            ->whereBetween('updated_at', [$lastMonthStartDate, $lastMonthEndDate])
            ->count();

        $lastMonthCompletedPRs = PurchaseRequest::where('status', 'completed')
            ->whereBetween('updated_at', [$lastMonthStartDate, $lastMonthEndDate])
            ->count();

        // Calculate percentage changes
        $pendingPercentageChange = $this->calculatePercentageChange($pendingPRs, $lastMonthPendingPRs);
        $approvedPercentageChange = $this->calculatePercentageChange($approvedPRs, $lastMonthApprovedPRs);
        $poGeneratedPercentageChange = $this->calculatePercentageChange($poGenerated, $lastMonthPOGenerated);
        $completedPercentageChange = $this->calculatePercentageChange($completedPRs, $lastMonthCompletedPRs);

        // Get completed Purchase Requests
        $completedPRsList = PurchaseRequest::whereIn('status', ['completed', 'po_generated'])
            ->with(['user', 'supplier']) // Add supplier relationship
            ->orderBy('updated_at', 'desc')
            ->paginate(5);

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('staff.gso_dashboard', compact(
            'pendingPRs',
            'pendingTotal',
            'approvedPRs',
            'approvedTotal',
            'completedPRs',
            'completedTotal',
            'poGenerated',
            'poGeneratedTotal',
            'pendingPercentageChange',
            'approvedPercentageChange',
            'completedPercentageChange',
            'poGeneratedPercentageChange',
            'completedPRsList',
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
