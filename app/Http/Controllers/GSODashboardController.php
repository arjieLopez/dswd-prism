<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PODocument;
use Carbon\Carbon;

class GSODashboardController extends Controller
{
    public function show()
    {
        // Get current month and last month
        $currentMonth = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        // Get PR statistics for current month
        $pendingPRs = PurchaseRequest::where('status', 'pending')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
        $approvedPRs = PurchaseRequest::where('status', 'approved')
            ->whereMonth('created_at', $currentMonth->month)
            ->whereYear('created_at', $currentMonth->year)
            ->count();
        $poGenerated = PurchaseRequest::whereMonth('po_generated_at', $currentMonth->month)
            ->whereYear('po_generated_at', $currentMonth->year)
            ->count();

        // Get PR statistics for last month
        $lastMonthPendingPRs = PurchaseRequest::where('status', 'pending')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $lastMonthApprovedPRs = PurchaseRequest::where('status', 'approved')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $lastMonthPOGenerated = PurchaseRequest::whereMonth('po_generated_at', $lastMonth->month)
            ->whereYear('po_generated_at', $lastMonth->year)
            ->count();

        // Calculate percentage changes
        $pendingPercentageChange = $this->calculatePercentageChange($pendingPRs, $lastMonthPendingPRs);
        $approvedPercentageChange = $this->calculatePercentageChange($approvedPRs, $lastMonthApprovedPRs);
        $poGeneratedPercentageChange = $this->calculatePercentageChange($poGenerated, $lastMonthPOGenerated);

        // Get generated Purchase Orders
        $generatedPOs = PurchaseRequest::where('status', 'po_generated')
            ->with(['user', 'supplier']) // Add supplier relationship
            ->orderBy('po_generated_at', 'desc')
            ->paginate(10);

        return view('staff.gso_dashboard', compact(
            'pendingPRs',
            'approvedPRs',
            'poGenerated',
            'pendingPercentageChange',
            'approvedPercentageChange',
            'poGeneratedPercentageChange',
            'generatedPOs'
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
