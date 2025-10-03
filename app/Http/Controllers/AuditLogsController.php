<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuditLogsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = UserActivity::with('user')
                ->select([
                    'user_activities.*',
                    DB::raw("CONCAT(users.first_name, ' ', IFNULL(users.middle_name, ''), ' ', users.last_name) as user_name"),
                    'users.role as user_role'
                ])
                ->join('users', 'user_activities.user_id', '=', 'users.id');

            // Search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where(DB::raw("CONCAT(users.first_name, ' ', IFNULL(users.middle_name, ''), ' ', users.last_name) as user_name"), 'like', "%{$search}%")
                        ->orWhere('user_activities.description', 'like', "%{$search}%")
                        ->orWhere('user_activities.pr_number', 'like', "%{$search}%")
                        ->orWhere('users.role', 'like', "%{$search}%");
                });
            }

            // Filter by action type
            if ($request->filled('action') && $request->action !== 'all') {
                $query->where('user_activities.action', $request->action);
            }

            // Filter by user role
            if ($request->filled('role') && $request->role !== 'all') {
                $query->where('users.role', $request->role);
            }

            // Filter by date range
            if ($request->filled('date_from')) {
                $query->whereDate('user_activities.created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('user_activities.created_at', '<=', $request->date_to);
            }

            $auditLogs = $query->orderBy('user_activities.created_at', 'desc')->paginate(10);

            // Get available actions and roles for filters
            $actions = UserActivity::select('action')->distinct()->pluck('action');
            $roles = User::select('role')->distinct()->pluck('role');

            $user = auth()->user();
            $recentActivities = $user->activities()
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            return view('admin.audit_logs', compact('auditLogs', 'actions', 'roles', 'recentActivities'));
        } catch (\Exception $e) {
            // If there's an error, return empty results
            $auditLogs = collect([])->paginate(10);
            $actions = collect([]);
            $roles = collect([]);

            return view('admin.audit_logs', compact('auditLogs', 'actions', 'roles'));
        }
    }

    public function export(Request $request)
    {
        // Export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
