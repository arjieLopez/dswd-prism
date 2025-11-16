<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate User data
        $this->migrateUserData();

        // Migrate PurchaseRequest data
        $this->migratePurchaseRequestData();

        // Migrate PurchaseOrder data
        $this->migratePurchaseOrderData();

        // Migrate Supplier data
        $this->migrateSupplierData();

        // Migrate PurchaseRequestItem data
        $this->migratePurchaseRequestItemData();

        // Migrate Approval data
        $this->migrateApprovalData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it converts data
        // In a production environment, you would need backups
    }

    private function migrateUserData()
    {
        // Update users table to use foreign keys
        $users = DB::table('users')->get();

        foreach ($users as $user) {
            $designationId = null;
            $officeId = null;
            $roleId = null;

            if ($user->designation) {
                $designation = DB::table('designations')->where('name', $user->designation)->first();
                if (!$designation) {
                    // Create missing designation
                    $designationId = DB::table('designations')->insertGetId([
                        'name' => $user->designation,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    $designationId = $designation->id;
                }
            }

            if ($user->office) {
                $office = DB::table('offices')->where('name', $user->office)->first();
                if (!$office) {
                    // Create missing office
                    $officeId = DB::table('offices')->insertGetId([
                        'name' => $user->office,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    $officeId = $office->id;
                }
            }

            if ($user->role) {
                $role = DB::table('roles')->where('name', $user->role)->first();
                $roleId = $role ? $role->id : null;
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update([
                    'designation_id' => $designationId,
                    'office_id' => $officeId,
                    'role_id' => $roleId,
                ]);
        }
    }

    private function migratePurchaseRequestData()
    {
        // Update purchase_requests table to use foreign keys
        $purchaseRequests = DB::table('purchase_requests')->get();

        foreach ($purchaseRequests as $pr) {
            $statusId = null;

            if ($pr->status) {
                $status = DB::table('statuses')
                    ->where('context', 'purchase_request')
                    ->where('name', $pr->status)
                    ->first();
                $statusId = $status ? $status->id : null;
            }

            DB::table('purchase_requests')
                ->where('id', $pr->id)
                ->update([
                    'status_id' => $statusId,
                ]);
        }
    }

    private function migratePurchaseOrderData()
    {
        // Update purchase_orders table to use foreign keys
        $purchaseOrders = DB::table('purchase_orders')->get();

        foreach ($purchaseOrders as $po) {
            $statusId = null;

            // Default to 'generated' status for existing POs
            $status = DB::table('statuses')
                ->where('context', 'purchase_order')
                ->where('name', 'generated')
                ->first();
            $statusId = $status ? $status->id : null;

            DB::table('purchase_orders')
                ->where('id', $po->id)
                ->update([
                    'status_id' => $statusId,
                ]);
        }
    }

    private function migrateSupplierData()
    {
        // Update suppliers table to use foreign keys
        $suppliers = DB::table('suppliers')->get();

        foreach ($suppliers as $supplier) {
            $statusId = null;

            if ($supplier->status) {
                $status = DB::table('statuses')
                    ->where('context', 'supplier')
                    ->where('name', $supplier->status)
                    ->first();
                $statusId = $status ? $status->id : null;
            }

            DB::table('suppliers')
                ->where('id', $supplier->id)
                ->update([
                    'status_id' => $statusId,
                ]);
        }
    }

    private function migratePurchaseRequestItemData()
    {
        // Update purchase_request_items table to use foreign keys
        $items = DB::table('purchase_request_items')->get();

        foreach ($items as $item) {
            $unitId = null;

            if ($item->unit) {
                $unit = DB::table('units')->where('name', $item->unit)->first();
                if (!$unit) {
                    // Create missing unit
                    $unitId = DB::table('units')->insertGetId([
                        'name' => $item->unit,
                        'abbreviation' => substr($item->unit, 0, 3), // Simple abbreviation
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    $unitId = $unit->id;
                }
            }

            DB::table('purchase_request_items')
                ->where('id', $item->id)
                ->update([
                    'unit_id' => $unitId,
                ]);
        }
    }

    private function migrateApprovalData()
    {
        // Update approvals table to use foreign keys
        $approvals = DB::table('approvals')->get();

        foreach ($approvals as $approval) {
            $statusId = null;

            if ($approval->status) {
                $status = DB::table('statuses')
                    ->where('context', 'approval')
                    ->where('name', $approval->status)
                    ->first();
                $statusId = $status ? $status->id : null;
            }

            DB::table('approvals')
                ->where('id', $approval->id)
                ->update([
                    'status_id' => $statusId,
                ]);
        }
    }
};
