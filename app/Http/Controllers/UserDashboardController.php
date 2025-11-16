<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\UserActivity;
use App\Models\User;
use Carbon\Carbon;
use App\Constants\PaginationConstants;
use App\Constants\ActivityConstants;
use App\Services\DashboardService;

class UserDashboardController extends Controller
{
    public function show(Request $request)
    {
        $user = auth()->user();
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

        // Get PR counts and total amounts for the current user (selected date range)
        $totalPRs = $user->purchaseRequests()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $draftPRs = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['draft']);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $draftTotal = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['draft']);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $approvedPRs = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['approved', 'po_generated']);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $approvedTotal = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['approved', 'po_generated']);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $pendingPRs = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->where('name', 'pending');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $pendingTotal = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->where('name', 'pending');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $rejectedPRs = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->where('name', 'rejected');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $rejectedTotal = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->where('name', 'rejected');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        $completedPRs = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->where('name', 'completed');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $completedTotal = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->where('name', 'completed');
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total');

        // Get last month's data for each status
        $lastMonthTotal = $user->purchaseRequests()
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $lastMonthDraft = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->where('name', 'draft');
            })
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $lastMonthApproved = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->whereIn('name', ['approved', 'po_generated']);
            })
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $lastMonthPending = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->where('name', 'pending');
            })
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $lastMonthRejected = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->where('name', 'rejected');
            })
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();
        $lastMonthCompleted = $user->purchaseRequests()
            ->whereHas('status', function ($query) {
                $query->where('name', 'completed');
            })
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        // Calculate percentage changes for each status
        $totalPercentageChange = $this->calculatePercentageChange($totalPRs, $lastMonthTotal);
        $approvedPercentageChange = $this->calculatePercentageChange($approvedPRs, $lastMonthApproved);
        $draftPercentageChange = $this->calculatePercentageChange($draftPRs, $lastMonthDraft);
        $pendingPercentageChange = $this->calculatePercentageChange($pendingPRs, $lastMonthPending);
        $rejectedPercentageChange = $this->calculatePercentageChange($rejectedPRs, $lastMonthRejected);
        $completedPercentageChange = $this->calculatePercentageChange($completedPRs, $lastMonthCompleted);

        // Chart data based on filter type
        $chartData = $this->getChartData($user, $filterType, $startDate, $endDate);
        $labels = $chartData['labels'];
        $approvePR = $chartData['approvePR'];
        $pendingPR = $chartData['pendingPR'];
        $rejectPR = $chartData['rejectPR'];
        $completedPR = $chartData['completedPR'];

        // Debug info (remove in production)
        // \Log::info('Chart Data Debug', [
        //     'filterType' => $filterType,
        //     'startDate' => $startDate->format('Y-m-d'),
        //     'endDate' => $endDate->format('Y-m-d'),
        //     'labels' => $labels,
        //     'monthsDiff' => $startDate->diffInMonths($endDate)
        // ]);

        // Get recent activities (increase limit for notifications)
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(ActivityConstants::RECENT_ACTIVITY_LIMIT)
            ->get();

        return view('user.requestingUnit_dashboard', compact(
            'totalPRs',
            'draftPRs',
            'draftTotal',
            'approvedPRs',
            'approvedTotal',
            'pendingPRs',
            'pendingTotal',
            'rejectedPRs',
            'rejectedTotal',
            'completedPRs',
            'completedTotal',
            'totalPercentageChange',
            'draftPercentageChange',
            'approvedPercentageChange',
            'pendingPercentageChange',
            'rejectedPercentageChange',
            'completedPercentageChange',
            'labels',
            'approvePR',
            'pendingPR',
            'rejectPR',
            'completedPR',
            'recentActivities',
            'filterType',
            'dateFrom',
            'dateTo'
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

    private function getChartData($user, $filterType, $startDate, $endDate)
    {
        $labels = [];
        $approvePR = [];
        $pendingPR = [];
        $rejectPR = [];
        $completedPR = [];

        if ($filterType === 'this_month' || $filterType === 'previous_month') {
            // Default 6 months view
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->format('M');

                $approvePR[] = $user->purchaseRequests()
                    ->whereHas('status', function ($query) {
                        $query->whereIn('name', ['approved', 'po_generated']);
                    })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();

                $pendingPR[] = $user->purchaseRequests()
                    ->whereHas('status', function ($query) {
                        $query->where('name', 'pending');
                    })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();

                $rejectPR[] = $user->purchaseRequests()
                    ->whereHas('status', function ($query) {
                        $query->where('name', 'rejected');
                    })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();

                $completedPR[] = $user->purchaseRequests()
                    ->whereHas('status', function ($query) {
                        $query->where('name', 'completed');
                    })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count();
            }
        } elseif ($filterType === 'custom') {
            // Calculate the difference in months
            $monthsDiff = $startDate->diffInMonths($endDate);

            if ($monthsDiff == 0) {
                // Single month - show that month and 6 months before
                $targetMonth = $startDate->copy();
                for ($i = 5; $i >= 0; $i--) {
                    $date = $targetMonth->copy()->subMonths($i);
                    $labels[] = $date->format('M Y');

                    $approvePR[] = $user->purchaseRequests()
                        ->whereHas('status', function ($query) {
                            $query->whereIn('name', ['approved', 'po_generated']);
                        })
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count();

                    $pendingPR[] = $user->purchaseRequests()
                        ->whereHas('status', function ($query) {
                            $query->where('name', 'pending');
                        })
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count();

                    $rejectPR[] = $user->purchaseRequests()
                        ->whereHas('status', function ($query) {
                            $query->where('name', 'rejected');
                        })
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count();

                    $completedPR[] = $user->purchaseRequests()
                        ->whereHas('status', function ($query) {
                            $query->where('name', 'completed');
                        })
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->count();
                }
            } elseif ($monthsDiff <= 12) {
                // Less than or equal to 12 months - show monthly data from start to end
                $currentDate = $startDate->copy()->startOfMonth();
                $endMonth = $endDate->copy()->endOfMonth();

                while ($currentDate <= $endMonth) {
                    $labels[] = $currentDate->format('M Y');

                    $approvePR[] = $user->purchaseRequests()
                        ->whereHas('status', function ($query) {
                            $query->whereIn('name', ['approved', 'po_generated']);
                        })
                        ->whereMonth('created_at', $currentDate->month)
                        ->whereYear('created_at', $currentDate->year)
                        ->count();

                    $pendingPR[] = $user->purchaseRequests()
                        ->whereHas('status', function ($query) {
                            $query->where('name', 'pending');
                        })
                        ->whereMonth('created_at', $currentDate->month)
                        ->whereYear('created_at', $currentDate->year)
                        ->count();

                    $rejectPR[] = $user->purchaseRequests()
                        ->whereHas('status', function ($query) {
                            $query->where('name', 'rejected');
                        })
                        ->whereMonth('created_at', $currentDate->month)
                        ->whereYear('created_at', $currentDate->year)
                        ->count();

                    $completedPR[] = $user->purchaseRequests()
                        ->whereHas('status', function ($query) {
                            $query->where('name', 'completed');
                        })
                        ->whereMonth('created_at', $currentDate->month)
                        ->whereYear('created_at', $currentDate->year)
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

                        $approvePR[] = $user->purchaseRequests()
                            ->whereHas('status', function ($query) {
                                $query->whereIn('name', ['approved', 'po_generated']);
                            })
                            ->whereBetween('created_at', [
                                max($firstHalfStart, $startDate),
                                min($firstHalfEnd, $endDate)
                            ])
                            ->count();

                        $pendingPR[] = $user->purchaseRequests()
                            ->whereHas('status', function ($query) {
                                $query->where('name', 'pending');
                            })
                            ->whereBetween('created_at', [
                                max($firstHalfStart, $startDate),
                                min($firstHalfEnd, $endDate)
                            ])
                            ->count();

                        $rejectPR[] = $user->purchaseRequests()
                            ->whereHas('status', function ($query) {
                                $query->where('name', 'rejected');
                            })
                            ->whereBetween('created_at', [
                                max($firstHalfStart, $startDate),
                                min($firstHalfEnd, $endDate)
                            ])
                            ->count();

                        $completedPR[] = $user->purchaseRequests()
                            ->whereHas('status', function ($query) {
                                $query->where('name', 'completed');
                            })
                            ->whereBetween('created_at', [
                                max($firstHalfStart, $startDate),
                                min($firstHalfEnd, $endDate)
                            ])
                            ->count();
                    }

                    // Second half of the year (Jul-Dec)
                    $secondHalfStart = Carbon::create($year, 7, 1);
                    $secondHalfEnd = Carbon::create($year, 12, 31)->endOfDay();

                    if ($secondHalfStart <= $endDate && $secondHalfEnd >= $startDate) {
                        $labels[] = $year . ' H2';

                        $approvePR[] = $user->purchaseRequests()
                            ->whereHas('status', function ($query) {
                                $query->whereIn('name', ['approved', 'po_generated']);
                            })
                            ->whereBetween('created_at', [
                                max($secondHalfStart, $startDate),
                                min($secondHalfEnd, $endDate)
                            ])
                            ->count();

                        $pendingPR[] = $user->purchaseRequests()
                            ->whereHas('status', function ($query) {
                                $query->where('name', 'pending');
                            })
                            ->whereBetween('created_at', [
                                max($secondHalfStart, $startDate),
                                min($secondHalfEnd, $endDate)
                            ])
                            ->count();

                        $rejectPR[] = $user->purchaseRequests()
                            ->whereHas('status', function ($query) {
                                $query->where('name', 'rejected');
                            })
                            ->whereBetween('created_at', [
                                max($secondHalfStart, $startDate),
                                min($secondHalfEnd, $endDate)
                            ])
                            ->count();

                        $completedPR[] = $user->purchaseRequests()
                            ->whereHas('status', function ($query) {
                                $query->where('name', 'completed');
                            })
                            ->whereBetween('created_at', [
                                max($secondHalfStart, $startDate),
                                min($secondHalfEnd, $endDate)
                            ])
                            ->count();
                    }
                }
            }
        }

        return [
            'labels' => $labels,
            'approvePR' => $approvePR,
            'pendingPR' => $pendingPR,
            'rejectPR' => $rejectPR,
            'completedPR' => $completedPR,
        ];
    }
}
