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
        // First, update statuses that are not in the new enum
        DB::table('purchase_requests')->where('status', 'po_generated')->update(['status' => 'approved']);
        DB::table('purchase_requests')->where('status', 'completed')->update(['status' => 'approved']);
        DB::table('purchase_requests')->where('status', 'failed')->update(['status' => 'rejected']);

        Schema::table('purchase_requests', function (Blueprint $table) {
            // Drop PO-related fields
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['po_number', 'po_generated_at', 'po_generated_by', 'supplier_id', 'mode_of_procurement', 'delivery_term', 'payment_term', 'date_of_delivery', 'completed_at']);
            // Drop redundant user fields
            $table->dropColumn(['requested_by_name', 'requested_by_designation', 'requested_by_signature', 'approved_by_name', 'approved_by_designation', 'approved_by_signature']);
            // Change status enum to PR-only statuses
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft')->change();
            // Add submitted_at if not exists
            if (!Schema::hasColumn('purchase_requests', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Reverse the changes - add back the columns
            $table->string('po_number')->nullable();
            $table->timestamp('po_generated_at')->nullable();
            $table->string('po_generated_by')->nullable();
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('set null');
            $table->string('mode_of_procurement')->nullable();
            $table->string('delivery_term')->nullable();
            $table->string('payment_term')->nullable();
            $table->date('date_of_delivery')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('requested_by_name');
            $table->string('requested_by_designation');
            $table->string('requested_by_signature')->nullable();
            $table->string('approved_by_name')->nullable();
            $table->string('approved_by_designation')->nullable();
            $table->string('approved_by_signature')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'po_generated', 'completed'])->default('draft')->change();
            $table->dropColumn('submitted_at');
        });
    }
};
