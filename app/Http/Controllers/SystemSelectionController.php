<?php

namespace App\Http\Controllers;

use App\Models\SystemSelection;
use App\Models\Unit;
use App\Models\Designation;
use App\Models\Office;
use App\Models\ProcurementMode;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Constants\PaginationConstants;
use App\Constants\ActivityConstants;

class SystemSelectionController extends Controller
{
    // Show the main management page (delegated to Blade view)
    public function index()
    {
        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(ActivityConstants::RECENT_ACTIVITY_LIMIT)
            ->get();

        // Get designations and offices for recommending approval dropdowns
        $designations = Designation::orderBy('name')->get();
        $offices = Office::orderBy('name')->get();

        return view('admin.system_selections', compact('recentActivities', 'designations', 'offices'));
    }

    // Map types to their corresponding models
    private function getModelForType($type)
    {
        $modelMap = [
            'metric_units' => Unit::class,
            'designation' => Designation::class,
            'office' => Office::class,
            'mode_of_procurement' => ProcurementMode::class,
        ];

        return $modelMap[$type] ?? null;
    }

    // Get items for a specific selection type
    public function list(Request $request, $type)
    {
        try {
            $perPage = 10; // Standard pagination size
            $page = $request->get('page', 1);

            // Check if this type uses a dedicated model
            $modelClass = $this->getModelForType($type);

            if ($modelClass) {
                // Use dedicated model table
                $query = $modelClass::orderBy('name');
            } else {
                // Use system_selections table
                $query = SystemSelection::where('type', $type)->orderBy('name');
            }

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
        $modelClass = $this->getModelForType($type);

        // Determine table name for unique validation
        $tableName = $modelClass ? (new $modelClass)->getTable() : 'system_selections';

        // Build validation rules
        if ($modelClass) {
            $uniqueRule = 'required|string|max:255|unique:' . $tableName . ',name';
        } else {
            $uniqueRule = 'required|string|max:255|unique:system_selections,name,NULL,id,type,' . $type;
        }

        $validator = Validator::make($request->all(), [
            'name' => $uniqueRule,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($modelClass) {
                // Create in dedicated model table
                $item = $modelClass::create([
                    'name' => trim($request->name),
                ]);
            } else {
                // Create in system_selections table
                $item = SystemSelection::create([
                    'type' => $type,
                    'name' => trim($request->name),
                ]);
            }

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
        $modelClass = $this->getModelForType($type);

        // Determine table name for unique validation
        $tableName = $modelClass ? (new $modelClass)->getTable() : 'system_selections';

        // Build validation rules
        if ($modelClass) {
            $uniqueRule = 'required|string|max:255|unique:' . $tableName . ',name,' . $id;
        } else {
            $uniqueRule = 'required|string|max:255|unique:system_selections,name,' . $id . ',id,type,' . $type;
        }

        $validator = Validator::make($request->all(), [
            'name' => $uniqueRule,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($modelClass) {
                // Update in dedicated model table
                $item = $modelClass::findOrFail($id);
            } else {
                // Update in system_selections table
                $item = SystemSelection::where('type', $type)->findOrFail($id);
            }

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
            $modelClass = $this->getModelForType($type);

            if ($modelClass) {
                // Delete from dedicated model table
                $item = $modelClass::findOrFail($id);
            } else {
                // Delete from system_selections table
                $item = SystemSelection::where('type', $type)->findOrFail($id);
            }

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
