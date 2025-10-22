<?php

namespace App\Http\Controllers;

use App\Models\SystemSelection;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SystemSelectionController extends Controller
{
    // Show the main management page (delegated to Blade view)
    public function index()
    {
        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        // In future: pass all selection types to the view
        return view('admin.system_selections', compact('recentActivities'));
    }

    // Get items for a specific selection type
    public function list(Request $request, $type)
    {
        try {
            $perPage = 10; // Standard pagination size
            $page = $request->get('page', 1);

            $query = SystemSelection::where('type', $type)->orderBy('name');
            $items = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'items' => $items->items(),
                'pagination' => [
                    'current_page' => $items->currentPage(),
                    'last_page' => $items->lastPage(),
                    'per_page' => $items->perPage(),
                    'total' => $items->total(),
                    'has_more_pages' => $items->hasMorePages(),
                    'next_page_url' => $items->nextPageUrl(),
                    'prev_page_url' => $items->previousPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load items.'
            ], 500);
        }
    }

    // Store a new value
    public function store(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:system_selections,name,NULL,id,type,' . $type,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $item = SystemSelection::create([
                'type' => $type,
                'name' => trim($request->name),
            ]);

            // Log activity
            ActivityService::log(
                'system_selection_created',
                "Added '{$item->name}' to {$type}",
                null,
                null,
                ['type' => $type, 'item_name' => $item->name]
            );

            return response()->json([
                'success' => true,
                'message' => 'Item added successfully.',
                'item' => [
                    'id' => $item->id,
                    'name' => $item->name,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item.'
            ], 500);
        }
    }

    // Update a value
    public function update(Request $request, $type, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:system_selections,name,' . $id . ',id,type,' . $type,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $item = SystemSelection::where('type', $type)->findOrFail($id);
            $oldName = $item->name;

            $item->update([
                'name' => trim($request->name),
            ]);

            // Log activity
            ActivityService::log(
                'system_selection_updated',
                "Changed '{$oldName}' to '{$item->name}' in {$type}",
                null,
                null,
                ['type' => $type, 'old_name' => $oldName, 'new_name' => $item->name]
            );

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully.',
                'item' => [
                    'id' => $item->id,
                    'name' => $item->name,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update item.'
            ], 500);
        }
    }

    // Delete a value
    public function destroy($type, $id)
    {
        try {
            $item = SystemSelection::where('type', $type)->findOrFail($id);
            $itemName = $item->name;

            $item->delete();

            // Log activity
            ActivityService::log(
                'system_selection_deleted',
                "Removed '{$itemName}' from {$type}",
                null,
                null,
                ['type' => $type, 'item_name' => $itemName]
            );

            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item.'
            ], 500);
        }
    }
}
