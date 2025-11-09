<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\PODocument;
use App\Models\UserActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
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

        // Get current year data for chart - now filtered based on date range
        $chartData = $this->getChartData($filterType, $startDate, $endDate);
        $labels = $chartData['labels'];
        $prData = $chartData['prData'];
        $poData = $chartData['poData'];

        // Get filtered counts and totals for cards - only approved, po_generated, and completed
        $prCount = PurchaseRequest::whereHas('status', function ($query) {
            $query->whereIn('name', ['approved', 'po_generated', 'completed']);
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
        $prTotal = PurchaseRequest::whereHas('status', function ($query) {
            $query->whereIn('name', ['approved', 'po_generated', 'completed']);
        })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $poCount = PurchaseOrder::whereBetween('generated_at', [$startDate, $endDate])
            ->count();
        $poTotal = PurchaseOrder::whereBetween('generated_at', [$startDate, $endDate])
            ->join('purchase_requests', 'purchase_orders.purchase_request_id', '=', 'purchase_requests.id')
            ->sum('purchase_requests.total');

        // Get previous period counts for percentage calculation
        $previousStartDate = $startDate->copy()->subMonth();
        $previousEndDate = $endDate->copy()->subMonth();

        $previousPrCount = PurchaseRequest::whereHas('status', function ($query) {
            $query->whereIn('name', ['approved', 'po_generated', 'completed']);
        })
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->count();
        $previousPoCount = PurchaseOrder::whereBetween('generated_at', [$previousStartDate, $previousEndDate])
            ->count();

        // Calculate percentage changes
        if ($previousPrCount == 0 && $prCount > 0) {
            $prPercentageChange = 100;
        } elseif ($previousPrCount == 0 && $prCount == 0) {
            $prPercentageChange = 0;
        } elseif ($previousPrCount > 0 && $prCount == 0) {
            $prPercentageChange = -100;
        } else {
            $prPercentageChange = round((($prCount - $previousPrCount) / $previousPrCount) * 100);
        }

        if ($previousPoCount == 0 && $poCount > 0) {
            $poPercentageChange = 100;
        } elseif ($previousPoCount == 0 && $poCount == 0) {
            $poPercentageChange = 0;
        } elseif ($previousPoCount > 0 && $poCount == 0) {
            $poPercentageChange = -100;
        } else {
            $poPercentageChange = round((($poCount - $previousPoCount) / $previousPoCount) * 100);
        }

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
                    'requesting_unit' => $pr->user
                        ? ($pr->user->first_name . ($pr->user->middle_name ? ' ' . $pr->user->middle_name : '') . ' ' . $pr->user->last_name)
                        : 'Unknown',
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
        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.admin_dashboard', compact(
            'labels',
            'prData',
            'poData',
            'prCount',
            'poCount',
            'prTotal',
            'poTotal',
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
            'completed' => 'bg-indigo-100 text-indigo-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    private function getChartData($filterType, $startDate, $endDate)
    {
        $labels = [];
        $prData = [];
        $poData = [];

        if ($filterType === 'this_month' || $filterType === 'previous_month') {
            // Default 6 months view
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->format('M');

                $prData[] = PurchaseRequest::whereHas('status', function ($query) {
                    $query->whereIn('name', ['approved', 'po_generated', 'completed']);
                })
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $poData[] = PurchaseOrder::whereYear('generated_at', $date->year)
                    ->whereMonth('generated_at', $date->month)
                    ->count();
            }
        } elseif ($filterType === 'custom') {
            // Calculate the difference in months
            $monthsDiff = $startDate->diffInMonths($endDate);

            if ($monthsDiff == 0) {
                // Single month - show that month and 5 months before
                $targetMonth = $startDate->copy();
                for ($i = 5; $i >= 0; $i--) {
                    $date = $targetMonth->copy()->subMonths($i);
                    $labels[] = $date->format('M Y');

                    $prData[] = PurchaseRequest::whereHas('status', function ($query) {
                        $query->whereIn('name', ['approved', 'po_generated', 'completed']);
                    })
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->count();

                    $poData[] = PurchaseOrder::whereYear('generated_at', $date->year)
                        ->whereMonth('generated_at', $date->month)
                        ->count();
                }
            } elseif ($monthsDiff <= 12) {
                // Less than or equal to 12 months - show monthly data from start to end
                $currentDate = $startDate->copy()->startOfMonth();
                $endMonth = $endDate->copy()->endOfMonth();

                while ($currentDate <= $endMonth) {
                    $labels[] = $currentDate->format('M Y');

                    $prData[] = PurchaseRequest::whereHas('status', function ($query) {
                        $query->whereIn('name', ['approved', 'po_generated', 'completed']);
                    })
                        ->whereYear('created_at', $currentDate->year)
                        ->whereMonth('created_at', $currentDate->month)
                        ->count();

                    $poData[] = PurchaseOrder::whereYear('generated_at', $currentDate->year)
                        ->whereMonth('generated_at', $currentDate->month)
                        ->count();

                    $currentDate->addMonth();
                }
            } else {
                // More than 12 months - show by year with 6-month spans
                $startYear = $startDate->year;
                $endYear = $endDate->year;

                for ($year = $startYear; $year <= $endYear; $year++) {
                    // First half of the year (Jan-Jun)
                    $firstHalfStart = Carbon::create($year, 1, 1);
                    $firstHalfEnd = Carbon::create($year, 6, 30)->endOfDay();

                    if ($firstHalfStart <= $endDate && $firstHalfEnd >= $startDate) {
                        $labels[] = $year . ' H1';

                        $prData[] = PurchaseRequest::whereHas('status', function ($query) {
                            $query->whereIn('name', ['approved', 'po_generated', 'completed']);
                        })
                            ->whereBetween('created_at', [
                                max($firstHalfStart, $startDate),
                                min($firstHalfEnd, $endDate)
                            ])->count();

                        $poData[] = PurchaseOrder::whereBetween('generated_at', [
                            max($firstHalfStart, $startDate),
                            min($firstHalfEnd, $endDate)
                        ])->count();
                    }

                    // Second half of the year (Jul-Dec)
                    $secondHalfStart = Carbon::create($year, 7, 1);
                    $secondHalfEnd = Carbon::create($year, 12, 31)->endOfDay();

                    if ($secondHalfStart <= $endDate && $secondHalfEnd >= $startDate) {
                        $labels[] = $year . ' H2';

                        $prData[] = PurchaseRequest::whereHas('status', function ($query) {
                            $query->whereIn('name', ['approved', 'po_generated', 'completed']);
                        })
                            ->whereBetween('created_at', [
                                max($secondHalfStart, $startDate),
                                min($secondHalfEnd, $endDate)
                            ])->count();

                        $poData[] = PurchaseOrder::whereBetween('generated_at', [
                            max($secondHalfStart, $startDate),
                            min($secondHalfEnd, $endDate)
                        ])->count();
                    }
                }
            }
        }

        return [
            'labels' => $labels,
            'prData' => $prData,
            'poData' => $poData,
        ];
    }
}
