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
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('pr_number')->unique();
            $table->string('entity_name');
            $table->string('fund_cluster');
            $table->string('office_section');
            $table->string('responsibility_center_code');
            $table->date('date');
            $table->string('stoc_property_no')->nullable();
            $table->string('unit');
            $table->text('item_description');
            $table->integer('quantity');
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->decimal('total', 15, 2);
            $table->string('delivery_period');
            $table->text('delivery_address');
            $table->text('purpose');
            $table->string('requested_by_name');
            $table->string('requested_by_designation');
            $table->string('requested_by_signature')->nullable(); // File path
            $table->string('approved_by_name')->nullable();
            $table->string('approved_by_designation')->nullable();
            $table->string('approved_by_signature')->nullable(); // File path
            $table->string('scanned_copy')->nullable(); // File path for uploaded PR
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'failed'])->default('draft');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
