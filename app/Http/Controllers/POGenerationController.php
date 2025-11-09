<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\User;
use App\Models\PODocument;
use App\Models\Supplier;
use App\Services\ActivityService;
use Barryvdh\DomPDF\Facade\Pdf;

class POGenerationController extends Controller
{
    public function index(Request $request)
    {
        // Build query for generated POs from purchase_orders table
        $generatedPOsQuery = PurchaseOrder::with(['purchaseRequest.user', 'supplier', 'purchaseRequest.status'])
            ->join('purchase_requests', 'purchase_orders.purchase_request_id', '=', 'purchase_requests.id')
            ->join('users', 'purchase_requests.user_id', '=', 'users.id')
            ->join('statuses', 'purchase_requests.status_id', '=', 'statuses.id')
            ->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->select([
                'purchase_orders.*',
                'purchase_requests.pr_number',
                'purchase_requests.total',
                'statuses.name as pr_status',
                'purchase_requests.delivery_address',
                'purchase_requests.delivery_period',
                'users.first_name',
                'users.middle_name',
                'users.last_name',
                'suppliers.supplier_name',
                'suppliers.address as supplier_address',
                'suppliers.tin as supplier_tin'
            ]);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $generatedPOsQuery->where(function ($query) use ($search) {
                $query->where('purchase_requests.pr_number', 'like', "%{$search}%")
                    ->orWhere('purchase_orders.po_number', 'like', "%{$search}%")
                    ->orWhere('suppliers.supplier_name', 'like', "%{$search}%")
                    ->orWhereRaw("CONCAT(users.first_name, ' ', IFNULL(users.middle_name, ''), ' ', users.last_name) LIKE ?", ["%{$search}%"]);
            });
        }

