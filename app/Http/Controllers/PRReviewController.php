<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\UserActivity;
use App\Services\ActivityService;
use App\Services\ExportService;
use App\Services\QueryService;
use App\Constants\PaginationConstants;
use App\Constants\ActivityConstants;
use Barryvdh\DomPDF\Facade\Pdf;


class PRReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with('user', 'status')
            ->whereHas('status', function ($statusQuery) {
                $statusQuery->where('name', '!=', 'draft');
            })
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('entity_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('fund_cluster', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('office', function ($officeQuery) use ($searchTerm) {
                        $officeQuery->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('status', function ($statusQuery) use ($searchTerm) {
                        $statusQuery->where('name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('display_name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('middle_name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        // Status filtering
        if ($request->filled('status') && $request->status !== 'all') {
            $query->whereHas('status', function ($statusQuery) use ($request) {
                $statusQuery->where('name', $request->status);
            });
        }

        // Date range filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $purchaseRequests = $query->paginate(PaginationConstants::DEFAULT_PER_PAGE)->appends($request->query());

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(ActivityConstants::RECENT_ACTIVITY_LIMIT)
            ->get();

        // Get available statuses for filtering
        $statuses = \App\Models\Status::where('context', 'procurement')
            ->where('name', '!=', 'draft')
            ->get();

        return view('staff.pr_review', compact('purchaseRequests', 'recentActivities', 'statuses'));
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        // Load the status relationship
        $purchaseRequest->load('status', 'office');
        try {
            $data = [
                'id' => $purchaseRequest->id,
                'pr_number' => $purchaseRequest->pr_number,
                'entity_name' => $purchaseRequest->entity_name,
                'fund_cluster' => $purchaseRequest->fund_cluster,
                'office_id' => $purchaseRequest->office_id,
                'office_name' => $purchaseRequest->office->name,
                'date' => $purchaseRequest->date ? $purchaseRequest->date->format('M d, Y') : '',
                'delivery_address' => $purchaseRequest->delivery_address,
                'purpose' => $purchaseRequest->purpose,
                'requested_by_name' => $purchaseRequest->user ? $purchaseRequest->user->first_name .
                    ($purchaseRequest->user->middle_name ? ' ' . $purchaseRequest->user->middle_name : '') .
                    ' ' . $purchaseRequest->user->last_name : 'Unknown',
                'delivery_period' => $purchaseRequest->delivery_period,
                'status' => $purchaseRequest->status,
                'status_display' => $purchaseRequest->status_display,
                'status_color' => $this->getStatusColorClass($purchaseRequest->status),
                'requesting_unit' => $purchaseRequest->user
                    ? ($purchaseRequest->user->first_name . ($purchaseRequest->user->middle_name ? ' ' . $purchaseRequest->user->middle_name : '') . ' ' . $purchaseRequest->user->last_name)
                    : 'Unknown',
                'created_at' => $purchaseRequest->created_at->format('M d, Y'),
                'items' => $purchaseRequest->items->map(function ($item) {
                    return [
                        'unit' => $item->unit,
                        'quantity' => $item->quantity,
                        'unit_cost' => $item->unit_cost,
                        'total_cost' => $item->total_cost,
                        'item_description' => $item->item_description,
                    ];
                }),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error loading purchase request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(PurchaseRequest $purchaseRequest)
    {
        try {
            // Load the status relationship
            $purchaseRequest->load('status');
            $approvedStatus = \App\Models\Status::where('context', 'procurement')->where('name', 'approved')->first();
            $purchaseRequest->update(['status_id' => $approvedStatus->id]);

            // Log staff Activity:
            ActivityService::logPrApproved(
                $purchaseRequest->pr_number,
                auth()->user()->first_name .
                    (auth()->user()->middle_name ? ' ' . auth()->user()->middle_name : '') .
                    ' ' .
                    auth()->user()->last_name
            );

            \App\Models\UserActivity::create([
                'user_id' => $purchaseRequest->user_id,
                'action' => 'approved_pr', // or 'approved_po' if you add that to your model
                'description' => 'Your Purchase Request (PR No. ' . $purchaseRequest->pr_number . ') has been approved.',
                'pr_number' => $purchaseRequest->pr_number,
            ]);

            return response()->json(['success' => true, 'message' => 'Purchase Request approved successfully!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving purchase request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(PurchaseRequest $purchaseRequest)
    {
        try {
            $rejectedStatus = \App\Models\Status::where('context', 'procurement')->where('name', 'rejected')->first();
            $purchaseRequest->update(['status_id' => $rejectedStatus->id]);

            // Log staff Activity:
            ActivityService::logPrRejected(
                $purchaseRequest->pr_number,
                auth()->user()->first_name .
                    (auth()->user()->middle_name ? ' ' . auth()->user()->middle_name : '') .
                    ' ' .
                    auth()->user()->last_name,
                request('reason')
            );

            // Notify the requesting user
            \App\Models\UserActivity::create([
                'user_id' => $purchaseRequest->user_id,
                'action' => 'rejected_pr',
                'description' => 'Your Purchase Request (PR No. ' . $purchaseRequest->pr_number . ') has been rejected.' . (request('reason') ? ' Reason: ' . request('reason') : ''),
                'pr_number' => $purchaseRequest->pr_number,
            ]);

            return response()->json(['success' => true, 'message' => 'Purchase Request rejected successfully!']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting purchase request: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getStatusColorClass($status)
    {
        return match ($status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'po_generated' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-purple-100 text-purple-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function exportXLSX(Request $request)
    {
        $query = PurchaseRequest::with('user')
            ->whereHas('status', function ($query) {
                $query->where('name', '!=', 'draft');
            })
            ->orderBy('created_at', 'desc');

        // Apply the same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('entity_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('fund_cluster', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('office', function ($officeQuery) use ($searchTerm) {
                        $officeQuery->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('status', function ($statusQuery) use ($searchTerm) {
                        $statusQuery->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('middle_name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->whereHas('status', function ($statusQuery) use ($request) {
                $statusQuery->where('name', $request->status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get all filtered results (no pagination)
        $purchaseRequests = $query->with(['status', 'office'])->get();

        // Prepare headers
        $headers = [
            'Counter',
            'PR Number',
            'Entity Name',
            'Fund Cluster',
            'Office/Section',
            'Requested By',
            'Total Amount',
            'Status',
            'Date Created',
            'Date'
        ];

        // Prepare rows
        $rows = [];
        $counter = 1;
        foreach ($purchaseRequests as $pr) {
            $requestedBy = $pr->user ? $pr->user->first_name .
                ($pr->user->middle_name ? ' ' . $pr->user->middle_name : '') .
                ' ' . $pr->user->last_name : 'N/A';

            $rows[] = [
                $counter++,
                $pr->pr_number,
                $pr->entity_name,
                $pr->fund_cluster,
                $pr->office->name ?? 'N/A',
                $requestedBy,
                '₱' . number_format($pr->total, 2),
                $pr->status ? ucfirst(str_replace('_', ' ', $pr->status->name)) : 'N/A',
                $pr->created_at->format('M d, Y'),
                $pr->date ? \Carbon\Carbon::parse($pr->date)->format('M d, Y') : ''
            ];
        }

        // Use ExportService
        $exportService = new ExportService();
        $filename = $exportService->generateFilename('pr_review', 'csv');

        return $exportService->exportToCSV($headers, $rows, $filename);
    }

    public function exportPDF(Request $request)
    {
        $query = PurchaseRequest::with('user')
            ->whereHas('status', function ($query) {
                $query->where('name', '!=', 'draft');
            })
            ->orderBy('created_at', 'desc');

        // Apply the same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('entity_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('fund_cluster', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('office', function ($officeQuery) use ($searchTerm) {
                        $officeQuery->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('status', function ($statusQuery) use ($searchTerm) {
                        $statusQuery->where('name', 'like', '%' . $searchTerm . '%');
                    })
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('middle_name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->whereHas('status', function ($statusQuery) use ($request) {
                $statusQuery->where('name', $request->status);
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get all filtered results (no pagination)
        $purchaseRequests = $query->with('status')->get();

        // Use ExportService
        $exportService = new ExportService();
        $filename = $exportService->generateFilename('pr_review', 'pdf');

        return $exportService->exportToPDF('exports.pr_review_pdf', compact('purchaseRequests'), $filename);
    }
}
