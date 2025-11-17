<?php

namespace App\Http\Controllers;

use App\Models\RecommendingApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecommendingApprovalController extends Controller
{
    public function index($type)
    {
        try {
            if (!in_array($type, ['primary', 'secondary'])) {
                return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
            }

            $items = RecommendingApproval::with(['designation', 'offices'])
                ->where('type', $type)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return response()->json([
                'success' => true,
                'items' => $items->items(),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'total' => $items->total(),
                    'per_page' => $items->perPage(),
                    'has_more_pages' => $items->hasMorePages(),
                    'next_page_url' => $items->nextPageUrl(),
                    'prev_page_url' => $items->previousPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error loading items: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request, $type)
    {
        try {
            if (!in_array($type, ['primary', 'secondary'])) {
                return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
            }

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:25',
                'middle_name' => 'nullable|string|max:25',
                'last_name' => 'required|string|max:25',
                'designation_id' => 'required|exists:designations,id',
                'office_ids' => 'required|array',
                'office_ids.*' => 'exists:offices,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $item = RecommendingApproval::create([
                'type' => $type,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'designation_id' => $request->designation_id,
            ]);

            // Attach offices
            $item->offices()->attach($request->office_ids);

            // Load relationships for response
            $item->load(['designation', 'offices']);

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' Approver added successfully!',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error adding item: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $type, $id)
    {
        try {
            if (!in_array($type, ['primary', 'secondary'])) {
                return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
            }

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:25',
                'middle_name' => 'nullable|string|max:25',
                'last_name' => 'required|string|max:25',
                'designation_id' => 'required|exists:designations,id',
                'office_ids' => 'required|array',
                'office_ids.*' => 'exists:offices,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $item = RecommendingApproval::where('type', $type)->findOrFail($id);
            $item->update([
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'last_name' => $request->last_name,
                'designation_id' => $request->designation_id,
            ]);

            // Sync offices
            $item->offices()->sync($request->office_ids);

            // Load relationships for response
            $item->load(['designation', 'offices']);

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' Approver updated successfully!',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating item: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($type, $id)
    {
        try {
            if (!in_array($type, ['primary', 'secondary'])) {
                return response()->json(['success' => false, 'message' => 'Invalid type'], 400);
            }

            $item = RecommendingApproval::where('type', $type)->findOrFail($id);
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' Approver deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting item: ' . $e->getMessage()], 500);
        }
    }
}
