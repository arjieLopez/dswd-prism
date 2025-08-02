<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Services\ActivityService;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::orderBy('created_at', 'desc')
            ->paginate(10);

        return view('staff.suppliers', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'tin' => 'nullable|string|max:255',
            'address' => 'required|string|max:1000',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        try {
            $supplier = Supplier::create([
                'supplier_name' => $request->supplier_name,
                'tin' => $request->tin,
                'address' => $request->address,
                'contact_person' => $request->contact_person,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
                'status' => 'active'
            ]);

            // Add this line:
            ActivityService::logSupplierCreated($supplier->supplier_name);

            return response()->json(['success' => true, 'message' => 'Supplier added successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error adding supplier: ' . $e->getMessage()], 500);
        }
    }

    public function show(Supplier $supplier)
    {
        return response()->json($supplier);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'tin' => 'nullable|string|max:255',
            'address' => 'required|string|max:1000',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        try {
            // Store old values for logging
            $changes = [
                'supplier_name' => $request->supplier_name !== $supplier->supplier_name ? ['old' => $supplier->supplier_name, 'new' => $request->supplier_name] : null,
                'tin' => $request->tin !== $supplier->tin ? ['old' => $supplier->tin, 'new' => $request->tin] : null,
                'address' => $request->address !== $supplier->address ? ['old' => $supplier->address, 'new' => $request->address] : null,
                'contact_person' => $request->contact_person !== $supplier->contact_person ? ['old' => $supplier->contact_person, 'new' => $request->contact_person] : null,
                'contact_number' => $request->contact_number !== $supplier->contact_number ? ['old' => $supplier->contact_number, 'new' => $request->contact_number] : null,
                'email' => $request->email !== $supplier->email ? ['old' => $supplier->email, 'new' => $request->email] : null,
            ];

            $supplier->update([
                'supplier_name' => $request->supplier_name,
                'tin' => $request->tin,
                'address' => $request->address,
                'contact_person' => $request->contact_person,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
            ]);

            // Add this line:
            ActivityService::logSupplierUpdated($supplier->supplier_name, $changes);

            return response()->json(['success' => true, 'message' => 'Supplier updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating supplier: ' . $e->getMessage()], 500);
        }
    }

    public function toggleStatus(Supplier $supplier)
    {
        try {
            $oldStatus = $supplier->status;
            $newStatus = $supplier->status === 'active' ? 'inactive' : 'active';

            $supplier->update(['status' => $newStatus]);

            // Add this line:
            ActivityService::logSupplierStatusChanged($supplier->supplier_name, $oldStatus, $newStatus);

            $statusText = $newStatus === 'active' ? 'activated' : 'deactivated';
            return response()->json(['success' => true, 'message' => "Supplier {$statusText} successfully!"]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating supplier status: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            // Store supplier name before deletion
            $supplierName = $supplier->supplier_name;

            $supplier->delete();

            // Add this line:
            ActivityService::logSupplierDeleted($supplierName);

            return response()->json(['success' => true, 'message' => 'Supplier deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting supplier: ' . $e->getMessage()], 500);
        }
    }
}
