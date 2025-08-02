<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;

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
            Supplier::create([
                'supplier_name' => $request->supplier_name,
                'tin' => $request->tin,
                'address' => $request->address,
                'contact_person' => $request->contact_person,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
                'status' => 'active'
            ]);

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
            $supplier->update([
                'supplier_name' => $request->supplier_name,
                'tin' => $request->tin,
                'address' => $request->address,
                'contact_person' => $request->contact_person,
                'contact_number' => $request->contact_number,
                'email' => $request->email,
            ]);

            return response()->json(['success' => true, 'message' => 'Supplier updated successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating supplier: ' . $e->getMessage()], 500);
        }
    }

    public function toggleStatus(Supplier $supplier)
    {
        try {
            $newStatus = $supplier->status === 'active' ? 'inactive' : 'active';
            $supplier->update(['status' => $newStatus]);

            $statusText = $newStatus === 'active' ? 'activated' : 'deactivated';
            return response()->json(['success' => true, 'message' => "Supplier {$statusText} successfully!"]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating supplier status: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
            return response()->json(['success' => true, 'message' => 'Supplier deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting supplier: ' . $e->getMessage()], 500);
        }
    }
}
