<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $purchaseRequests = auth()->user()->purchaseRequests()->paginate(10);
        return view('user.requests', compact('purchaseRequests'));
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
            'scanned_copy' => 'nullable|file|mimes:pdf,jpeg,png,jpg|max:10240',
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

        return redirect()->route('user.requests')->with('success', 'Purchase Request created successfully!');
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        // Redirect to requests page since we use modals
        return redirect()->route('user.requests');
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        // Redirect to requests page since we use modals
        return redirect()->route('user.requests');
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $request->validate([
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

        return redirect()->route('user.requests')->with('success', 'Purchase Request updated successfully!');
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
