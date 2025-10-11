<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PODocument;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Get PRs that are approved or completed (only these statuses should be shown)
        $prQuery = PurchaseRequest::with('user')
            ->whereIn('purchase_requests.status', ['approved', 'completed'])
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

        // Get PO data for PRs that are po_generated (only this status should be shown)
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

        // Apply status filter (only allow approved, completed, and po_generated)
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'po_generated') {
                // Only show PO data
                $prQuery->whereRaw('1 = 0'); // This will return no results
            } elseif (in_array($request->status, ['approved', 'completed'])) {
                // Only show PR data with specific status
                $prQuery->where('purchase_requests.status', $request->status);
                $poQuery->whereRaw('1 = 0'); // This will return no results
            } else {
                // Invalid status filter, show no results
                $prQuery->whereRaw('1 = 0');
                $poQuery->whereRaw('1 = 0');
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
        $format = $request->input('format', 'xlsx');

        // Get the same data as the index method with filters applied
        $reports = $this->getFilteredReports($request);

        if ($format === 'xlsx') {
            return $this->exportToCsv($reports, $request);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($reports, $request);
        }

        return response()->json(['error' => 'Invalid format'], 400);
    }
    private function getFilteredReports(Request $request)
    {
        // Same logic as index method to get filtered data
        $prQuery = PurchaseRequest::with('user')
            ->whereIn('purchase_requests.status', ['approved', 'completed'])
            ->select([
                'purchase_requests.id',
                'purchase_requests.pr_number',
                'purchase_requests.po_number',
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

        $poQuery = PurchaseRequest::with('user')
            ->where('purchase_requests.status', 'po_generated')
            ->select([
                'purchase_requests.id',
                'purchase_requests.pr_number',
                'purchase_requests.po_number',
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

        // Apply filters
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

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'po_generated') {
                $prQuery->whereRaw('1 = 0');
            } elseif (in_array($request->status, ['approved', 'completed'])) {
                $prQuery->where('purchase_requests.status', $request->status);
                $poQuery->whereRaw('1 = 0');
            } else {
                $prQuery->whereRaw('1 = 0');
                $poQuery->whereRaw('1 = 0');
            }
        }

        if ($request->filled('date_from')) {
            $prQuery->whereDate('purchase_requests.created_at', '>=', $request->date_from);
            $poQuery->whereDate('purchase_requests.po_generated_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $prQuery->whereDate('purchase_requests.created_at', '<=', $request->date_to);
            $poQuery->whereDate('purchase_requests.po_generated_at', '<=', $request->date_to);
        }

        return $prQuery->union($poQuery)->orderBy('date_created', 'desc')->get();
    }

    private function exportToCsv($reports, Request $request)
    {
        // Create CSV content
        $csvContent = [];
        $csvContent[] = [
            'Counter',
            'Type',
            'Document Number',
            'Department',
            'Status',
            'Amount'
        ];

        $counter = 1;
        foreach ($reports as $report) {
            $csvContent[] = [
                $counter++,
                $report->type,
                $report->document_number,
                $report->department,
                $report->type === 'PO' ? 'PO Generated' : ucfirst(str_replace('_', ' ', $report->status)),
                '₱' . number_format($report->amount, 2)
            ];
        }

        // Create CSV file
        $filename = 'reports_' . date('Y-m-d_H-i-s') . '.csv';
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
    private function exportToPdf($reports, Request $request)
    {
        $data = [
            'reports' => $reports,
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ],
            'exported_at' => now()->format('F j, Y g:i A')
        ];

        $pdf = Pdf::loadView('exports.reports_pdf', $data);
        $filename = 'reports_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }

    public function getPRData($id)
    {
        $pr = PurchaseRequest::with(['user', 'items'])->findOrFail($id);

        return response()->json([
            'id' => $pr->id,
            'pr_number' => $pr->pr_number,
            'entity_name' => $pr->entity_name,
            'fund_cluster' => $pr->fund_cluster,
            'office_section' => $pr->office_section,
            'responsibility_center_code' => $pr->responsibility_center_code,
            'date' => $pr->created_at->format('M d, Y'),
            'purpose' => $pr->purpose,
            'total' => $pr->total,
            'status' => $pr->status,
            'status_color' => $pr->status_color,
            'status_display' => $pr->status_display,
            'requested_by_name' => $pr->user ? $pr->user->first_name .
                ($pr->user->middle_name ? ' ' . $pr->user->middle_name : '') .
                ' ' . $pr->user->last_name : '',
            'delivery_address' => $pr->delivery_address,
            'delivery_period' => $pr->delivery_period,
            'items' => $pr->items->map(function ($item) {
                return [
                    'unit' => $item->unit,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->total_cost,
                    'item_description' => $item->item_description,
                ];
            })
        ]);
    }

    public function getPOData($id)
    {
        $pr = PurchaseRequest::with(['user', 'items', 'supplier'])->findOrFail($id);

        return response()->json([
            'id' => $pr->id,
            'pr_number' => $pr->pr_number,
            'po_number' => $pr->po_number,
            'supplier_name' => $pr->supplier->supplier_name ?? '',
            'supplier_address' => $pr->supplier->address ?? '',
            'supplier_tin' => $pr->supplier->tin ?? '',
            'mode_of_procurement' => $pr->mode_of_procurement,
            'place_of_delivery' => $pr->delivery_address,
            'delivery_term' => $pr->delivery_term,
            'payment_term' => $pr->payment_term,
            'date_of_delivery' => $pr->date_of_delivery ? $pr->date_of_delivery->format('M d, Y') : '',
            'po_generated_at' => $pr->po_generated_at ? $pr->po_generated_at->format('M d, Y') : '',
            'requesting_unit' => $pr->user ? $pr->user->first_name .
                ($pr->user->middle_name ? ' ' . $pr->user->middle_name : '') .
                ' ' . $pr->user->last_name : '',
            'status' => $pr->status,
            'status_color' => $pr->status_color,
            'purpose' => $pr->purpose,
            'delivery_address' => $pr->delivery_address,
            'delivery_period' => $pr->delivery_period,
            'requested_by_name' => $pr->user ? $pr->user->first_name .
                ($pr->user->middle_name ? ' ' . $pr->user->middle_name : '') .
                ' ' . $pr->user->last_name : '',
            'items' => $pr->items->map(function ($item) {
                return [
                    'unit' => $item->unit,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->total_cost,
                    'item_description' => $item->item_description,
                ];
            })
        ]);
    }
}
