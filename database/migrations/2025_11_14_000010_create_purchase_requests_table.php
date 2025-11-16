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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->string('pr_number', 50)->unique();
            $table->string('entity_name', 100);
            $table->string('fund_cluster', 50);
            $table->string('responsibility_center_code', 50);
            $table->date('date');
            $table->timestamp('submitted_at')->nullable();
            $table->string('stoc_property_no', 50)->nullable();
            $table->decimal('total', 15, 2);
            $table->string('delivery_period', 100);
            $table->text('delivery_address');
            $table->text('purpose');
            $table->string('scanned_copy')->nullable();
            $table->text('notes')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('status_id')->nullable()->constrained('statuses');
            $table->foreignId('procurement_mode_id')->nullable()->constrained('procurement_modes');
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
