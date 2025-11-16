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
        // Add soft deletes to system_selections table
        Schema::table('system_selections', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to units table
        Schema::table('units', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to designations table
        Schema::table('designations', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to offices table
        Schema::table('offices', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Add soft deletes to procurement_modes table
        Schema::table('procurement_modes', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove soft deletes from system_selections table
        Schema::table('system_selections', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // Remove soft deletes from units table
        Schema::table('units', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // Remove soft deletes from designations table
        Schema::table('designations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // Remove soft deletes from offices table
        Schema::table('offices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        // Remove soft deletes from procurement_modes table
        Schema::table('procurement_modes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
