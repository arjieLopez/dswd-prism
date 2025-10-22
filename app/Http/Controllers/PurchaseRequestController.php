<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\SystemSelection;
use App\Models\UserActivity;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->purchaseRequests()->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('entity_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('fund_cluster', 'like', '%' . $searchTerm . '%')
                    ->orWhere('office_section', 'like', '%' . $searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $searchTerm . '%');
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

        $uploadedDocumentsQuery = auth()->user()->uploadedDocuments()->orderBy('created_at', 'desc');
        $fileTypes = auth()->user()->uploadedDocuments()->select('file_type')->distinct()->pluck('file_type');
        if ($request->filled('file_type') && $request->file_type !== 'all') {
            $uploadedDocumentsQuery->where('file_type', $request->file_type);
        }
        $uploadedDocuments = $uploadedDocumentsQuery->paginate(10);

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        $statuses = PurchaseRequest::select('status')->distinct()->pluck('status');

        // Get system selections for edit modal
        $entities = SystemSelection::getByType('entity');
        $fundClusters = SystemSelection::getByType('fund_cluster');
        $responsibilityCodes = SystemSelection::getByType('responsibility_code');
        $deliveryPeriods = SystemSelection::getByType('delivery_period');
        $deliveryAddresses = SystemSelection::getByType('delivery_address');
        $metricUnits = SystemSelection::getByType('metric_units');

        return view('user.requests', compact('purchaseRequests', 'uploadedDocuments', 'recentActivities', 'statuses', 'fileTypes', 'entities', 'fundClusters', 'responsibilityCodes', 'deliveryPeriods', 'deliveryAddresses', 'metricUnits'));
    }

    public function create()
    {
        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Get metric units from system selections
        $metricUnits = SystemSelection::getByType('metric_units');

        // Get entities from system selections
        $entities = SystemSelection::getByType('entity');

        // Get other system selections
        $fundClusters = SystemSelection::getByType('fund_cluster');
        $responsibilityCodes = SystemSelection::getByType('responsibility_code');
        $deliveryPeriods = SystemSelection::getByType('delivery_period');
        $deliveryAddresses = SystemSelection::getByType('delivery_address');

        return view('user.create_pr', compact('recentActivities', 'metricUnits', 'entities', 'fundClusters', 'responsibilityCodes', 'deliveryPeriods', 'deliveryAddresses'));
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
            'unit' => 'required|array',
            'unit.*' => 'required|string|max:255',
            'quantity' => 'required|array',
            'quantity.*' => 'required|integer|min:1',
            'unit_cost' => 'required|array',
            'unit_cost.*' => 'required|numeric|min:0',
            'item_description' => 'required|array',
            'item_description.*' => 'required|string',
            'delivery_period' => 'required|string|max:255',
            'delivery_address' => 'required|string',
            'purpose' => 'required|string',
            'requested_by_name' => 'required|string|max:255',
            'requested_by_designation' => 'required|string|max:255',
            'requested_by_signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Calculate totals
        $totalCost = 0;
        foreach ($request->quantity as $i => $qty) {
            $totalCost += $qty * $request->unit_cost[$i];
        }
        $total = $totalCost; // Add tax or other calculations if needed

        // Generate PR Number
        $prNumber = 'PR ' . date('Y-m') . '-' . str_pad(PurchaseRequest::whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->count() + 1, 4, '0', STR_PAD_LEFT);

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
            'total' => $total,
            'delivery_period' => $request->delivery_period,
            'delivery_address' => $request->delivery_address,
            'purpose' => $request->purpose,
            'requested_by_name' => auth()->user()->first_name . (auth()->user()->middle_name ? ' ' . auth()->user()->middle_name : '') . ' ' . auth()->user()->last_name,
            'requested_by_designation' => $request->requested_by_designation,
            'requested_by_signature' => $requestedBySignature,
            'scanned_copy' => $scannedCopy,
            'status' => 'draft',
        ]);

        foreach ($request->unit as $i => $unit) {
            $item = $purchaseRequest->items()->create([
                'unit' => $unit,
                'quantity' => $request->quantity[$i],
                'unit_cost' => $request->unit_cost[$i],
                'item_description' => $request->item_description[$i],
                'total_cost' => $request->quantity[$i] * $request->unit_cost[$i],
            ]);
        }

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
            'delivery_address' => $purchaseRequest->delivery_address,
            'purpose' => $purchaseRequest->purpose,
            'requested_by_name' => $purchaseRequest->requested_by_name,
            'delivery_period' => $purchaseRequest->delivery_period,
            'status' => $purchaseRequest->status,
            'status_color' => $purchaseRequest->status_color,
            'items' => $purchaseRequest->items->map(function ($item) {
                return [
                    'unit' => $item->unit,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->total_cost,
                    'item_description' => $item->item_description,
                ];
            }),
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
                'unit' => 'required|array',
                'unit.*' => 'required|string|max:255',
                'quantity' => 'required|array',
                'quantity.*' => 'required|integer|min:1',
                'unit_cost' => 'required|array',
                'unit_cost.*' => 'required|numeric|min:0',
                'item_description' => 'required|array',
                'item_description.*' => 'required|string',
                'delivery_period' => 'required|string|max:255',
                'delivery_address' => 'required|string',
                'purpose' => 'required|string',
            ]);

            // Calculate totals
            $totalCost = 0;
            foreach ($request->quantity as $i => $qty) {
                $totalCost += $qty * $request->unit_cost[$i];
            }
            $total = $totalCost;

            if (!in_array($purchaseRequest->status, ['approved', 'po_generated'])) {
                $purchaseRequest->status = 'draft';
            }

            $purchaseRequest->update([
                'entity_name' => $request->entity_name,
                'fund_cluster' => $request->fund_cluster,
                'office_section' => $request->office_section,
                'date' => $request->date,
                'total' => $total,
                'delivery_period' => $request->delivery_period,
                'delivery_address' => $request->delivery_address,
                'purpose' => $request->purpose,
            ]);

            // Delete existing items and create new ones
            $purchaseRequest->items()->delete();

            foreach ($request->unit as $i => $unit) {
                $purchaseRequest->items()->create([
                    'unit' => $unit,
                    'quantity' => $request->quantity[$i],
                    'unit_cost' => $request->unit_cost[$i],
                    'item_description' => $request->item_description[$i],
                    'total_cost' => $request->quantity[$i] * $request->unit_cost[$i],
                ]);
            }

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

        // Log activity for the submitting user
        ActivityService::logPrSubmitted($purchaseRequest->pr_number, $purchaseRequest->entity_name);

        // Notify all staff users about the new PR submission
        $staffUsers = \App\Models\User::where('role', 'staff')->get();

        foreach ($staffUsers as $staffUser) {
            \App\Models\UserActivity::create([
                'user_id' => $staffUser->id,
                'action' => 'pr_submitted_notification',
                'description' => 'New Purchase Request submitted by ' .
                    auth()->user()->first_name .
                    (auth()->user()->middle_name ? ' ' . auth()->user()->middle_name : '') .
                    ' ' . auth()->user()->last_name .
                    ' - ' . $purchaseRequest->pr_number,
                'pr_number' => $purchaseRequest->pr_number,
                'details' => [
                    'submitter_name' => auth()->user()->first_name .
                        (auth()->user()->middle_name ? ' ' . auth()->user()->middle_name : '') .
                        ' ' . auth()->user()->last_name,
                    'entity_name' => $purchaseRequest->entity_name,
                    'total' => $purchaseRequest->total,
                ]
            ]);
        }

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

    public function complete(PurchaseRequest $purchaseRequest)
    {
        try {
            // Ensure only the owner can mark as completed
            if ($purchaseRequest->user_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            // Only allow if status is approved or po_generated
            if (!in_array($purchaseRequest->status, ['approved', 'po_generated'])) {
                return response()->json(['success' => false, 'message' => 'PR cannot be marked as completed. Current status: ' . $purchaseRequest->status], 400);
            }

            $purchaseRequest->status = 'completed';

            // Try to set completed_at if the column exists
            try {
                $purchaseRequest->completed_at = now();
            } catch (\Exception $e) {
                // If completed_at column doesn't exist yet, continue without it
                // This will be handled once the migration is run
                Log::info('completed_at field not available yet: ' . $e->getMessage());
            }

            $purchaseRequest->save();

            // Log activity (basic logging for now)
            Log::info('Purchase Request marked as completed: ' . $purchaseRequest->pr_number . ' by user ' . auth()->id());

            return response()->json(['success' => true, 'message' => 'Purchase Request marked as completed!']);
        } catch (\Exception $e) {
            Log::error('Error completing purchase request: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error marking as completed: ' . $e->getMessage()], 500);
        }
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

    public function exportXLSX(Request $request)
    {
        $query = auth()->user()->purchaseRequests()->orderBy('created_at', 'desc');

        // Apply the same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('entity_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('fund_cluster', 'like', '%' . $searchTerm . '%')
                    ->orWhere('office_section', 'like', '%' . $searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $searchTerm . '%');
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
            'Total Amount',
            'Status',
            'Date Created',
            'Date'
        ];

        $counter = 1;
        foreach ($purchaseRequests as $pr) {
            $csvContent[] = [
                $counter++,
                $pr->pr_number,
                $pr->entity_name,
                $pr->fund_cluster,
                $pr->office_section,
                '₱' . number_format($pr->total, 2),
                ucfirst(str_replace('_', ' ', $pr->status)),
                $pr->created_at->format('M d, Y'),
                $pr->date ? \Carbon\Carbon::parse($pr->date)->format('M d, Y') : ''
            ];
        }

        // Create CSV file
        $filename = 'purchase_requests_' . date('Y-m-d_H-i-s') . '.csv';
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
        $query = auth()->user()->purchaseRequests()->orderBy('created_at', 'desc');

        // Apply the same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('entity_name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('fund_cluster', 'like', '%' . $searchTerm . '%')
                    ->orWhere('office_section', 'like', '%' . $searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $searchTerm . '%');
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

        $pdf = Pdf::loadView('exports.purchase_requests_pdf', compact('purchaseRequests'));
        $filename = 'purchase_requests_' . date('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($filename);
    }
}