        // Apply status filter (now refers to PR status, but we show all POs)
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'po_generated') {
                // Show all POs (default behavior)
            } elseif ($request->status === 'completed') {
                // Filter by completed POs
                $generatedPOsQuery->whereNotNull('purchase_orders.completed_at');
            } else {
                // Filter by PR status
                $generatedPOsQuery->where('statuses.name', $request->status);
            }
        }

        // Apply date filters
        if ($request->filled('date_from')) {
            $generatedPOsQuery->whereDate('purchase_orders.generated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $generatedPOsQuery->whereDate('purchase_orders.generated_at', '<=', $request->date_to);
        }

        // Get filtered results
        $generatedPOs = $generatedPOsQuery->orderBy('purchase_orders.generated_at', 'desc')->paginate(10);

        // Get all approved purchase requests (not filtered for now, as they're separate section)
        $approvedPRs = PurchaseRequest::with('user')
            ->whereHas('status', function ($query) {
                $query->where('name', 'approved');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get PO documents (not filtered for now, as they're separate section)
        $poDocuments = PODocument::orderBy('created_at', 'desc')
            ->paginate(10);

        $suppliers = \App\Models\Supplier::whereHas('status', function ($query) {
            $query->where('name', 'active');
        })->get();

        // Get system selections for edit modal
        $modesOfProcurement = \App\Models\SystemSelection::getByType('mode_of_procurement');
        $deliveryTerms = \App\Models\SystemSelection::getByType('delivery_term');
        $paymentTerms = \App\Models\SystemSelection::getByType('payment_term');

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('staff.po_generation', compact('approvedPRs', 'generatedPOs', 'poDocuments', 'suppliers', 'modesOfProcurement', 'deliveryTerms', 'paymentTerms', 'recentActivities'));
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        // Get the associated PR
        $purchaseRequest = $purchaseOrder->purchaseRequest;

        if (!$purchaseRequest) {
            return response()->json(['error' => 'No Purchase Request found for this PO.'], 404);
        }

        // Load the items and relationships
        $purchaseRequest->load(['items', 'user']);
        $purchaseOrder->load(['supplier']);

        return response()->json([
            'id' => $purchaseOrder->id,
            'pr_number' => $purchaseRequest->pr_number,
            'entity_name' => $purchaseRequest->entity_name,
            'fund_cluster' => $purchaseRequest->fund_cluster,
            'office_section' => $purchaseRequest->office_section,
            'date' => $purchaseRequest->date ? $purchaseRequest->date->toDateString() : '',
            'delivery_address' => $purchaseRequest->delivery_address,
            'purpose' => $purchaseRequest->purpose,
            'requested_by_name' => $purchaseRequest->user
                ? ($purchaseRequest->user->first_name . ($purchaseRequest->user->middle_name ? ' ' . $purchaseRequest->user->middle_name : '') . ' ' . $purchaseRequest->user->last_name)
                : 'Unknown',
            'delivery_period' => $purchaseRequest->delivery_period,
            'status' => $purchaseRequest->status,
            'status_display' => $purchaseRequest->status_display,
            'status_color' => $purchaseRequest->status_color,
            'requesting_unit' => $purchaseRequest->user
                ? ($purchaseRequest->user->first_name . ($purchaseRequest->user->middle_name ? ' ' . $purchaseRequest->user->middle_name : '') . ' ' . $purchaseRequest->user->last_name)
                : 'Unknown',
            'created_at' => $purchaseRequest->date ? $purchaseRequest->date->format('M d, Y') : '',
            'po_number' => $purchaseOrder->po_number,
            'supplier_id' => $purchaseOrder->supplier_id,
            'supplier_name' => $purchaseOrder->supplier ? $purchaseOrder->supplier->supplier_name : null,
            'supplier_address' => $purchaseOrder->supplier ? $purchaseOrder->supplier->address : '',
            'supplier_tin' => $purchaseOrder->supplier ? $purchaseOrder->supplier->tin : '',
            'generated_at' => $purchaseOrder->generated_at ? $purchaseOrder->generated_at->format('M d, Y') : null,
            'delivery_term' => $purchaseOrder->delivery_term,
            'payment_term' => $purchaseOrder->payment_term,
            'mode_of_procurement' => $purchaseOrder->mode_of_procurement,
            'place_of_delivery' => $purchaseRequest->delivery_address,
            'date_of_delivery' => $purchaseOrder->date_of_delivery ? $purchaseOrder->date_of_delivery->format('M d, Y') : '',
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
        // Find the associated PO
        $purchaseOrder = PurchaseOrder::where('purchase_request_id', $purchaseRequest->id)->first();
        if (!$purchaseOrder) {
            return redirect()->route('staff.po_generation')->with('error', 'Purchase Order not found.');
        }

        return view('staff.po_show', compact('purchaseRequest', 'purchaseOrder'));
    }

    public function printPO(\App\Models\PurchaseRequest $purchaseRequest)
    {
        // Find the associated PO
        $purchaseOrder = PurchaseOrder::where('purchase_request_id', $purchaseRequest->id)->first();
        if (!$purchaseOrder) {
            return redirect()->route('staff.po_generation')->with('error', 'Purchase Order not found.');
        }

        // Load relationships
        $purchaseRequest->load('user');
        $purchaseOrder->load('supplier', 'generatedBy');
        return view('staff.po_print', compact('purchaseRequest', 'purchaseOrder'));
    }

    public function editPO(PurchaseRequest $purchaseRequest)
    {
        // Find the associated PO
        $purchaseOrder = PurchaseOrder::where('purchase_request_id', $purchaseRequest->id)->first();
        if (!$purchaseOrder) {
            return redirect()->route('staff.po_generation')->with('error', 'Purchase Order not found.');
        }

        return view('staff.po_edit', compact('purchaseRequest', 'purchaseOrder'));
    }

    public function updatePO(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Find the associated PO
        $purchaseOrder = PurchaseOrder::where('purchase_request_id', $purchaseRequest->id)->first();
        if (!$purchaseOrder) {
            return response()->json(['error' => 'Purchase Order not found.'], 404);
        }

        $request->validate([
            'po_number' => 'required|string|max:50',
            'delivery_term' => 'required|string|max:255',
            'payment_term' => 'required|string|max:255',
            'mode_of_procurement' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'date_of_delivery' => 'required|date',
        ]);

        $purchaseOrder->update([
            'po_number' => $request->po_number,
            'delivery_term' => $request->delivery_term,
            'payment_term' => $request->payment_term,
            'mode_of_procurement' => $request->mode_of_procurement,
            'supplier_id' => $request->supplier_id,
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
        $suppliers = \App\Models\Supplier::whereHas('status', function ($query) {
            $query->where('name', 'active');
        })->orderBy('supplier_name')->get();

        // Get system selections
        $modesOfProcurement = \App\Models\SystemSelection::getByType('mode_of_procurement');
        $deliveryTerms = \App\Models\SystemSelection::getByType('delivery_term');
        $paymentTerms = \App\Models\SystemSelection::getByType('payment_term');

        // Generate PO number
        $autoGeneratedPONumber = 'PO ' . date('Y-m') . '-' . str_pad(PurchaseOrder::whereYear('generated_at', date('Y'))->whereMonth('generated_at', date('m'))->count() + 1, 4, '0', STR_PAD_LEFT);

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('staff.generate_po', compact('purchaseRequest', 'suppliers', 'modesOfProcurement', 'deliveryTerms', 'paymentTerms', 'autoGeneratedPONumber', 'recentActivities'));
    }

    public function storeGeneratedPO(Request $request, PurchaseRequest $purchaseRequest)
    {
        // Check if PR is approved
        if ($purchaseRequest->status !== 'approved') {
            return redirect()->route('staff.po_generation')->with('error', 'Only approved PRs can generate POs.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'po_number' => 'required|string|max:50',
            'supplier_address' => 'required|string|max:1000',
            'supplier_tin' => 'nullable|string|max:255',
            'mode_of_procurement' => 'required|string|max:255',
            'place_of_delivery' => 'required|string|max:1000',
            'delivery_term' => 'required|string|max:255',
            'payment_term' => 'required|string|max:255',
            'date_of_delivery' => 'required|date',
        ]);

        try {
            // Check if PO already exists for this PR
            $existingPO = PurchaseOrder::where('purchase_request_id', $purchaseRequest->id)->first();
            if ($existingPO) {
                return back()->withErrors(['error' => 'A Purchase Order already exists for this PR.']);
            }

            // Create new PurchaseOrder record
            PurchaseOrder::create([
                'purchase_request_id' => $purchaseRequest->id,
                'supplier_id' => $request->supplier_id,
                'po_number' => $request->po_number,
                'mode_of_procurement' => $request->mode_of_procurement,
                'delivery_term' => $request->delivery_term,
                'payment_term' => $request->payment_term,
                'date_of_delivery' => $request->date_of_delivery,
                'generated_at' => now(),
                'generated_by' => auth()->id(),
            ]);

            // Update PR status to po_generated
            $purchaseRequest->update(['status' => 'po_generated']);

            // Log the activity
            ActivityService::logPoGenerated($purchaseRequest->pr_number, $request->po_number);

            return redirect()->route('staff.po_generation')->with('success', 'Purchase Order generated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error generating PO: ' . $e->getMessage()]);
        }
    }

    public function exportXLSX(Request $request)
    {
        $query = PurchaseOrder::with(['purchaseRequest.user', 'supplier'])
            ->join('purchase_requests', 'purchase_orders.purchase_request_id', '=', 'purchase_requests.id')
            ->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->select([
                'purchase_orders.*',
                'purchase_requests.pr_number',
                'purchase_requests.total',
                'suppliers.supplier_name'
            ]);

        // Apply the same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('purchase_requests.pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('purchase_orders.po_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('suppliers.supplier_name', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'completed') {
                $query->whereNotNull('purchase_orders.completed_at');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_orders.generated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('purchase_orders.generated_at', '<=', $request->date_to);
        }

        // Get all filtered results (no pagination)
        $generatedPOs = $query->orderBy('purchase_orders.generated_at', 'desc')->get();

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
            $requestedBy = $po->purchaseRequest->user ? $po->purchaseRequest->user->first_name .
                ($po->purchaseRequest->user->middle_name ? ' ' . $po->purchaseRequest->user->middle_name : '') .
                ' ' . $po->purchaseRequest->user->last_name : 'N/A';

            $csvContent[] = [
                $counter++,
                $po->po_number,
                $po->pr_number,
                $po->supplier ? $po->supplier->supplier_name : 'N/A',
                $requestedBy,
                $po->generated_at ? $po->generated_at->format('M d, Y') : 'N/A',
                '₱' . number_format($po->purchaseRequest->total, 2),
                $po->status_display
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
        $query = PurchaseOrder::with(['purchaseRequest.user', 'purchaseRequest.items', 'supplier'])
            ->join('purchase_requests', 'purchase_orders.purchase_request_id', '=', 'purchase_requests.id')
            ->leftJoin('suppliers', 'purchase_orders.supplier_id', '=', 'suppliers.id')
            ->select([
                'purchase_orders.*',
                'purchase_requests.*',
                'suppliers.supplier_name',
                'suppliers.address as supplier_address',
                'suppliers.tin as supplier_tin'
            ]);

        // Apply the same filters as index method
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('purchase_requests.pr_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('purchase_orders.po_number', 'like', '%' . $searchTerm . '%')
                    ->orWhere('suppliers.supplier_name', 'like', '%' . $searchTerm . '%');
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'completed') {
                $query->whereNotNull('purchase_orders.completed_at');
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_orders.generated_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('purchase_orders.generated_at', '<=', $request->date_to);
        }

        // Get all filtered results (no pagination)
        $purchaseOrders = $query->orderBy('purchase_orders.generated_at', 'desc')->get();

        // Generate PDF using DomPDF
        $pdf = Pdf::loadView('exports.po_generation_pdf', compact('purchaseOrders'));

        $filename = 'po_generation_' . date('Y-m-d_H-i-s') . '.pdf';
        return $pdf->download($filename);
    }
}
