<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Services\ActivityService;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('supplier_name', 'like', "%{$search}%")
                    ->orWhere('tin', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('contact_number', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status') && in_array($request->input('status'), ['active', 'inactive'])) {
            $query->whereHas('status', function ($statusQuery) use ($request) {
                $statusQuery->where('name', $request->input('status'));
            });
        }

        // Date filter
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $query->whereBetween('created_at', [
                $dateFrom . ' 00:00:00',
                $dateTo . ' 23:59:59'
            ]);
        } elseif ($request->filled('date_from')) {
            $dateFrom = $request->input('date_from');
            $query->where('created_at', '>=', $dateFrom . ' 00:00:00');
        } elseif ($request->filled('date_to')) {
            $dateTo = $request->input('date_to');
            $query->where('created_at', '<=', $dateTo . ' 23:59:59');
        }

        $suppliers = $query->with('status')->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->query());

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('staff.suppliers', compact('suppliers', 'recentActivities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:100',
            'tin' => 'nullable|string|max:255',
            'address' => 'required|string|max:1000',
            'contact_person' => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:100',
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
            'supplier_name' => 'required|string|max:100',
            'tin' => 'nullable|string|max:255',
            'address' => 'required|string|max:1000',
            'contact_person' => 'required|string|max:100',
            'contact_number' => 'required|string|max:20',
            'email' => 'required|email|max:100',
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
            $newStatusName = $supplier->status === 'active' ? 'inactive' : 'active';

            // Find the new status ID
            $newStatus = \App\Models\Status::where('context', 'supplier')
                ->where('name', $newStatusName)
                ->first();

            if (!$newStatus) {
                return response()->json(['success' => false, 'message' => 'Status not found'], 404);
            }

            $supplier->update(['status_id' => $newStatus->id]);

            ActivityService::logSupplierStatusChanged($supplier->supplier_name, $oldStatus, $newStatusName);

            $statusText = $newStatusName === 'active' ? 'activated' : 'deactivated';
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
