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
            $table->foreignId('status_id')->nullable()->constrained('statuses')->after('purpose');
            $table->foreignId('procurement_mode_id')->nullable()->constrained('procurement_modes')->after('status_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropForeign(['procurement_mode_id']);
            $table->dropColumn(['status_id', 'procurement_mode_id']);
        });
    }
};
