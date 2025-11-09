<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class MigrateExistingDataToNormalizedStructure extends Migration
{
    public function up(): void
    {
        // Migrate PO data from purchase_requests to purchase_orders
        $prs = DB::table('purchase_requests')->whereNotNull('po_number')->get();
        foreach ($prs as $pr) {
            DB::table('purchase_orders')->insert([
                'purchase_request_id' => $pr->id,
                'supplier_id' => $pr->supplier_id,
                'po_number' => $pr->po_number,
                'mode_of_procurement' => $pr->mode_of_procurement,
                'delivery_term' => $pr->delivery_term,
                'payment_term' => $pr->payment_term,
                'date_of_delivery' => $pr->date_of_delivery,
                'generated_at' => $pr->po_generated_at,
                'generated_by' => $pr->po_generated_by,
                'completed_at' => $pr->completed_at,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Migrate approvals
        foreach ($prs as $pr) {
            if ($pr->approved_by_name) {
                $approver = DB::table('users')->where('first_name', 'like', '%' . explode(' ', $pr->approved_by_name)[0] . '%')->first();
                if ($approver) {
                    DB::table('approvals')->insert([
                        'purchase_request_id' => $pr->id,
                        'approver_id' => $approver->id,
                        'status' => $pr->status == 'approved' ? 'approved' : 'rejected',
                        'approved_at' => $pr->approved_at ?? now(),
                        'remarks' => $pr->remarks,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Migrate signatures
        foreach ($prs as $pr) {
            if ($pr->requested_by_signature) {
                DB::table('signatures')->insert([
                    'signable_type' => 'App\\Models\\PurchaseRequest',
                    'signable_id' => $pr->id,
                    'signature_path' => $pr->requested_by_signature,
                    'signed_at' => $pr->submitted_at ?? $pr->created_at,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            if ($pr->approved_by_signature) {
                DB::table('signatures')->insert([
                    'signable_type' => 'App\\Models\\Approval',
                    'signable_id' => DB::table('approvals')->where('purchase_request_id', $pr->id)->value('id'),
                    'signature_path' => $pr->approved_by_signature,
                    'signed_at' => $pr->approved_at ?? now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Migrate common attributes (status, timestamps)
        $tables = ['users', 'purchase_requests', 'purchase_request_items', 'suppliers', 'user_activities', 'uploaded_documents', 'po_documents', 'system_selections'];
        foreach ($tables as $table) {
            $records = DB::table($table)->get();
            foreach ($records as $record) {
                if (isset($record->status)) {
                    DB::table('common_attributes')->insert([
                        'entity_type' => $table,
                        'entity_id' => $record->id,
                        'attribute_key' => 'status',
                        'attribute_value' => $record->status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                if (isset($record->created_at)) {
                    DB::table('common_attributes')->insert([
                        'entity_type' => $table,
                        'entity_id' => $record->id,
                        'attribute_key' => 'created_at',
                        'attribute_value' => $record->created_at,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                if (isset($record->updated_at)) {
                    DB::table('common_attributes')->insert([
                        'entity_type' => $table,
                        'entity_id' => $record->id,
                        'attribute_key' => 'updated_at',
                        'attribute_value' => $record->updated_at,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Data migration is one-way; manual restoration required
    }
