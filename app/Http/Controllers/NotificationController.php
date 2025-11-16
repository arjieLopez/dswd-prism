<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivity;
use App\Constants\PaginationConstants;
use App\Constants\ActivityConstants;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $filterType = $request->get('filter_type', 'all');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $query = UserActivity::where('user_id', $user->id);

        if ($filterType === 'today') {
            $query->whereDate('created_at', now()->toDateString());
        } elseif ($filterType === 'this_week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($filterType === 'earlier') {
            $query->where('created_at', '<', now()->startOfWeek());
        } elseif ($filterType === 'custom' && $dateFrom && $dateTo) {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay(),
                \Carbon\Carbon::createFromFormat('Y-m-d', $dateTo)->endOfDay(),
            ]);
        } // 'all' shows everything

        $allActivities = $query->orderByDesc('created_at')->paginate(PaginationConstants::DEFAULT_PER_PAGE);

        // For header bell icon
        $recentActivities = UserActivity::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(ActivityConstants::RECENT_ACTIVITY_LIMIT)
            ->get();

        return view('notifications.all', compact('allActivities', 'recentActivities', 'filterType', 'dateFrom', 'dateTo'));
    }
}
