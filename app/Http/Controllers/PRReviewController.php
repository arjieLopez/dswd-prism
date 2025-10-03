<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\UserActivity;
use App\Services\ActivityService;


class PRReviewController extends Controller
{
    public function index()
    {
        // Get all purchase requests for review (including draft for debugging)
        $purchaseRequests = PurchaseRequest::with('user')
            ->where('status', '!=', 'draft')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('staff.pr_review', compact('purchaseRequests', 'recentActivities'));
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
                'created_at' => $purchaseRequest->created_at->format('M d, Y H:i'),
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

            // Add this line:
            ActivityService::logPrRejected(
                $purchaseRequest->pr_number,
                auth()->user()->first_name .
                    (auth()->user()->middle_name ? ' ' . auth()->user()->middle_name : '') .
                    ' ' .
                    auth()->user()->last_name,
                request('reason')
            );

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
}
