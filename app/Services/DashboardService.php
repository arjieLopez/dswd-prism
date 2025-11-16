<?php

namespace App\Services;

use App\Models\PurchaseRequest;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get date range based on filter type
     *
     * @param string $filterType
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return array
     */
    public function getDateRange(string $filterType, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        if ($filterType === 'this_month') {
            $currentMonth = Carbon::now();
            return [
                'start' => $currentMonth->copy()->startOfMonth(),
                'end' => $currentMonth->copy()->endOfMonth(),
                'display' => $currentMonth->format('F Y')
            ];
        }

        if ($filterType === 'previous_month') {
            $currentMonth = Carbon::now()->subMonth();
            return [
                'start' => $currentMonth->copy()->startOfMonth(),
                'end' => $currentMonth->copy()->endOfMonth(),
                'display' => $currentMonth->format('F Y')
            ];
        }

        if ($filterType === 'custom' && $dateFrom && $dateTo) {
            $start = Carbon::parse($dateFrom)->startOfDay();
            $end = Carbon::parse($dateTo)->endOfDay();
            return [
                'start' => $start,
                'end' => $end,
                'display' => $start->format('M j, Y') . ' - ' . $end->format('M j, Y')
            ];
        }

        // Default to this month
        $currentMonth = Carbon::now();
        return [
            'start' => $currentMonth->copy()->startOfMonth(),
            'end' => $currentMonth->copy()->endOfMonth(),
            'display' => $currentMonth->format('F Y')
        ];
    }

    /**
     * Get purchase request statistics
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getPRStatistics(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $query = PurchaseRequest::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total' => (clone $query)->count(),
            'draft' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'draft'))->count(),
            'pending' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'pending'))->count(),
            'approved' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'approved'))->count(),
            'rejected' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'rejected'))->count(),
            'po_generated' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'po_generated'))->count(),
            'completed' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'completed'))->count(),
        ];
    }

    /**
     * Get system-wide PR statistics for staff/admin
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getSystemPRStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $query = PurchaseRequest::whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total' => (clone $query)->count(),
            'draft' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'draft'))->count(),
            'pending' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'pending'))->count(),
            'approved' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'approved'))->count(),
            'rejected' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'rejected'))->count(),
            'po_generated' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'po_generated'))->count(),
            'completed' => (clone $query)->whereHas('status', fn($q) => $q->where('name', 'completed'))->count(),
        ];
    }

    /**
     * Get recent purchase requests
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentPurchaseRequests(int $userId, int $limit = 5)
    {
        return PurchaseRequest::where('user_id', $userId)
            ->with(['status', 'office', 'procurementMode'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get chart data for purchase requests
     *
     * @param int $userId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getPRChartData(int $userId, Carbon $startDate, Carbon $endDate): array
    {
        $days = $startDate->diffInDays($endDate) + 1;
        $labels = [];
        $data = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $labels[] = $date->format('M j');

            $count = PurchaseRequest::where('user_id', $userId)
                ->whereDate('created_at', $date)
                ->count();

            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}
