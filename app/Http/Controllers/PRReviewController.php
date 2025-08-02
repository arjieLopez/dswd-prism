<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Services\ActivityService;

class PRReviewController extends Controller
{
    public function index()
    {
        // Get all purchase requests for review (including draft for debugging)
        $purchaseRequests = PurchaseRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('staff.pr_review', compact('purchaseRequests'));
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        // Check if user can view this PR (staff can view all)
        // Temporarily removed role check for debugging
        // if (!auth()->user()->hasRole('staff')) {
        //     abort(403, 'Unauthorized access.');
        // }

        try {
            $data = [
                'id' => $purchaseRequest->id,
                'pr_number' => $purchaseRequest->pr_number,
                'entity_name' => $purchaseRequest->entity_name,
                'fund_cluster' => $purchaseRequest->fund_cluster,
                'office_section' => $purchaseRequest->office_section,
                'date' => $purchaseRequest->date->toDateString(),
                'unit' => $purchaseRequest->unit,
                'quantity' => $purchaseRequest->quantity,
                'unit_cost' => $purchaseRequest->unit_cost,
                'total_cost' => $purchaseRequest->total_cost,
                'item_description' => $purchaseRequest->item_description,
                'delivery_address' => $purchaseRequest->delivery_address,
                'purpose' => $purchaseRequest->purpose,
                'requested_by_name' => $purchaseRequest->requested_by_name,
                'delivery_period' => $purchaseRequest->delivery_period,
                'status' => $purchaseRequest->status,
                'status_color' => $this->getStatusColorClass($purchaseRequest->status),
                'requesting_unit' => $purchaseRequest->user->name,
                'created_at' => $purchaseRequest->created_at->format('M d, Y H:i'),
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
