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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('context', 50); // purchase_request, supplier, approval, purchase_order
            $table->string('name', 50); // draft, pending, approved, etc.
            $table->string('display_name', 50); // Human-readable name
            $table->string('color', 50); // CSS color class
            $table->timestamps();
            $table->unique(['context', 'name']); // Ensure unique status per context
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
