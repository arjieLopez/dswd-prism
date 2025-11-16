<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Truncate long values (skip pr_number due to unique constraint)
        DB::table('purchase_requests')->whereRaw('LENGTH(entity_name) > 100')->update(['entity_name' => DB::raw('LEFT(entity_name, 100)')]);
        DB::table('purchase_requests')->whereRaw('LENGTH(fund_cluster) > 50')->update(['fund_cluster' => DB::raw('LEFT(fund_cluster, 50)')]);
        DB::table('purchase_requests')->whereRaw('LENGTH(office_section) > 100')->update(['office_section' => DB::raw('LEFT(office_section, 100)')]);
        DB::table('purchase_requests')->whereRaw('LENGTH(responsibility_center_code) > 50')->update(['responsibility_center_code' => DB::raw('LEFT(responsibility_center_code, 50)')]);
        DB::table('purchase_requests')->whereRaw('LENGTH(stoc_property_no) > 50')->update(['stoc_property_no' => DB::raw('LEFT(stoc_property_no, 50)')]);
        DB::table('purchase_requests')->whereRaw('LENGTH(delivery_period) > 100')->update(['delivery_period' => DB::raw('LEFT(delivery_period, 100)')]);

        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->string('pr_number', 50)->change(); // Increased to accommodate existing data
            $table->string('entity_name', 100)->change();
            $table->string('fund_cluster', 50)->change();
            $table->string('office_section', 100)->change();
            $table->string('responsibility_center_code', 50)->change();
            $table->string('stoc_property_no', 50)->nullable()->change();
            $table->string('delivery_period', 100)->change();
            $table->string('scanned_copy', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->string('pr_number', 255)->change();
            $table->string('entity_name', 255)->change();
            $table->string('fund_cluster', 255)->change();
            $table->string('office_section', 255)->change();
            $table->string('responsibility_center_code', 255)->change();
            $table->string('stoc_property_no', 255)->change();
            $table->string('delivery_period', 255)->change();
            $table->string('scanned_copy', 255)->change();
        });
    }
};
