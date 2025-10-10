<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\User;
use App\Models\PODocument;
use App\Models\Supplier;
use App\Services\ActivityService;
use Barryvdh\DomPDF\Facade\Pdf;

class POGenerationController extends Controller
{
    public function index(Request $request)
    {
        // Build query for generated POs (PRs with status 'po_generated' or 'completed')
        $generatedPOsQuery = PurchaseRequest::with(['supplier', 'user'])
            ->whereIn('status', ['po_generated', 'completed']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $generatedPOsQuery->where(function ($query) use ($search) {
                $query->where('pr_number', 'like', "%{$search}%")
                    ->orWhere('po_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($q) use ($search) {
                        $q->where('supplier_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    });
            });
        }

        // Apply status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $generatedPOsQuery->where('status', $request->status);
        }

        // Apply date filters
        if ($request->filled('date_from')) {
            $generatedPOsQuery->whereDate('po_generated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $generatedPOsQuery->whereDate('po_generated_at', '<=', $request->date_to);
        }

        // Get filtered results
        $generatedPOs = $generatedPOsQuery->orderBy('po_generated_at', 'desc')->paginate(10);

        // Get all approved purchase requests (not filtered for now, as they're separate section)
        $approvedPRs = PurchaseRequest::with('user')
            ->where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get PO documents (not filtered for now, as they're separate section)
        $poDocuments = PODocument::orderBy('created_at', 'desc')
            ->paginate(10);

        $suppliers = \App\Models\Supplier::where('status', 'active')->get();

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('staff.po_generation', compact('approvedPRs', 'generatedPOs', 'poDocuments', 'suppliers', 'recentActivities'));
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        if (!in_array($purchaseRequest->status, ['approved', 'po_generated', 'completed'])) {
            return response()->json(['error' => 'Only approved PRs or generated POs can be viewed.'], 403);
        }

        // Load the items and user relationships
        $purchaseRequest->load(['items', 'user', 'supplier']);

        return response()->json([
            'id' => $purchaseRequest->id,
            'pr_number' => $purchaseRequest->pr_number,
            'entity_name' => $purchaseRequest->entity_name,
            'fund_cluster' => $purchaseRequest->fund_cluster,
            'office_section' => $purchaseRequest->office_section,
            'date' => $purchaseRequest->date ? $purchaseRequest->date->toDateString() : '',
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
            'po_number' => $purchaseRequest->po_number,
            'supplier_id' => $purchaseRequest->supplier_id,
            'supplier_name' => $purchaseRequest->supplier ? $purchaseRequest->supplier->supplier_name : null,
            'supplier_address' => $purchaseRequest->supplier ? $purchaseRequest->supplier->address : '',
            'supplier_tin' => $purchaseRequest->supplier ? $purchaseRequest->supplier->tin : '',
            'po_generated_at' => $purchaseRequest->po_generated_at ? $purchaseRequest->po_generated_at->format('Y-m-d') : null,
            'delivery_term' => $purchaseRequest->delivery_term,
            'payment_term' => $purchaseRequest->payment_term,
            'mode_of_procurement' => $purchaseRequest->mode_of_procurement,
            'place_of_delivery' => $purchaseRequest->place_of_delivery ?? $purchaseRequest->delivery_address,
            'date_of_delivery' => $purchaseRequest->date_of_delivery ? $purchaseRequest->date_of_delivery->format('Y-m-d') : '',
            'items' => $purchaseRequest->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'unit' => $item->unit,
                    'quantity' => $item->quantity,
                    'unit_cost' => $item->unit_cost,
                    'total_cost' => $item->total_cost,
                    'item_description' => $item->item_description,
                ];
            }),
            'total' => $purchaseRequest->total,
        ]);
    }

    public function generatePO(PurchaseRequest $purchaseRequest)
    {
        // Check if PR is approved
        if ($purchaseRequest->status !== 'approved') {
            return response()->json(['success' => false, 'message' => 'Only approved PRs can generate POs.'], 403);
        }

        // Redirect to the PO generation form instead of generating directly
        return response()->json([
            'success' => true,
            'redirect' => route('staff.generate_po.form', $purchaseRequest)
        ]);
    }

    private function getStatusColorClass($status)
    {
        return match ($status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            'po_generated' => 'bg-blue-100 text-blue-800',
            'failed' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function showPO(PurchaseRequest $purchaseRequest)
    {
        // Add your logic to show the PO details
        return view('staff.po_show', compact('purchaseRequest'));
    }

    public function printPO(\App\Models\PurchaseRequest $purchaseRequest)
    {
        // You can load relationships as needed
        $purchaseRequest->load('supplier', 'user');
        return view('staff.po_print', compact('purchaseRequest'));
    }

    public function editPO(PurchaseRequest $purchaseRequest)
    {
        // Add your logic to edit the PO
        return view('staff.po_edit', compact('purchaseRequest'));
    }

    public function updatePO(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
            'po_number' => 'required|string|max:255',
            'delivery_term' => 'required|string|max:255',
            'payment_term' => 'required|string|max:255',
            'mode_of_procurement' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'place_of_delivery' => 'required|string|max:1000',
            'date_of_delivery' => 'required|date',
        ]);

        $purchaseRequest->update([
            'po_number' => $request->po_number,
            'delivery_term' => $request->delivery_term,
            'payment_term' => $request->payment_term,
            'mode_of_procurement' => $request->mode_of_procurement,
            'supplier_id' => $request->supplier_id,
            'place_of_delivery' => $request->place_of_delivery,
            'date_of_delivery' => $request->date_of_delivery,
        ]);

        return response()->json(['success' => true]);
    }

