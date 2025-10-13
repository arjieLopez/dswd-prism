<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserActivity;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

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

    public function exportXlsx(Request $request)
    {
        $query = UserActivity::with('user')
            ->select([
                'user_activities.*',
                DB::raw("CONCAT(users.first_name, ' ', IFNULL(users.middle_name, ''), ' ', users.last_name) as user_name"),
                'users.role as user_role'
            ])
            ->join('users', 'user_activities.user_id', '=', 'users.id');

        // Apply same filters as index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw("CONCAT(users.first_name, ' ', IFNULL(users.middle_name, ''), ' ', users.last_name)"), 'like', "%{$search}%")
                    ->orWhere('user_activities.description', 'like', "%{$search}%")
                    ->orWhere('user_activities.pr_number', 'like', "%{$search}%")
                    ->orWhere('users.role', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('user_activities.action', $request->action);
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('users.role', $request->role);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('user_activities.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('user_activities.created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->orderBy('user_activities.created_at', 'desc')->get();

        // Create CSV content
        $csvContent = [];
        $csvContent[] = [
            '#',
            'Timestamp',
            'User',
            'Role',
            'Action'
        ];

        foreach ($auditLogs as $index => $log) {
            // Combine action description and PR number like in the blade
            $actionText = $log->description;
            if ($log->pr_number) {
                $actionText .= ' (' . $log->pr_number . ')';
            }

            $csvContent[] = [
                $index + 1,
                $log->created_at->format('F j, Y g:i A'),
                $log->user_name,
                ucfirst($log->user_role),
                $actionText
            ];
        }

        // Create CSV file
        $filename = 'audit_logs_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://temp', 'r+');

        foreach ($csvContent as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csvData = stream_get_contents($handle);
        fclose($handle);

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function exportPdf(Request $request)
    {
        $query = UserActivity::with('user')
            ->select([
                'user_activities.*',
                DB::raw("CONCAT(users.first_name, ' ', IFNULL(users.middle_name, ''), ' ', users.last_name) as user_name"),
                'users.role as user_role'
            ])
            ->join('users', 'user_activities.user_id', '=', 'users.id');

        // Apply same filters as index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw("CONCAT(users.first_name, ' ', IFNULL(users.middle_name, ''), ' ', users.last_name)"), 'like', "%{$search}%")
                    ->orWhere('user_activities.description', 'like', "%{$search}%")
                    ->orWhere('user_activities.pr_number', 'like', "%{$search}%")
                    ->orWhere('users.role', 'like', "%{$search}%");
            });
        }

        if ($request->filled('action') && $request->action !== 'all') {
            $query->where('user_activities.action', $request->action);
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('users.role', $request->role);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('user_activities.created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('user_activities.created_at', '<=', $request->date_to);
        }

        $auditLogs = $query->orderBy('user_activities.created_at', 'desc')->get();

        // Prepare filter summary
        $filterSummary = [];
        if ($request->filled('search')) {
            $filterSummary[] = 'Search: "' . $request->search . '"';
        }
        if ($request->filled('action') && $request->action !== 'all') {
            $filterSummary[] = 'Action: ' . ucfirst(str_replace('_', ' ', $request->action));
        }
        if ($request->filled('role') && $request->role !== 'all') {
            $filterSummary[] = 'Role: ' . ucfirst($request->role);
        }
        if ($request->filled('date_from') || $request->filled('date_to')) {
            $dateRange = '';
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $dateRange = \Carbon\Carbon::parse($request->date_from)->format('M d, Y') . ' - ' . \Carbon\Carbon::parse($request->date_to)->format('M d, Y');
            } elseif ($request->filled('date_from')) {
                $dateRange = 'From ' . \Carbon\Carbon::parse($request->date_from)->format('M d, Y');
            } else {
                $dateRange = 'Until ' . \Carbon\Carbon::parse($request->date_to)->format('M d, Y');
            }
            $filterSummary[] = 'Date: ' . $dateRange;
        }

        $data = [
            'auditLogs' => $auditLogs,
            'filterSummary' => $filterSummary,
            'totalLogs' => $auditLogs->count(),
            'exportDate' => now()->format('F j, Y g:i A')
        ];

        $pdf = Pdf::loadView('exports.audit_logs_pdf', $data);
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('audit_logs_' . date('Y_m_d_H_i_s') . '.pdf');
    }
}
