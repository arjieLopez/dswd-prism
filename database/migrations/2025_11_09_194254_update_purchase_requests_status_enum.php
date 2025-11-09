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
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'po_generated', 'completed'])->default('draft')->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft')->change();
        });
    }
};
