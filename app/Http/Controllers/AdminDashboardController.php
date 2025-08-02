<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PODocument;
use App\Models\UserActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function show()
    {
        // Get current year data for chart
        $currentYear = Carbon::now()->year;
        $labels = [];
        $prData = [];
        $poData = [];

        // Generate data for last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('M');

            // Count PRs for this month
            $prCount = PurchaseRequest::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $prData[] = $prCount;

            // Count POs for this month
            $poCount = PODocument::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $poData[] = $poCount;
        }

        // Get total counts
        $totalPRs = PurchaseRequest::count();
        $totalPOs = PODocument::count();

        // Get previous month counts for percentage calculation
        $lastMonth = Carbon::now()->subMonth();
        $lastMonthPRs = PurchaseRequest::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();
        $lastMonthPOs = PODocument::whereYear('created_at', $lastMonth->year)
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $currentMonthPRs = PurchaseRequest::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $currentMonthPOs = PODocument::whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        // Calculate percentage changes
        $prPercentageChange = $lastMonthPRs > 0 ? round((($currentMonthPRs - $lastMonthPRs) / $lastMonthPRs) * 100) : 0;
        $poPercentageChange = $lastMonthPOs > 0 ? round((($currentMonthPOs - $lastMonthPOs) / $lastMonthPOs) * 100) : 0;

        // Get recent activities (combine PRs and POs)
        $recentActivities = collect();

        // Get recent PRs
        $recentPRs = PurchaseRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($pr) {
                return [
                    'type' => 'PR',
                    'document_number' => $pr->pr_number,
                    'action' => $this->getActionForPR($pr->status),
                    'requesting_unit' => $pr->user->name ?? 'Unknown',
                    'status' => $pr->status,
                    'date' => $pr->created_at,
                    'status_color' => $this->getStatusColor($pr->status)
                ];
            });

        // Get recent POs
        $recentPOs = PODocument::orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($po) {
                return [
                    'type' => 'PO',
                    'document_number' => $po->po_number,
                    'action' => 'Generated',
                    'requesting_unit' => 'GSO Division',
                    'status' => 'Generated',
                    'date' => $po->created_at,
                    'status_color' => 'bg-green-100 text-green-800'
                ];
            });

        // Combine and sort by date
        $recentActivities = $recentPRs->concat($recentPOs)
            ->sortByDesc('date')
            ->take(5);

        return view('admin.admin_dashboard', compact(
            'labels',
            'prData',
            'poData',
            'totalPRs',
            'totalPOs',
            'prPercentageChange',
            'poPercentageChange',
            'recentActivities'
        ));
    }

    private function getActionForPR($status)
    {
        return match ($status) {
            'draft' => 'Created',
            'pending' => 'Submitted',
            'under_review' => 'Under Review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'po_generated' => 'Converted to PO',
            default => 'Updated'
        };
    }

    private function getStatusColor($status)
    {
        return match ($status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'under_review' => 'bg-blue-100 text-blue-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'po_generated' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
