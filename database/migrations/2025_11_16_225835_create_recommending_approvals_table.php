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
        Schema::create('recommending_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'primary' or 'secondary'
            $table->string('first_name', 25);
            $table->string('middle_name', 25)->nullable();
            $table->string('last_name', 25);
            $table->foreignId('designation_id')->nullable()->constrained('designations');
            $table->timestamps();
            $table->softDeletes();
        });

        // Pivot table for recommending_approval and offices (many-to-many)
        Schema::create('recommending_approval_office', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recommending_approval_id')->constrained('recommending_approvals')->onDelete('cascade');
            $table->foreignId('office_id')->constrained('offices')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommending_approval_office');
        Schema::dropIfExists('recommending_approvals');
    }
};
