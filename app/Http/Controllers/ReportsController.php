<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PODocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Get PRs that are NOT po_generated
        $prQuery = PurchaseRequest::with('user')
            ->where('purchase_requests.status', '!=', 'po_generated')
            ->select([
                'purchase_requests.id',
                'purchase_requests.pr_number',
                'purchase_requests.total',
                'purchase_requests.status',
                'purchase_requests.created_at',
                'purchase_requests.updated_at',
                DB::raw("'PR' as type"),
                DB::raw("purchase_requests.pr_number as document_number"),
                DB::raw("CONCAT(users.first_name, ' ', IFNULL(users.middle_name, ''), ' ', users.last_name) as department"),
                DB::raw("purchase_requests.total as amount"),
                DB::raw("purchase_requests.created_at as date_created"),
                DB::raw("purchase_requests.updated_at as date_edited")
            ])
            ->join('users', 'purchase_requests.user_id', '=', 'users.id');

        // Get PO data for PRs that are po_generated
        $poQuery = PurchaseRequest::with('user')
            ->where('purchase_requests.status', 'po_generated')
            ->select([
                'purchase_requests.id',
                'purchase_requests.pr_number',
                'purchase_requests.total',
                'purchase_requests.status',
                'purchase_requests.po_generated_at',
                'purchase_requests.updated_at',
                DB::raw("'PO' as type"),
                DB::raw("purchase_requests.po_number as document_number"),
                DB::raw("CONCAT(users.first_name, ' ', IFNULL(users.middle_name, ''), ' ', users.last_name) as department"),
                DB::raw("purchase_requests.total as amount"),
                DB::raw("purchase_requests.po_generated_at as date_created"),
                DB::raw("purchase_requests.updated_at as date_edited")
            ])
            ->join('users', 'purchase_requests.user_id', '=', 'users.id');

        // Apply search filters to both queries
        if ($request->filled('search')) {
            $search = $request->search;
            $prQuery->where(function ($q) use ($search) {
                $q->where('purchase_requests.pr_number', 'like', "%{$search}%")
                    ->orWhere(DB::raw("CONCAT(users.first_name, ' ', IFNULL(users.middle_name, ''), ' ', users.last_name)"), 'like', "%{$search}%")
                    ->orWhere('purchase_requests.status', 'like', "%{$search}%");
            });

            $poQuery->where(function ($q) use ($search) {
                $q->where('purchase_requests.pr_number', 'like', "%{$search}%")
                    ->orWhere('purchase_requests.po_number', 'like', "%{$search}%")
                    ->orWhere(DB::raw("CONCAT(users.first_name, ' ', IFNULL(users.middle_name, ''), ' ', users.last_name)"), 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'po_generated') {
                // Only show PO data
                $prQuery->whereRaw('1 = 0'); // This will return no results
            } else {
                // Only show PR data with specific status
                $prQuery->where('purchase_requests.status', $request->status);
                $poQuery->whereRaw('1 = 0'); // This will return no results
            }
        }

        // Apply date range filters
        if ($request->filled('date_from')) {
            $prQuery->whereDate('purchase_requests.created_at', '>=', $request->date_from);
            $poQuery->whereDate('purchase_requests.po_generated_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $prQuery->whereDate('purchase_requests.created_at', '<=', $request->date_to);
            $poQuery->whereDate('purchase_requests.po_generated_at', '<=', $request->date_to);
        }

        // Union the queries and order by date
        $combinedQuery = $prQuery->union($poQuery)
            ->orderBy('date_created', 'desc');

        // Paginate the combined results
        $reports = $combinedQuery->paginate(10);

        // Get all available statuses for filter dropdown
        $statuses = PurchaseRequest::select('status')
            ->distinct()
            ->pluck('status');

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports', compact('reports', 'statuses', 'recentActivities'));
    }

    public function export(Request $request)
    {
        // Export functionality can be implemented here
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
