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
            $table->string('pr_number', 20)->unique();
            $table->string('entity_name', 100);
            $table->string('fund_cluster', 50);
            $table->string('office_section', 100);
            $table->string('responsibility_center_code', 50);
            $table->date('date');
            $table->string('stoc_property_no', 50)->nullable();
            $table->decimal('total', 15, 2);
            $table->string('delivery_period', 100);
            $table->text('delivery_address');
            $table->text('purpose');
            $table->string('requested_by_name', 100);
            $table->string('requested_by_designation', 100);
            $table->string('requested_by_signature', 255)->nullable(); // File path
            $table->string('approved_by_name', 100)->nullable();
            $table->string('approved_by_designation', 100)->nullable();
            $table->string('approved_by_signature', 255)->nullable(); // File path
            $table->string('scanned_copy', 255)->nullable(); // File path
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