    public function showGenerateForm(PurchaseRequest $purchaseRequest)
    {
        // Check if PR is approved
        if ($purchaseRequest->status !== 'approved') {
            return redirect()->route('staff.po_generation')->with('error', 'Only approved PRs can generate POs.');
        }

        // Load the items relationship
        $purchaseRequest->load('items');

        // Get active suppliers
        $suppliers = \App\Models\Supplier::where('status', 'active')->orderBy('supplier_name')->get();

        // Generate PO number
        $autoGeneratedPONumber = 'PO ' . date('Y') . '-' . str_pad(PurchaseRequest::where('status', 'po_generated')->count() + 1, 4, '0', STR_PAD_LEFT);

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('staff.generate_po', compact('purchaseRequest', 'suppliers', 'autoGeneratedPONumber', 'recentActivities'));
    }

    public function storeGeneratedPO(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Check if PR is approved
        if ($purchaseRequest->status !== 'approved') {
            return redirect()->route('staff.po_generation')->with('error', 'Only approved PRs can generate POs.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_number' => 'required|string|max:255',
            'supplier_address' => 'required|string|max:1000',
            'supplier_tin' => 'nullable|string|max:255',
            'mode_of_procurement' => 'required|string|max:255',
            'place_of_delivery' => 'required|string|max:1000',
            'delivery_term' => 'required|string|max:255',
            'payment_term' => 'required|string|max:255',
            'date_of_delivery' => 'required|date',
        ]);

        try {
            // Update PR with PO details
            $purchaseRequest->update([
                'po_number' => $request->po_number,
                'status' => 'po_generated',
                'po_generated_at' => now(),
                'po_generated_by' => auth()->user()->first_name . (auth()->user()->middle_name ? ' ' . auth()->user()->middle_name : '') . ' ' . auth()->user()->last_name,
                'supplier_id' => $request->supplier_id,
                'mode_of_procurement' => $request->mode_of_procurement,
                'delivery_term' => $request->delivery_term,
                'payment_term' => $request->payment_term,
                'date_of_delivery' => $request->date_of_delivery,
            ]);

            // Add this line:
            ActivityService::logPoGenerated($purchaseRequest->pr_number, $request->po_number);

            return redirect()->route('staff.po_generation')->with('success', 'Purchase Order generated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error generating PO: ' . $e->getMessage()]);
        }
    }

    public function exportXLSX(Request $request)
    {
        $query = PurchaseRequest::with(['supplier', 'user'])
            ->whereIn('status', ['po_generated', 'completed'])
            ->orderBy('po_generated_at', 'desc');

        // Apply the same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('po_number', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('supplier', function ($supplierQuery) use ($searchTerm) {
                        $supplierQuery->where('supplier_name', 'like', '%' . $searchTerm . '%');
                    })
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
            $query->whereDate('po_generated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('po_generated_at', '<=', $request->date_to);
        }

        // Get all filtered results (no pagination)
        $generatedPOs = $query->get();

        // Create CSV content
        $csvContent = [];
        $csvContent[] = [
            'Counter',
            'PO Number',
            'PR Number',
            'Supplier',
            'Requested By',
            'Date Generated',
            'Amount',
            'Status'
        ];

        $counter = 1;
        foreach ($generatedPOs as $po) {
            $requestedBy = $po->user ? $po->user->first_name .
                ($po->user->middle_name ? ' ' . $po->user->middle_name : '') .
                ' ' . $po->user->last_name : 'N/A';

            $csvContent[] = [
                $counter++,
                $po->po_number,
                $po->pr_number,
                $po->supplier ? $po->supplier->supplier_name : 'N/A',
                $requestedBy,
                $po->po_generated_at ? $po->po_generated_at->format('M d, Y') : 'N/A',
                '₱' . number_format($po->total, 2),
                ucfirst(str_replace('_', ' ', $po->status))
            ];
        }

        // Create CSV file
        $filename = 'po_generation_' . date('Y-m-d_H-i-s') . '.csv';
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
        $query = PurchaseRequest::with(['supplier', 'user', 'items'])
            ->whereIn('status', ['po_generated', 'completed'])
            ->orderBy('po_generated_at', 'desc');

        // Apply the same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('po_number', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('supplier', function ($supplierQuery) use ($searchTerm) {
                        $supplierQuery->where('supplier_name', 'like', '%' . $searchTerm . '%');
                    })
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
            $query->whereDate('po_generated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('po_generated_at', '<=', $request->date_to);
        }

        // Get all filtered results (no pagination)
        $purchaseRequests = $query->get();

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('exports.po_generation_pdf', compact('purchaseRequests'));

        $filename = 'po_generation_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }
}
