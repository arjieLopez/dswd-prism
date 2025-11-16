<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PODocument;
use App\Models\UserActivity;
use Carbon\Carbon;
use App\Constants\PaginationConstants;
use App\Constants\ActivityConstants;
use App\Services\DashboardService;

class GSODashboardController extends Controller
{
    public function show(Request $request)
    {
        $dashboardService = new DashboardService();

        // Get filter type and dates
        $filterType = $request->get('filter_type', 'this_month');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Get date range using DashboardService
        $dateRange = $dashboardService->getDateRange($filterType, $dateFrom, $dateTo);
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        $lastMonth = $startDate->copy()->subMonth();

        // Get PR statistics for selected date range
        $pendingPRs = PurchaseRequest::whereHas('status', function ($query) {
            $query->where('name', 'pending');
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $pendingTotal = PurchaseRequest::whereHas('status', function ($query) {
            $query->where('name', 'pending');
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $approvedPRs = PurchaseRequest::whereHas('status', function ($query) {
            $query->where('name', 'approved');
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $approvedTotal = PurchaseRequest::whereHas('status', function ($query) {
            $query->where('name', 'approved');
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $poGenerated = PurchaseOrder::whereBetween('generated_at', [$startDate, $endDate])
            ->count();
        $poGeneratedTotal = PurchaseOrder::whereBetween('generated_at', [$startDate, $endDate])
            ->join('purchase_requests', 'purchase_orders.purchase_request_id', '=', 'purchase_requests.id')
            ->sum('purchase_requests.total');

        $completedPRs = PurchaseOrder::whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->count();
        $completedTotal = PurchaseOrder::whereNotNull('completed_at')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->join('purchase_requests', 'purchase_orders.purchase_request_id', '=', 'purchase_requests.id')
            ->sum('purchase_requests.total');

        // Get PR statistics for last month (for percentage calculation)
        $lastMonthStartDate = $lastMonth->copy()->startOfMonth();
        $lastMonthEndDate = $lastMonth->copy()->endOfMonth();

        $lastMonthPendingPRs = PurchaseRequest::whereHas('status', function ($query) {
            $query->where('name', 'pending');
        })
            ->whereBetween('created_at', [$lastMonthStartDate, $lastMonthEndDate])
            ->count();

        $lastMonthApprovedPRs = PurchaseRequest::whereHas('status', function ($query) {
            $query->where('name', 'approved');
        })
            ->whereBetween('created_at', [$lastMonthStartDate, $lastMonthEndDate])
            ->count();

        $lastMonthPOGenerated = PurchaseOrder::whereBetween('generated_at', [$lastMonthStartDate, $lastMonthEndDate])
            ->count();

        $lastMonthCompletedPRs = PurchaseOrder::whereNotNull('completed_at')
            ->whereBetween('completed_at', [$lastMonthStartDate, $lastMonthEndDate])
            ->count();

        // Calculate percentage changes
        $pendingPercentageChange = $this->calculatePercentageChange($pendingPRs, $lastMonthPendingPRs);
        $approvedPercentageChange = $this->calculatePercentageChange($approvedPRs, $lastMonthApprovedPRs);
        $poGeneratedPercentageChange = $this->calculatePercentageChange($poGenerated, $lastMonthPOGenerated);
        $completedPercentageChange = $this->calculatePercentageChange($completedPRs, $lastMonthCompletedPRs);

        // Get completed Purchase Orders (both completed and pending completion)
        $completedPRsList = PurchaseOrder::with(['purchaseRequest.user', 'supplier'])
            ->join('purchase_requests', 'purchase_orders.purchase_request_id', '=', 'purchase_requests.id')
            ->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->join('statuses', 'purchase_requests.status_id', '=', 'statuses.id')
            ->select([
                'purchase_orders.*',
                'purchase_requests.pr_number',
                'purchase_requests.total',
                'statuses.name as pr_status',
                'suppliers.supplier_name'
            ])
            ->orderBy('purchase_orders.generated_at', 'desc')
            ->paginate(5);

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(ActivityConstants::RECENT_ACTIVITY_LIMIT)
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
