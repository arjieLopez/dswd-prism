<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\UserActivity;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->purchaseRequests()->orderBy('created_at', 'desc');
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        $purchaseRequests = $query->paginate(10);

        $uploadedDocumentsQuery = auth()->user()->uploadedDocuments()->orderBy('created_at', 'desc');
        $fileTypes = auth()->user()->uploadedDocuments()->select('file_type')->distinct()->pluck('file_type');
        if ($request->filled('file_type') && $request->file_type !== 'all') {
            $uploadedDocumentsQuery->where('file_type', $request->file_type);
        }
        $uploadedDocuments = $uploadedDocumentsQuery->paginate(10);

        $recentActivities = UserActivity::where('user_id', auth()->id())
            ->latest()
            ->take(10)
            ->get();
        $statuses = PurchaseRequest::select('status')->distinct()->pluck('status');

        return view('user.requests', compact('purchaseRequests', 'uploadedDocuments', 'recentActivities', 'statuses', 'fileTypes'));
    }

    public function create()
    {
        return view('user.create_pr');
    }

    public function store(Request $request)
    {
        $request->validate([
            'entity_name' => 'required|string|max:255',
            'fund_cluster' => 'required|string|max:255',
            'office_section' => 'required|string|max:255',
            'responsibility_center_code' => 'required|string|max:255',
            'date' => 'required|date',
            'stoc_property_no' => 'nullable|string|max:255',
            'unit' => 'required|string|max:255',
            'item_description' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'delivery_period' => 'required|string|max:255',
            'delivery_address' => 'required|string',
            'purpose' => 'required|string',
            'requested_by_name' => 'required|string|max:255',
            'requested_by_designation' => 'required|string|max:255',
            'requested_by_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Calculate totals
        $totalCost = $request->quantity * $request->unit_cost;
        $total = $totalCost; // Add tax or other calculations if needed

        // Generate PR Number
        $prNumber = 'PR ' . date('Y') . '-' . str_pad(PurchaseRequest::whereYear('created_at', date('Y'))->count() + 1, 4, '0', STR_PAD_LEFT);

        // Handle file uploads
        $requestedBySignature = null;
        $scannedCopy = null;

        if ($request->hasFile('requested_by_signature')) {
            $requestedBySignature = $request->file('requested_by_signature')->store('signatures', 'public');
        }

        if ($request->hasFile('scanned_copy')) {
            $scannedCopy = $request->file('scanned_copy')->store('purchase_requests', 'public');
        }

        $purchaseRequest = auth()->user()->purchaseRequests()->create([
            'pr_number' => $prNumber,
            'entity_name' => $request->entity_name,
            'fund_cluster' => $request->fund_cluster,
            'office_section' => $request->office_section,
            'responsibility_center_code' => $request->responsibility_center_code,
            'date' => $request->date,
            'stoc_property_no' => $request->stoc_property_no,
            'unit' => $request->unit,
            'item_description' => $request->item_description,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
            'total_cost' => $totalCost,
            'total' => $total,
            'delivery_period' => $request->delivery_period,
            'delivery_address' => $request->delivery_address,
            'purpose' => $request->purpose,
            'requested_by_name' => $request->requested_by_name,
            'requested_by_designation' => $request->requested_by_designation,
            'requested_by_signature' => $requestedBySignature,
            'scanned_copy' => $scannedCopy,
            'status' => 'draft',
        ]);


        ActivityService::logPrCreated($purchaseRequest->pr_number, $purchaseRequest->entity_name);

        return redirect()->route('user.requests')->with('success', 'Purchase Request created successfully!');
    }

    public function getData(PurchaseRequest $purchaseRequest)
    {
        // Check if user can access this purchase request
        if ($purchaseRequest->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
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
            'status_color' => $purchaseRequest->status_color,
        ]);
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Check if user can edit this purchase request
        if ($purchaseRequest->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            $validated = $request->validate([
                'entity_name' => 'required|string|max:255',
                'fund_cluster' => 'required|string|max:255',
                'office_section' => 'required|string|max:255',
                'date' => 'required|date',
                'unit' => 'required|string|max:255',
                'item_description' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'unit_cost' => 'required|numeric|min:0',
                'delivery_period' => 'required|string|max:255',
                'delivery_address' => 'required|string',
                'purpose' => 'required|string',
            ]);

            // Calculate totals
            $totalCost = $request->quantity * $request->unit_cost;
            $total = $totalCost;

            if (!in_array($purchaseRequest->status, ['approved', 'po_generated'])) {
                $purchaseRequest->status = 'draft';
            }

            $purchaseRequest->update([
                'entity_name' => $request->entity_name,
                'fund_cluster' => $request->fund_cluster,
                'office_section' => $request->office_section,
                'date' => $request->date,
                'unit' => $request->unit,
                'item_description' => $request->item_description,
                'quantity' => $request->quantity,
                'unit_cost' => $request->unit_cost,
                'total_cost' => $totalCost,
                'total' => $total,
                'delivery_period' => $request->delivery_period,
                'delivery_address' => $request->delivery_address,
                'purpose' => $request->purpose,
            ]);

            ActivityService::logPrUpdated($purchaseRequest->pr_number, $purchaseRequest->entity_name);

            return response()->json(['success' => true, 'message' => 'Purchase Request updated successfully!']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating purchase request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submit(PurchaseRequest $purchaseRequest)
    {
        // Ensure only the owner can submit
        if ($purchaseRequest->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Only allow submission if status is draft
        if ($purchaseRequest->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Only draft PRs can be submitted.']);
        }

        $purchaseRequest->status = 'pending';
        $purchaseRequest->save();

        // Optionally log activity here

        return response()->json(['success' => true, 'message' => 'Purchase Request submitted successfully!']);
    }

    public function withdraw(PurchaseRequest $purchaseRequest)
    {
        // Ensure only the owner can withdraw
        if ($purchaseRequest->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Only allow withdrawal if status is pending
        if ($purchaseRequest->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Only pending PRs can be withdrawn.']);
        }

        $purchaseRequest->status = 'draft';
        $purchaseRequest->save();

        // Optionally log activity here

        return response()->json(['success' => true, 'message' => 'Purchase Request withdrawn successfully!']);
    }

    public function print(PurchaseRequest $purchaseRequest)
    {
        // Optional: Only allow the owner to print
        if ($purchaseRequest->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return view('user.print_pr', compact('purchaseRequest'));
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        // Delete associated files
        if ($purchaseRequest->requested_by_signature) {
            Storage::disk('public')->delete($purchaseRequest->requested_by_signature);
        }
        if ($purchaseRequest->scanned_copy) {
            Storage::disk('public')->delete($purchaseRequest->scanned_copy);
        }

        $purchaseRequest->delete();
        return redirect()->route('user.requests')->with('success', 'Purchase Request deleted successfully!');
    }
}
