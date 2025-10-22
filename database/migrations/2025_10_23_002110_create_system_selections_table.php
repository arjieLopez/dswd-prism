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
        Schema::create('system_selections', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // e.g., 'metric_units', 'entity', 'fund_cluster', etc.
            $table->string('name');
            $table->timestamps();

            $table->index(['type', 'name']); // For efficient queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_selections');
    }
};
