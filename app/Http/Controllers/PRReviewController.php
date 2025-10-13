<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\UserActivity;
use App\Services\ActivityService;
use Barryvdh\DomPDF\Facade\Pdf;


class PRReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with('user')
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('entity_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('fund_cluster', 'like', '%' . $searchTerm . '%')
                    ->orWhere('office_section', 'like', '%' . $searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('middle_name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        // Status filtering
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Date range filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $purchaseRequests = $query->paginate(10)->appends($request->query());

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get available statuses for filtering
        $statuses = PurchaseRequest::where('status', '!=', 'draft')
            ->select('status')
            ->distinct()
            ->pluck('status');

        return view('staff.pr_review', compact('purchaseRequests', 'recentActivities', 'statuses'));
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        try {
            $data = [
                'id' => $purchaseRequest->id,
                'pr_number' => $purchaseRequest->pr_number,
                'entity_name' => $purchaseRequest->entity_name,
                'fund_cluster' => $purchaseRequest->fund_cluster,
                'office_section' => $purchaseRequest->office_section,
                'date' => $purchaseRequest->date->toDateString(),
                'delivery_address' => $purchaseRequest->delivery_address,
                'purpose' => $purchaseRequest->purpose,
                'requested_by_name' => $purchaseRequest->requested_by_name,
                'delivery_period' => $purchaseRequest->delivery_period,
                'status' => $purchaseRequest->status,
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
            $purchaseRequest->update(['status' => 'approved']);

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
            $purchaseRequest->update(['status' => 'rejected']);

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
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function exportXLSX(Request $request)
    {
        $query = PurchaseRequest::with('user')
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc');

        // Apply the same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('entity_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('fund_cluster', 'like', '%' . $searchTerm . '%')
                    ->orWhere('office_section', 'like', '%' . $searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('middle_name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get all filtered results (no pagination)
        $purchaseRequests = $query->get();

        // Create CSV content
        $csvContent = [];
        $csvContent[] = [
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

        $counter = 1;
        foreach ($purchaseRequests as $pr) {
            $requestedBy = $pr->user ? $pr->user->first_name .
                ($pr->user->middle_name ? ' ' . $pr->user->middle_name : '') .
                ' ' . $pr->user->last_name : 'N/A';

            $csvContent[] = [
                $counter++,
                $pr->pr_number,
                $pr->entity_name,
                $pr->fund_cluster,
                $pr->office_section,
                $requestedBy,
                '₱' . number_format($pr->total, 2),
                ucfirst(str_replace('_', ' ', $pr->status)),
                $pr->created_at->format('M d, Y'),
                $pr->date ? \Carbon\Carbon::parse($pr->date)->format('M d, Y') : ''
            ];
        }

        // Create CSV file
        $filename = 'pr_review_' . date('Y-m-d_H-i-s') . '.csv';
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

    public function exportPDF(Request $request)
    {
        $query = PurchaseRequest::with('user')
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc');

        // Apply the same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('entity_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('fund_cluster', 'like', '%' . $searchTerm . '%')
                    ->orWhere('office_section', 'like', '%' . $searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                        $userQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                            ->orWhere('middle_name', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get all filtered results (no pagination)
        $purchaseRequests = $query->get();

        $pdf = Pdf::loadView('exports.pr_review_pdf', compact('purchaseRequests'));
        $filename = 'pr_review_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }
}
