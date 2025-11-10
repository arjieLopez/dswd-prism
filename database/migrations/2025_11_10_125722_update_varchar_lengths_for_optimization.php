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
        // Update users table varchar lengths
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 25)->change();
            $table->string('middle_name', 25)->nullable()->change();
            $table->string('last_name', 25)->change();
            $table->string('email', 50)->change();
            $table->string('twofactor_code', 6)->nullable()->change();
        });

        // Update suppliers table varchar lengths
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('email', 50)->change();
        });

        // Update purchase_orders table varchar lengths
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('po_number', 50)->change();
            $table->string('mode_of_procurement', 50)->nullable()->change();
            $table->string('delivery_term', 50)->change();
            $table->string('payment_term', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert users table varchar lengths
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 50)->change();
            $table->string('middle_name', 50)->nullable()->change();
            $table->string('last_name', 50)->change();
            $table->string('email', 255)->change();
            $table->string('twofactor_code', 255)->nullable()->change();
        });

        // Revert suppliers table varchar lengths
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('email', 255)->change();
        });

        // Revert purchase_orders table varchar lengths
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('po_number', 20)->change();
            $table->string('mode_of_procurement', 255)->nullable()->change();
            $table->string('delivery_term', 100)->change();
            $table->string('payment_term', 100)->change();
        });
    }
};
