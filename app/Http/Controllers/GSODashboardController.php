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
        $pendingPRs = PurchaseRequest::where('status', 'pending')->count();
        $approvedPRs = PurchaseRequest::where('status', 'approved')->count();
        $poGenerated = PurchaseRequest::where('status', 'po_generated')->count();

        // Get PR statistics for last month
        $lastMonthPendingPRs = PurchaseRequest::where('status', 'pending')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $lastMonthApprovedPRs = PurchaseRequest::where('status', 'approved')
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $lastMonthPOGenerated = PurchaseRequest::where('status', 'po_generated')
            ->whereMonth('po_generated_at', $lastMonth->month)
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
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }
}
