<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop redundant VARCHAR columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['designation', 'office', 'role']);
        });

        // Drop redundant VARCHAR columns from purchase_requests table
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Drop redundant VARCHAR columns from suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Drop redundant VARCHAR columns from approvals table
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // Drop redundant VARCHAR columns from purchase_request_items table
        Schema::table('purchase_request_items', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back VARCHAR columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('designation')->nullable()->after('last_name');
            $table->string('office')->nullable()->after('designation');
            $table->string('role')->nullable()->after('office');
        });

        // Add back VARCHAR columns to purchase_requests table
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->string('status')->nullable()->after('remarks');
        });

        // Add back VARCHAR columns to suppliers table
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('status')->nullable()->after('email');
        });

        // Add back VARCHAR columns to approvals table
        Schema::table('approvals', function (Blueprint $table) {
            $table->string('status')->nullable()->after('remarks');
        });

        // Add back VARCHAR columns to purchase_request_items table
        Schema::table('purchase_request_items', function (Blueprint $table) {
            $table->string('unit')->nullable()->after('purchase_request_id');
        });
    }
};
